<?php
/**
 * register.php – New user registration page
 */
require_once __DIR__ . '/includes/bootstrap.php';

// Already logged in? No need to register again.
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Process registration (returns error string or redirects on success)
$error = handleRegister();

$pageTitle = 'Register';
include __DIR__ . '/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card card shadow">

        <div class="text-center mb-3">
            <i class="bi bi-person-plus" style="font-size: 3rem; color: var(--clr-primary-lt);"></i>
            <h4 class="card-title mt-2 mb-0">Create an Account</h4>
            <small class="text-clr-muted">Join DukaDash today</small>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="register.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />

            <div class="mb-3">
                <label for="regName" class="form-label fw-semibold small">Full Name</label>
                <input type="text" id="regName" name="name" class="form-control" placeholder="Your Name" value="<?= esc($_POST['name'] ?? '') ?>" required />
            </div>

            <div class="mb-3">
                <label for="regEmail" class="form-label fw-semibold small">Email Address</label>
                <input type="email" id="regEmail" name="email" class="form-control" placeholder="email@example.com" value="<?= esc($_POST['email'] ?? '') ?>" required />
            </div>

            <div class="mb-3">
                <label for="regPassword" class="form-label fw-semibold small">Password</label>
                <input type="password" id="regPassword" name="password" class="form-control" placeholder="At least 6 characters" required />
            </div>

            <div class="mb-4">
                <label for="regConfirm" class="form-label fw-semibold small">Confirm Password</label>
                <input type="password" id="regConfirm" name="password_confirm" class="form-control" placeholder="Re-enter password" required />
            </div>

            <button type="submit" class="btn btn-primary-custom w-100">Register</button>
        </form>

        <hr class="my-3" />
        <p class="text-center text-clr-muted small mb-0">
            Already have an account? <a href="login.php" class="fw-semibold">Log in here</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>