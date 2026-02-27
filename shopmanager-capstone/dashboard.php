<?php
/**
 * dashboard.php  –  Main dashboard with KPI cards and recent audit log.
 */
require_once __DIR__ . '/includes/bootstrap.php';
requireAuth();

$pdo = getDB();

$productCount  = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$userCount     = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$recentAudit   = getRecentAudit(10);

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </h2>
        <small class="text-clr-muted">Overview of DukaDash Inventory</small>
    </div>
    <a href="products.php?action=create" class="btn btn-primary-custom">Add Product</a>
</div>

<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card kpi-card p-4">
            <div class="text-clr-muted small text-uppercase fw-semibold">Products</div>
            <div class="kpi-value"><?= (int)$productCount ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card kpi-card p-4" style="border-left-color:#27ae60;">
            <div class="text-clr-muted small text-uppercase fw-semibold">Categories</div>
            <div class="kpi-value" style="color:#27ae60;"><?= (int)$categoryCount ?></div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card kpi-card p-4" style="border-left-color:#f39c12;">
            <div class="text-clr-muted small text-uppercase fw-semibold">Users</div>
            <div class="kpi-value" style="color:#f39c12;"><?= (int)$userCount ?></div>
        </div>
    </div>
</div>

<h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">Recent Activity</h5>
<div class="card table-responsive">
    <table class="table table-custom mb-0">
        <thead>
            <tr>
                <th>Action</th>
                <th>Entity</th>
                <th>By</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentAudit as $log): ?>
            <tr>
                <td><span class="badge-action badge-<?= strtolower($log['action']) ?>"><?= esc($log['action']) ?></span></td>
                <td class="text-capitalize"><?= esc($log['entity']) ?> (ID: <?= (int)$log['entity_id'] ?>)</td>
                <td><?= esc($log['user_name']) ?></td>
                <td class="text-clr-muted small"><?= date('d M Y, H:i', strtotime($log['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>