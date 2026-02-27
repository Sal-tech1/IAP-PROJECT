<?php
/**
 * includes/footer.php
 * Shared bottom-of-page HTML: closing tags, Bootstrap JS, app scripts.
 */

if (!defined('APP_RUNNING')) exit;
?>
</main>

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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        crossorigin="anonymous"></script>

<script src="js/cookies.js"></script>
<script src="js/validate.js"></script>

<?php if (isset($extraScript)): ?>
<script>
<?= $extraScript ?>
</script>
<?php endif; ?>

</body>
</html>