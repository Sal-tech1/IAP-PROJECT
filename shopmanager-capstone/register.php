<?php
/**
 * register.php  –  New user registration page
 */
require_once __DIR__ . '/backend/includes/bootstrap.php';

// Already logged in? No need to register again.
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

// Process registration (returns error string or redirects on success)
$error = handleRegister();

$pageTitle = 'Register';
include __DIR__ . '/backend/includes/header.php';
?>

<div class="auth-wrapper">
    <div class="auth-card card shadow">

        <!-- Header -->
        <div class="text-center mb-3">
            <i class="bi bi-person-plus" style="font-size: 3rem; color: var(--clr-primary-lt);"></i>
            <h4 class="card-title mt-2 mb-0">Create an Account</h4>
            <small class="text-clr-muted">Fill in the details below to get started</small>
        </div>

        <!-- Server-side error -->
        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <?= esc($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Registration form -->
        <form id="registerForm" method="POST" action="register.php" novalidate>
            <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />

            <!-- Full Name -->
            <div class="mb-3">
                <label for="regName" class="form-label fw-semibold small">Full Name</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-person" style="color: var(--clr-primary-lt);"></i>
                    </span>
                    <input type="text" id="regName" name="name"
                           class="form-control"
                           placeholder="Geoffrey Kagombe"
                           value="<?= esc($_POST['name'] ?? '') ?>"
                           autocomplete="name"
                           required />
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="regEmail" class="form-label fw-semibold small">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-envelope" style="color: var(--clr-primary-lt);"></i>
                    </span>
                    <input type="email" id="regEmail" name="email"
                           class="form-control"
                           placeholder="kagombe@example.com"
                           value="<?= esc($_POST['email'] ?? '') ?>"
                           autocomplete="email"
                           required />
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="regPassword" class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock" style="color: var(--clr-primary-lt);"></i>
                    </span>
                    <input type="password" id="regPassword" name="password"
                           class="form-control"
                           placeholder="At least 6 characters"
                           autocomplete="new-password"
                           required />
                </div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-4">
                <label for="regConfirm" class="form-label fw-semibold small">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-lock-fill" style="color: var(--clr-primary-lt);"></i>
                    </span>
                    <input type="password" id="regConfirm" name="password_confirm"
                           class="form-control"
                           placeholder="Re-enter password"
                           autocomplete="new-password"
                           required />
                </div>
            </div>

            <!-- Submit -->
            <button type="submit" class="btn btn-primary-custom w-100">
                <i class="bi bi-person-plus me-2"></i>Register
            </button>
        </form>

        <hr class="my-3" />

        <p class="text-center text-clr-muted small mb-0">
            Already have an account?
            <a href="login.php" class="fw-semibold">Log in here</a>
        </p>
    </div>
</div>

<?php include __DIR__ . '/backend/includes/footer.php'; ?>
