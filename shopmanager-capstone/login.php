<?php
/**
 * login.php – User login page
 */
require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = handleLogin();
$pageTitle = 'Log In';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card card shadow">
        <div class="text-center mb-3">
            <i class="bi bi-person-circle" style="font-size: 3rem; color: var(--clr-primary-lt);"></i>
            <h4 class="card-title mt-2 mb-0">Welcome Back</h4>
            <small class="text-clr-muted">Sign in to DukaDash</small>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="login.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />
            <div class="mb-3">
                <label for="loginEmail" class="form-label fw-semibold small">Email Address</label>
                <input type="email" id="loginEmail" name="email" class="form-control" placeholder="you@example.com" value="<?= esc($_POST['email'] ?? '') ?>" required />
            </div>
            <div class="mb-4">
                <label for="loginPassword" class="form-label fw-semibold small">Password</label>
                <input type="password" id="loginPassword" name="password" class="form-control" placeholder="••••••••" required />
            </div>
            <button type="submit" class="btn btn-primary-custom w-100">Log In</button>
        </form>
        <hr class="my-3" />
        <p class="text-center text-clr-muted small mb-0">
            Need an account? <a href="register.php" class="fw-semibold">Register here</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>