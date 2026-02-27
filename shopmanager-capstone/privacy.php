<?php
/**
 * privacy.php – Privacy Policy page
 */
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Privacy Policy';
include __DIR__ . '/includes/header.php';
?>

<div class="text-center mb-5">
    <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--clr-primary-lt);"></i>
    <h2 class="fw-bold mt-2 mb-1" style="color: var(--clr-primary);">Privacy Policy</h2>
    <small class="text-clr-muted">Last updated: February 2026</small>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
                    <i class="bi bi-1-circle me-2"></i>Data Collection
                </h5>
                <p class="text-clr-muted">DukaDash collects the minimum information needed to manage your inventory:</p>
                <ul class=\"text-clr-muted ps-4\">
                    <li><strong>Account Info:</strong> Name and email for authentication.</li>
                    <li><strong>Preferences:</strong> Theme (Light/Dark) and Language settings.</li>
                    <li><strong>Audit Logs:</strong> We track changes made to products for security.</li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4 text-center">
                <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">Contact Support</h5>
                <p class="text-clr-muted mb-0">
                    Questions? Contact us at: 
                    <a href="mailto:dukadash@gmail.com" class="fw-semibold">dukadash@gmail.com</a>
                </p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>