<?php
/**
 * products.php  –  Full CRUD operations for the DukaDash Inventory.
 * * This file handles:
 * - Listing all products with pagination and search.
 * - Routing to the Create form and saving new products.
 * - Routing to the Edit form and updating existing products.
 * - Securely deleting products via a POST request.
 */
require_once __DIR__ . '/includes/bootstrap.php';
requireAuth();

$pdo    = getDB();
$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$error  = null;

$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();

// =============================================================================
// HELPER: PROCESS IMAGE UPLOAD
// =============================================================================
function processImageUpload(&$error) {
    if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // No file uploaded, return null
    }

    if ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
        $error = 'An error occurred during file upload.';
        return null;
    }

    // Validate file type
    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $fileMimeType = mime_content_type($_FILES['image_file']['tmp_name']);
    
    if (!in_array($fileMimeType, $allowedMimeTypes)) {
        $error = 'Invalid image format. Only JPG, PNG, and WEBP are allowed.';
        return null;
    }

    // Validate file size (e.g., max 2MB)
    if ($_FILES['image_file']['size'] > 2 * 1024 * 1024) {
        $error = 'Image file is too large. Maximum size is 2MB.';
        return null;
    }

    // Generate a unique filename and move it
    $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('prod_', true) . '.' . strtolower($ext);
    $uploadDir = __DIR__ . '/assets/uploads/';
    $destination = $uploadDir . $newFileName;

    if (move_uploaded_file($_FILES['image_file']['tmp_name'], $destination)) {
        return 'assets/uploads/' . $newFileName;
    } else {
        $error = 'Failed to save the uploaded image to the server.';
        return null;
    }
}

// =============================================================================
// ACTION: CREATE
// =============================================================================
if ($action === 'create') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf()) { 
            $error = 'Security token expired. Please try again.'; 
        } else {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = $_POST['price']            ?? '';
            $stock       = $_POST['stock']            ?? '';
            $categoryId  = (int)($_POST['category_id'] ?? 0);
            
            // Process the uploaded image
            $imageUrl = processImageUpload($error);

            if (strlen($name) < 3 || strlen($name) > 200) {
                $error = 'Product name must be between 3 and 200 characters.';
            } elseif (!is_numeric($price) || (float)$price <= 0) {
                $error = 'Price must be a positive number.';
            } elseif (!is_numeric($stock) || (int)$stock < 0) {
                $error = 'Stock must be 0 or a positive whole number.';
            } elseif ($categoryId === 0) {
                $error = 'Please select a category.';
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare(
                'INSERT INTO products (name, description, price, stock, category_id, image_url, created_by)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                htmlspecialchars(strip_tags($name)),
                htmlspecialchars(strip_tags($description)),
                number_format((float)$price, 2, '.', ''),
                (int)$stock,
                $categoryId,
                $imageUrl,
                $_SESSION['user_id']
            ]);

            $newId = $pdo->lastInsertId();
            logAction('CREATE', 'product', $newId, ['name' => $name, 'price' => $price]);

            flashSet('success', 'Product "' . esc($name) . '" created successfully.');
            header('Location: products.php');
            exit;
        }
    }

    $pageTitle  = 'Add Product';
    $formAction = 'create';
    $product    = $_POST;   
    
    include __DIR__ . '/includes/header.php';
    include __DIR__ . '/php/product_form.php';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// =============================================================================
