<?php
/**
 * products.php  –  Full CRUD for products.
 *
 * GET  /products.php              → paginated product list
 * GET  /products.php?action=create → blank create form
 * GET  /products.php?action=edit&id=N → edit form pre-filled
 * POST /products.php?action=create   → create a product
 * POST /products.php?action=update&id=N → update a product
 * POST /products.php?action=delete&id=N → delete a product (confirm first)
 */
require_once __DIR__ . '/includes/bootstrap.php';
requireAuth();

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$error  = null;

// ── Fetch all categories (used in forms) ──────────────────────────────────
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

// =============================================================================
// ACTION: CREATE
// =============================================================================
if ($action === 'create') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf()) { $error = 'Invalid request.'; }
        else {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = $_POST['price']            ?? '';
            $stock       = $_POST['stock']            ?? '';
            $categoryId  = (int)($_POST['category_id'] ?? 0);

            // Server-side validation
            if (strlen($name) < 3 || strlen($name) > 200)
                $error = 'Product name must be between 3 and 200 characters.';
            elseif (!is_numeric($price) || (float)$price <= 0)
                $error = 'Price must be a positive number.';
            elseif (!is_numeric($stock) || (int)$stock < 0)
                $error = 'Stock must be 0 or a positive whole number.';
            elseif ($categoryId === 0)
                $error = 'Please select a category.';
        }

        if (!$error) {
            $stmt = $pdo->prepare(
                'INSERT INTO products (name, description, price, stock, category_id, created_by)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                htmlspecialchars(strip_tags($name)),
                htmlspecialchars(strip_tags($description)),
                number_format((float)$price, 2, '.', ''),
                (int)$stock,
                $categoryId,
                $_SESSION['user_id']
            ]);

            $newId = $pdo->lastInsertId();
            logAction('CREATE', 'product', $newId, ['name' => $name, 'price' => $price]);

            flashSet('success', 'Product "' . esc($name) . '" created successfully.');
            header('Location: products.php');
            exit;
        }
    }

    // ── Render CREATE form ──────────────────────────────────────────────────
    $pageTitle = 'Add Product';
    include __DIR__ . '/includes/header.php';
    $formAction = 'create';
    $product    = $_POST;   // re-fill on validation error
    include __DIR__ . '/php/product_form.php';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// =============================================================================
// ACTION: EDIT / UPDATE
// =============================================================================
if ($action === 'edit') {

    // Fetch existing product
    $stmt    = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        flashSet('danger', 'Product not found.');
        header('Location: products.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf()) { $error = 'Invalid request.'; }
        else {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = $_POST['price']            ?? '';
            $stock       = $_POST['stock']            ?? '';
            $categoryId  = (int)($_POST['category_id'] ?? 0);

            if (strlen($name) < 3 || strlen($name) > 200)
                $error = 'Product name must be between 3 and 200 characters.';
            elseif (!is_numeric($price) || (float)$price <= 0)
                $error = 'Price must be a positive number.';
            elseif (!is_numeric($stock) || (int)$stock < 0)
                $error = 'Stock must be 0 or a positive whole number.';
            elseif ($categoryId === 0)
                $error = 'Please select a category.';
        }

        if (!$error) {
            $stmt = $pdo->prepare(
                'UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?
                 WHERE id=?'
            );
            $stmt->execute([
                htmlspecialchars(strip_tags($name)),
                htmlspecialchars(strip_tags($description)),
                number_format((float)$price, 2, '.', ''),
                (int)$stock,
                $categoryId,
                $id
            ]);

            logAction('UPDATE', 'product', $id, ['name' => $name, 'price' => $price]);

            flashSet('success', 'Product "' . esc($name) . '" updated successfully.');
            header('Location: products.php');
            exit;
        }

        // On error, use POST data to re-fill the form
        $product = $_POST;
        $product['id'] = $id;
    }

    $pageTitle = 'Edit Product';
    include __DIR__ . '/includes/header.php';
    $formAction = 'edit';
    include __DIR__ . '/php/product_form.php';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// =============================================================================
// ACTION: DELETE
// =============================================================================
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flashSet('danger', 'Invalid request.');
        header('Location: products.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT name FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        logAction('DELETE', 'product', $id, ['name' => $product['name']]);
        flashSet('success', 'Product "' . esc($product['name']) . '" deleted.');
    } else {
        flashSet('danger', 'Product not found.');
    }

    header('Location: products.php');
    exit;
}

// =============================================================================
// DEFAULT: LIST
// =============================================================================

// Search filter
$search = trim($_GET['search'] ?? '');

// Count total (with optional search)
if ($search !== '') {
    $countRow = $pdo->prepare('SELECT COUNT(*) FROM products WHERE name LIKE ?');
    $countRow->execute(['%' . $search . '%']);
} else {
    $countRow = $pdo->query('SELECT COUNT(*) FROM products');
}
$totalRows = (int)$countRow->fetchColumn();

// Pagination
$paging = paginate($totalRows, 10);

// Fetch page of products
$query = 'SELECT p.*, c.name AS category_name
          FROM products p
          JOIN categories c ON c.id = p.category_id';
