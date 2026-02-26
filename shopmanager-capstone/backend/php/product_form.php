<?php
/**
 * php/product_form.php
 * Reusable form partial for creating AND editing a product.
 *
 * Expected variables (set by products.php before including):
 *   $formAction   – 'create' | 'edit'
 *   $product      – associative array with current values (empty array for create)
 *   $categories   – array of category rows from DB
 *   $error        – validation error string or null
 *   $id           – product ID (only when editing)
 */
if (!defined('APP_RUNNING')) exit;

$isEdit  = ($formAction === 'edit');
$heading = $isEdit ? 'Edit Product' : 'Add New Product';
?>

<!-- ── Page heading ───────────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?> me-2"></i>
            <?= esc($heading) ?>
        </h2>
        <small class="text-clr-muted">
            <?= $isEdit ? 'Update the details below' : 'Fill in the fields to create a new product' ?>
        </small>
    </div>
    <a href="products.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Products
    </a>
</div>

<!-- ── Error alert ────────────────────────────────────────────────────────── -->
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <?= esc($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── Form card ──────────────────────────────────────────────────────────── -->
<div class="card shadow-sm">
    <div class="card-body p-4">
        <form id="productForm" method="POST"
              action="products.php?action=<?= esc($formAction) ?><?= $isEdit ? '&id=' . (int)$id : '' ?>"
              novalidate>

            <!-- CSRF -->
            <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />

            <div class="row g-4">

                <!-- Product Name -->
                <div class="col-lg-8">
                    <label for="productName" class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                    <input type="text" id="productName" name="name"
                           class="form-control"
                           placeholder="e.g. Wireless Headphones"
                           value="<?= esc($product['name'] ?? '') ?>"
                           required />
                </div>

                <!-- Category -->
                <div class="col-lg-4">
                    <label for="productCategory" class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                    <select id="productCategory" name="category_id" class="form-select" required>
                        <option value="">— Select a category —</option>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['id'] ?>"
                                <?= (int)($product['category_id'] ?? 0) === (int)$cat['id'] ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Price -->
                <div class="col-lg-4">
                    <label for="productPrice" class="form-label fw-semibold small">Price ($) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-cash-coin" style="color:var(--clr-primary-lt);"></i></span>
                        <input type="number" id="productPrice" name="price"
                               class="form-control"
                               step="0.01" min="0.01"
                               placeholder="29.99"
                               value="<?= esc($product['price'] ?? '') ?>"
                               required />
                    </div>
                </div>

                <!-- Stock -->
                <div class="col-lg-4">
                    <label for="productStock" class="form-label fw-semibold small">Stock Quantity <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-layers" style="color:var(--clr-primary-lt);"></i></span>
                        <input type="number" id="productStock" name="stock"
                               class="form-control"
                               step="1" min="0"
                               placeholder="50"
                               value="<?= esc($product['stock'] ?? '') ?>"
                               required />
                    </div>
                </div>

                <!-- Description (spans full width) -->
                <div class="col-12">
                    <label for="productDesc" class="form-label fw-semibold small">Description</label>
                    <textarea id="productDesc" name="description"
                              class="form-control"
                              rows="3"
                              placeholder="A short description of the product…">
<?= esc($product['description'] ?? '') ?></textarea>
                </div>

                <!-- Image URL -->
                <div class="col-12">
                    <label for="productImage" class="form-label fw-semibold small">
                        Product Image URL
                        <span class="text-clr-muted fw-normal">(optional)</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-image" style="color:var(--clr-primary-lt);"></i>
                        </span>
                        <input type="url" id="productImage" name="image_url"
                               class="form-control"
                               placeholder="https://example.com/image.jpg"
                               value="<?= esc($product['image_url'] ?? '') ?>" />
                    </div>
                    <div class="form-text text-clr-muted">
                        Paste a direct link to a product image (JPG, PNG, WebP). Leave blank for no image.
                    </div>

                    <!-- Live preview -->
                    <div id="imagePreviewWrap" class="mt-3" style="display:<?= !empty($product['image_url']) ? 'block' : 'none' ?>;">
                        <p class="form-label fw-semibold small mb-1">Preview</p>
                        <img id="imagePreview"
                             src="<?= esc($product['image_url'] ?? '') ?>"
                             alt="Product preview"
                             class="product-thumb-preview"
                             onerror="document.getElementById('imagePreviewWrap').style.display='none';" />
                    </div>
                </div>
            </div>

            <!-- Submit row -->
            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="products.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary-custom">
                    <i class="bi bi-<?= $isEdit ? 'check-lg' : 'plus-lg' ?> me-1"></i>
                    <?= $isEdit ? 'Save Changes' : 'Create Product' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Live image URL preview script
$extraScript = <<<JS
(function () {
    var urlInput   = document.getElementById('productImage');
    var previewImg = document.getElementById('imagePreview');
    var previewWrap = document.getElementById('imagePreviewWrap');

    if (!urlInput || !previewImg || !previewWrap) return;

    urlInput.addEventListener('input', function () {
        var val = this.value.trim();
        if (val === '') {
            previewWrap.style.display = 'none';
            previewImg.src = '';
            return;
        }
        previewImg.src = val;
        previewImg.onload  = function () { previewWrap.style.display = 'block'; };
        previewImg.onerror = function () { previewWrap.style.display = 'none'; };
    });
})();
JS;
?>