// ACTION: EDIT / UPDATE
// =============================================================================
if ($action === 'edit') {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        flashSet('danger', 'Product not found.');
        header('Location: products.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrf()) { 
            $error = 'Security token expired. Please try again.'; 
        } else {
            $name        = trim($_POST['name']        ?? '');
            $description = trim($_POST['description'] ?? '');
            $price       = $_POST['price']            ?? '';
            $stock       = $_POST['stock']            ?? '';
            $categoryId  = (int)($_POST['category_id'] ?? 0);
            
            // Retain the existing image URL unless a new valid file is uploaded
            $imageUrl = $product['image_url']; 
            $uploadedImagePath = processImageUpload($error);
            if ($uploadedImagePath) {
                $imageUrl = $uploadedImagePath; 
            }

            if (strlen($name) < 3 || strlen($name) > 200) {
                $error = 'Product name must be between 3 and 200 characters.';
            } elseif (!is_numeric($price) || (float)$price <= 0) {
                $error = 'Price must be a positive number.';
            } elseif (!is_numeric($stock) || (int)$stock < 0) {
                $error = 'Stock must be 0 or a positive whole number.';
            } elseif ($categoryId === 0) {
                $error = 'Please select a category.';
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare(
                'UPDATE products SET name=?, description=?, price=?, stock=?, category_id=?, image_url=?
                 WHERE id=?'
            );
            $stmt->execute([
                htmlspecialchars(strip_tags($name)),
                htmlspecialchars(strip_tags($description)),
                number_format((float)$price, 2, '.', ''),
                (int)$stock,
                $categoryId,
                $imageUrl,
                $id
            ]);

            logAction('UPDATE', 'product', $id, ['name' => $name, 'price' => $price]);

            flashSet('success', 'Product "' . esc($name) . '" updated successfully.');
            header('Location: products.php');
            exit;
        }

        $product = $_POST;
        $product['id'] = $id;
    }

    $pageTitle  = 'Edit Product';
    $formAction = 'edit';
    
    include __DIR__ . '/includes/header.php';
    include __DIR__ . '/php/product_form.php';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// =============================================================================
// ACTION: DELETE
// =============================================================================
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        flashSet('danger', 'Security token expired. Action denied.');
        header('Location: products.php');
        exit;
    }

    $stmt = $pdo->prepare('SELECT name, image_url FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $productToDelete = $stmt->fetch();

    if ($productToDelete) {
        $pdo->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
        
        // Optional: Delete the physical file from the server to save space
        if (!empty($productToDelete['image_url']) && strpos($productToDelete['image_url'], 'assets/uploads/') === 0) {
            $filePath = __DIR__ . '/' . $productToDelete['image_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        logAction('DELETE', 'product', $id, ['name' => $productToDelete['name']]);
        flashSet('success', 'Product "' . esc($productToDelete['name']) . '" has been deleted.');
    } else {
        flashSet('danger', 'Product not found or already deleted.');
    }

    header('Location: products.php');
    exit;
}

// =============================================================================
// DEFAULT: LIST VIEW
// =============================================================================
$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $countRow = $pdo->prepare('SELECT COUNT(*) FROM products WHERE name LIKE ?');
    $countRow->execute(['%' . $search . '%']);
} else {
    $countRow = $pdo->query('SELECT COUNT(*) FROM products');
}
$totalRows = (int)$countRow->fetchColumn();

$paging = paginate($totalRows, 10);

$query = 'SELECT p.*, c.name AS category_name
          FROM products p
          LEFT JOIN categories c ON c.id = p.category_id';
$params = [];

if ($search !== '') {
    $query  .= ' WHERE p.name LIKE ?';
    $params[] = '%' . $search . '%';
}
$query .= ' ORDER BY p.created_at DESC LIMIT ? OFFSET ?';
$params[] = $paging['per_page'];
$params[] = $paging['offset'];

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'Inventory';
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-box-seam me-2"></i>Product Inventory
        </h2>
        <small class="text-clr-muted"><?= (int)$totalRows ?> item(s) in database</small>
    </div>
    <a href="products.php?action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i>Add New Product
    </a>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width:70px;" class="text-center">Image</th>
                    <th>Product Name</th>
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
                    <td class="text-center" style="width:70px;">
                        <?php if (!empty($p['image_url'])): ?>
                            <img src="<?= esc($p['image_url']) ?>" alt="Image" class="product-thumb" />
                        <?php else: ?>
                            <span class="thumb-placeholder"><i class="bi bi-image text-clr-muted"></i></span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <strong><?= esc($p['name']) ?></strong>
                    </td>
                    <td>
                        <span class="badge"><?= esc($p['category_name']) ?></span>
                    </td>
                    <td class="text-end fw-semibold" style="color:var(--clr-primary-lt);">
                        KES <?= number_format((float)$p['price'], 2) ?>
                    </td>
                    <td class="text-end"><?= (int)$p['stock'] ?></td>
                    <td class="text-end">
                        <a href="products.php?action=edit&amp;id=<?= (int)$p['id'] ?>" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-product-id="<?= (int)$p['id'] ?>" data-product-name="<?= esc($p['name']) ?>" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: var(--clr-card-bg); color: var(--clr-text);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color:var(--clr-danger);">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Are you sure you want to delete <strong id="deleteProductName">this product</strong>?
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="products.php">
                    <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />
                    <input type="hidden" name="action" value="delete" />
                    <input type="hidden" id="deleteProductId" name="id" value="" />
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Product
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$extraScript = <<<JS
document.getElementById('deleteModal').addEventListener('show.bs.modal', function (e) {
    var btn  = e.relatedTarget;
    var name = btn.getAttribute('data-product-name');
    var id   = btn.getAttribute('data-product-id');
    
    document.getElementById('deleteProductName').textContent = name;
    document.getElementById('deleteProductId').value = id;
    document.getElementById('deleteForm').setAttribute('action', 'products.php?action=delete&id=' + id);
});
JS;

include __DIR__ . '/includes/footer.php';
?>