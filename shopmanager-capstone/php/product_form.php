<?php
/**
 * php/product_form.php
 * Reusable HTML form for creating AND editing a product.
 * Now upgraded to support multipart/form-data for local file uploads.
 */
if (!defined('APP_RUNNING')) exit;

$isEdit  = ($formAction === 'edit');
$heading = $isEdit ? 'Edit Product' : 'Add New Product';

$submitUrl = 'products.php?action=' . esc($formAction);
if ($isEdit && isset($id)) {
    $submitUrl .= '&id=' . (int)$id;
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-<?= $isEdit ? 'pencil' : 'plus-circle' ?> me-2"></i>
            <?= esc($heading) ?>
        </h2>
        <small class="text-clr-muted">
            <?= $isEdit ? 'Update the product details below' : 'Fill in the fields to create a new product' ?>
        </small>
    </div>
    <a href="products.php" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Inventory
    </a>
</div>

<?php if (!empty($error)): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <?= esc($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-4">
        <form id="productForm" method="POST" action="<?= $submitUrl ?>" enctype="multipart/form-data" novalidate>

            <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />

            <div class="row g-4">
                <div class="col-lg-8">
                    <label for="productName" class="form-label fw-semibold small">Product Name <span class="text-danger">*</span></label>
                    <input type="text" id="productName" name="name"
                           class="form-control"
                           placeholder="e.g. Wireless Headphones"
                           value="<?= esc($product['name'] ?? '') ?>"
                           required />
                </div>

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

                <div class="col-lg-4">
                    <label for="productPrice" class="form-label fw-semibold small">Price (KES) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-cash-coin" style="color:var(--clr-primary-lt);"></i></span>
                        <input type="number" id="productPrice" name="price"
                               class="form-control"
                               step="0.01" min="0.01"
                               value="<?= esc($product['price'] ?? '') ?>"
                               required />
                    </div>
                </div>

                <div class="col-lg-4">
                    <label for="productStock" class="form-label fw-semibold small">Stock Quantity <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-layers" style="color:var(--clr-primary-lt);"></i></span>
                        <input type="number" id="productStock" name="stock"
                               class="form-control"
                               step="1" min="0"
                               value="<?= esc($product['stock'] ?? '') ?>"
                               required />
                    </div>
                </div>

                <div class="col-lg-4">
                    <label for="productImage" class="form-label fw-semibold small">Product Image</label>
                    <input type="file" id="productImage" name="image_file"
                           class="form-control"
                           accept="image/jpeg, image/png, image/webp" />
                    <?php if ($isEdit && !empty($product['image_url'])): ?>
                        <div class="form-text">Current image exists. Upload a new one to replace it.</div>
                    <?php else: ?>
                        <div class="form-text">Accepted formats: JPG, PNG, WEBP.</div>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <label for="productDesc" class="form-label fw-semibold small">Description</label>
                    <textarea id="productDesc" name="description"
                              class="form-control"
                              rows="3"><?= esc($product['description'] ?? '') ?></textarea>
                </div>
            </div>

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