$params = [];

if ($search !== '') {
    $query  .= ' WHERE p.name LIKE ?';
    $params[] = '%' . $search . '%';
}
$query .= ' ORDER BY p.created_at DESC LIMIT ? OFFSET ?';
$params[] = $paging['per_page'];
$params[] = $paging['offset'];

$stmt     = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// ── Render LIST page ────────────────────────────────────────────────────────
$pageTitle = 'Products';
include __DIR__ . '/includes/header.php';
?>

<!-- ── Page heading ───────────────────────────────────────────────────────── -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-box-seam me-2"></i>Products
        </h2>
        <small class="text-clr-muted"><?= (int)$totalRows ?> product<?= $totalRows !== 1 ? 's' : '' ?> total</small>
    </div>
    <a href="products.php?action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
</div>

<!-- ── Search bar ─────────────────────────────────────────────────────────── -->
<form method="GET" action="products.php" class="mb-4">
    <div class="input-group" style="max-width:420px;">
        <span class="input-group-text bg-white">
            <i class="bi bi-search" style="color:var(--clr-primary-lt);"></i>
        </span>
        <input type="text" name="search" class="form-control"
               placeholder="Search products…"
               value="<?= esc($search) ?>" />
        <button type="submit" class="btn btn-primary-custom">Search</button>
        <?php if ($search !== ''): ?>
            <a href="products.php" class="btn btn-outline-secondary">Clear</a>
        <?php endif; ?>
    </div>
</form>

<!-- ── Product table ──────────────────────────────────────────────────────── -->
<div class="table-responsive">
    <table class="table table-custom">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Category</th>
                <th class="text-end">Price</th>
                <th class="text-end">Stock</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
            <tr>
                <td colspan="6" class="text-center text-clr-muted py-4">
                    <i class="bi bi-inbox me-2"></i>No products found.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($products as $p): ?>
            <tr>
                <td class="text-clr-muted small"><?= (int)$p['id'] ?></td>
                <td>
                    <strong><?= esc($p['name']) ?></strong>
                    <?php if (!empty($p['description'])): ?>
                        <br /><small class="text-clr-muted"><?= esc(mb_stsubstr($p['description'], 0, 60)) ?>…</small>
                    <?php endif; ?>
                </td>
                <td>
                    <span class="badge bg-light text-dark border">
                        <?= esc($p['category_name']) ?>
                    </span>
                </td>
                <td class="text-end fw-semibold" style="color:var(--clr-primary-lt);">
                    $<?= number_format((float)$p['price'], 2) ?>
                </td>
                <td class="text-end"><?= (int)$p['stock'] ?></td>
                <td class="text-end">
                    <!-- Edit button -->
                    <a href="products.php?action=edit&amp;id=<?= (int)$p['id'] ?>"
                       class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <!-- Delete trigger (opens modal) -->
                    <button type="button"
                            class="btn btn-sm btn-outline-danger"
                            data-bs-toggle="modal" data-bs-target="#deleteModal"
                            data-product-id="<?= (int)$p['id'] ?>"
                            data-product-name="<?= esc($p['name']) ?>"
                            title="Delete">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ── Pagination ─────────────────────────────────────────────────────────── -->
<?php if ($paging['total_pages'] > 1): ?>
<nav aria-label="Products pagination">
    <ul class="pagination justify-content-center">
        <!-- Previous -->
        <li class="page-item <?= $paging['page'] <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="products.php?page=<?= $paging['page'] - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                <i class="bi bi-chevron-left"></i>
            </a>
        </li>

        <?php for ($i = 1; $i <= $paging['total_pages']; $i++): ?>
        <li class="page-item <?= $i === $paging['page'] ? 'active' : '' ?>">
            <a class="page-link"
               href="products.php?page={$i}<?= $search ? '&search=' . urlencode($search) : '' ?>">
                <?= $i ?>
            </a>
        </li>
        <?php endfor; ?>

        <!-- Next -->
        <li class="page-item <?= $paging['page'] >= $paging['total_pages'] ? 'disabled' : '' ?>">
            <a class="page-link" href="products.php?page=<?= $paging['page'] + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
                <i class="bi bi-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>
<?php endif; ?>

<!-- ── Delete confirmation modal ──────────────────────────────────────────── -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-label="Delete confirmation">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:var(--clr-danger);">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-clr-muted">
                    Are you sure you want to delete
                    <strong id="deleteProductName">this product</strong>?
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="products.php">
                    <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />
                    <input type="hidden" id="deleteProductId" name="id" value="" />
                    <input type="hidden" name="action" value="delete" />
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Inline script to wire the delete modal
$extraScript = <<<JS
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    var btn  = e.relatedTarget;
    var name = btn.getAttribute('data-product-name');
    var id   = btn.getAttribute('data-product-id');
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteProductId').value = id;
    // Update the form action to include the id in the URL for clean-URL support
    document.getElementById('deleteForm').setAttribute('action', 'products.php?action=delete&id=' + id);
});
JS;

include __DIR__ . '/includes/footer.php';
?>
