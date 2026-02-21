<?php
/**
 * dashboard.php  –  Main dashboard with KPI cards and recent audit log.
 */
require_once __DIR__ . '/includes/bootstrap.php';
requireAuth();   // redirect to login if not authenticated

$pdo = getDB();

// ── Fetch KPI counts ──────────────────────────────────────────────────────
$productCount  = $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$categoryCount = $pdo->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$userCount     = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();

// ── Fetch recent audit entries (last 10) ──────────────────────────────────
$recentAudit = getRecentAudit(10);

$pageTitle = 'Dashboard';
include __DIR__ . '/includes/header.php';
?>

<!-- ── Page heading ───────────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </h2>
        <small class="text-clr-muted">Overview of your shop</small>
    </div>
    <a href="products.php?action=create" class="btn btn-primary-custom">
        <i class="bi bi-plus-lg me-1"></i>Add Product
    </a>
</div>

<!-- ── KPI Cards ──────────────────────────────────────────────────────────── -->
<div class="row g-4 mb-5">

    <!-- Products -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card kpi-card p-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:#eaf4fc;">
                    <i class="bi bi-box-seam" style="font-size:1.6rem;color:var(--clr-primary-lt);"></i>
                </div>
                <div>
                    <div class="text-clr-muted small text-uppercase fw-semibold">Products</div>
                    <div class="kpi-value"><?= (int)$productCount ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card kpi-card p-4" style="border-left-color:#27ae60;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:#eafaf1;">
                    <i class="bi bi-tags" style="font-size:1.6rem;color:#27ae60;"></i>
                </div>
                <div>
                    <div class="text-clr-muted small text-uppercase fw-semibold">Categories</div>
                    <div class="kpi-value" style="color:#27ae60;"><?= (int)$categoryCount ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Users -->
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="card kpi-card p-4" style="border-left-color:#f39c12;">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:52px;height:52px;background:#fef9e7;">
                    <i class="bi bi-people" style="font-size:1.6rem;color:#f39c12;"></i>
                </div>
                <div>
                    <div class="text-clr-muted small text-uppercase fw-semibold">Users</div>
                    <div class="kpi-value" style="color:#f39c12;"><?= (int)$userCount ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Recent Audit Log ───────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-semibold mb-0" style="color: var(--clr-primary);">
        <i class="bi bi-clock-history me-2"></i>Recent Activity
    </h5>
</div>

<div class="table-responsive">
    <table class="table table-custom">
        <thead>
            <tr>
                <th>#</th>
                <th>Action</th>
                <th>Entity</th>
                <th>ID</th>
                <th>By</th>
                <th>Timestamp</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recentAudit)): ?>
            <tr>
                <td colspan="6" class="text-center text-clr-muted py-4">
                    <i class="bi bi-inbox me-2"></i>No activity yet.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($recentAudit as $i => $log): ?>
            <tr>
                <td class="text-clr-muted small"><?= $i + 1 ?></td>
                <td>
                    <span class="badge-action badge-<?= strtolower($log['action']) ?>">
                        <?= esc($log['action']) ?>
                    </span>
                </td>
                <td class="text-capitalize"><?= esc($log['entity']) ?></td>
                <td class="text-clr-muted"><?= (int)$log['entity_id'] ?></td>
                <td>
                    <i class="bi bi-person me-1"></i>
                    <?= esc($log['user_name']) ?>
                </td>
                <td class="text-clr-muted small">
                    <?= date('d M Y, H:i', strtotime($log['created_at'])) ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
