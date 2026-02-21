<?php
/**
 * privacy.php  –  Privacy Policy page
 */
require_once __DIR__ . '/includes/bootstrap.php';

$pageTitle = 'Privacy Policy';
include __DIR__ . '/includes/header.php';
?>

<!-- ── Hero ───────────────────────────────────────────────────────────────── -->
<div class="text-center mb-5">
    <i class="bi bi-shield-check" style="font-size: 3rem; color: var(--clr-primary-lt);"></i>
    <h2 class="fw-bold mt-2 mb-1" style="color: var(--clr-primary);">Privacy Policy</h2>
    <small class="text-clr-muted">Last updated: January 2026</small>
</div>

<div class="row justify-content-center">
<div class="col-lg-8">

<!-- ── 1. What we collect ─────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
            <i class="bi bi-1-circle me-2"></i>What Information Do We Collect?
        </h5>
        <p class="text-clr-muted">We collect only the minimum information necessary to operate ShopManager:</p>
        <ul class="text-clr-muted ps-4">
            <li><strong>Name and email address</strong> – required for account creation and login.</li>
            <li><strong>Password</strong> – stored as a bcrypt hash; we never see or store your plain-text password.</li>
            <li><strong>Display preferences</strong> – your chosen theme (light/dark) and language.</li>
        </ul>
        <p class="text-clr-muted mb-0">We do <strong>not</strong> collect browsing history, location data, device fingerprints, or any other tracking information.</p>
    </div>
</div>

<!-- ── 2. How we use it ───────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
            <i class="bi bi-2-circle me-2"></i>How Do We Use Your Information?
        </h5>
        <ul class="text-clr-muted ps-4 mb-0">
            <li>To authenticate you and maintain your login session.</li>
            <li>To personalise your interface (theme and language).</li>
            <li>To log product changes for accountability (audit trail).</li>
        </ul>
    </div>
</div>

<!-- ── 3. Cookies ─────────────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
            <i class="bi bi-3-circle me-2"></i>How Do We Use Cookies?
        </h5>
        <p class="text-clr-muted">
            We use cookies <strong>only</strong> for the following purposes. No third-party or
            tracking cookies are ever set.
        </p>
        <div class="table-responsive">
            <table class="table table-sm table-custom">
                <thead>
                    <tr>
                        <th>Cookie</th>
                        <th>Purpose</th>
                        <th>Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>PHPSESSID</code></td>
                        <td>Maintains your authenticated session</td>
                        <td>Browser session (deleted on close)</td>
                    </tr>
                    <tr>
                        <td><code>consent</code></td>
                        <td>Records whether you have accepted cookies</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td><code>theme</code></td>
                        <td>Remembers your display theme preference</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td><code>lang</code></td>
                        <td>Remembers your language preference</td>
                        <td>1 year</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-clr-muted mb-0">
            All preference cookies are set with <code>SameSite=Strict</code>, <code>HttpOnly</code> (where applicable),
            and <code>Secure</code> (on HTTPS). You can decline cookies at any time via the banner —
            only the session cookie will remain active.
        </p>
    </div>
</div>

<!-- ── 4. Data security ───────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
            <i class="bi bi-4-circle me-2"></i>How Do We Protect Your Data?
        </h5>
        <ul class="text-clr-muted ps-4 mb-0">
            <li>Passwords are hashed with <strong>bcrypt</strong> before storage.</li>
            <li>All database queries use <strong>PDO prepared statements</strong> to prevent SQL injection.</li>
            <li>All user input is sanitised server-side to prevent XSS.</li>
            <li>Data in transit is protected via <strong>HTTPS (TLS)</strong>.</li>
            <li>The database account used by the application follows the <strong>principle of least privilege</strong>.</li>
        </ul>
    </div>
</div>

<!-- ── 5. Your rights ─────────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-4">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
            <i class="bi bi-5-circle me-2"></i>Your Rights
        </h5>
        <p class="text-clr-muted">You have the right to:</p>
        <ul class="text-clr-muted ps-4 mb-0">
            <li><strong>Access</strong> – view the personal data we hold about you (via Settings).</li>
            <li><strong>Rectification</strong> – update your name or email (via Settings).</li>
            <li><strong>Erasure</strong> – request deletion of your account and associated data by contacting the administrator.</li>
            <li><strong>Withdraw consent</strong> – decline cookies at any time via the cookie banner or Settings page.</li>
        </ul>
    </div>
</div>

<!-- ── 6. Contact ─────────────────────────────────────────────────────────── -->
<div class="card shadow-sm">
    <div class="card-body p-4">
        <h5 class="fw-semibold mb-3" style="color: var(--clr-primary);">
            <i class="bi bi-6-circle me-2"></i>Contact Us
        </h5>
        <p class="text-clr-muted mb-0">
            If you have questions about this privacy policy or how we handle your data,
            please contact the project team at:
            <a href="mailto:team@example.com" class="fw-semibold">team@example.com</a>
        </p>
    </div>
</div>

</div><!-- /col -->
</div><!-- /row -->

<?php include __DIR__ . '/includes/footer.php'; ?>
