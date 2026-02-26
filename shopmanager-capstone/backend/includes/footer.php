<?php
/**
 * includes/footer.php
 * Shared bottom-of-page HTML: closing tags, Bootstrap JS, app scripts.
 *
 * Usage at the bottom of every page:
 *   include __DIR__ . '/includes/footer.php';
 */

if (!defined('APP_RUNNING')) exit;
?>
</main><!-- /main -->

<!-- ── Footer ─────────────────────────────────────────────────────────────── -->
<footer class="mt-auto py-3 text-center text-muted small"
        style="background: rgba(0, 0, 0, 0.25); backdrop-filter: blur(8px);">
    <div class="container">
        <span class="text-white-50">
            &copy; <?= date('Y') ?> DukaDash &mdash; IAP Project
            &nbsp;|&nbsp;
            <a href="privacy.php" class="text-warning text-decoration-none">Privacy Policy</a>
        </span>
    </div>
</footer>

<!-- ── Bootstrap 5 JS (Popper included) ──────────────────────────────────── -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

<!-- ── App scripts ────────────────────────────────────────────────────────── -->
<script src="frontend/js/cookies.js"></script>
<script src="frontend/js/validate.js"></script>

<!-- ── Page-specific script (optional) ───────────────────────────────────── -->
<?php if (isset($extraScript)): ?>
<script>
<?= $extraScript ?>
</script>
<?php endif; ?>

</body>
</html>
