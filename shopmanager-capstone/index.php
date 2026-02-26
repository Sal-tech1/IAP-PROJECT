<?php
// ── Temporary debug mode ──
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once __DIR__ . '/backend/includes/bootstrap.php';
// ... rest of the file



/**
 * index.php  –  Public landing page / entry point
 * Logged-in users are redirected straight to the dashboard.
 */
require_once __DIR__ . '/backend/includes/bootstrap.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Welcome';
include __DIR__ . '/backend/includes/header.php';
?>

<!-- ── Hero section ───────────────────────────────────────────────────────── -->
<div class="row justify-content-center text-center mt-4">
    <div class="col-lg-8">

        <!-- Icon -->
        <div class="mb-4">
            <i class="bi bi-shop" style="font-size: 4rem; color: var(--clr-primary-lt);"></i>
        </div>

        <!-- Headline -->
        <h1 class="fw-bold mb-3" style="color: var(--clr-primary); font-size: 2.4rem;">
            Welcome to <span style="color: var(--clr-primary-lt);">DukaDash</span>
        </h1>

        <!-- Sub-headline -->
        <p class="lead text-clr-muted mb-4" style="max-width: 560px; margin: 0 auto 1.5rem;">
            A responsive product-management application built with PHP &amp; MySQL.
            Create, read, update, and delete products — all from one clean dashboard.
        </p>

        <!-- CTA buttons -->
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="login.php" class="btn btn-primary-custom btn-lg">
                <i class="bi bi-box-arrow-in-right me-2"></i>Log In
            </a>
            <a href="register.php" class="btn btn-outline-secondary btn-lg">
                <i class="bi bi-person-plus me-2"></i>Register
            </a>
        </div>
    </div>
</div>

<!-- ── Feature cards ──────────────────────────────────────────────────────── -->
<div class="row g-4 mt-5">

    <div class="col-md-4">
        <div class="card text-center p-4 h-100">
            <div class="mb-3">
                <i class="bi bi-database-gear" style="font-size: 2.2rem; color: var(--clr-primary-lt);"></i>
            </div>
            <h5 class="fw-semibold" style="color: var(--clr-primary);">CRUD Operations</h5>
            <p class="text-clr-muted small">
                Full create, read, update, and delete functionality for products,
                with an audit trail that logs every change.
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center p-4 h-100">
            <div class="mb-3">
                <i class="bi bi-phone-landscape" style="font-size: 2.2rem; color: var(--clr-primary-lt);"></i>
            </div>
            <h5 class="fw-semibold" style="color: var(--clr-primary);">Responsive Design</h5>
            <p class="text-clr-muted small">
                Mobile-first layout powered by Bootstrap 5. Looks great on phones,
                tablets, and desktops alike.
            </p>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card text-center p-4 h-100">
            <div class="mb-3">
                <i class="bi bi-shield-lock" style="font-size: 2.2rem; color: var(--clr-primary-lt);"></i>
            </div>
            <h5 class="fw-semibold" style="color: var(--clr-primary);">Secure & Private</h5>
            <p class="text-clr-muted small">
                Passwords hashed with bcrypt, SQL injection prevented via PDO,
                and cookie consent respected at every step.
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/backend/includes/footer.php'; ?>
