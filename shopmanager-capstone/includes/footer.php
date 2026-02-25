<?php
/**
 * includes/footer.php
 * Shared bottom-of-page HTML: closing tags, Bootstrap JS, app scripts.
 */

if (!defined('APP_RUNNING')) exit;
?>
</main>

<footer class="mt-auto py-3 text-center text-muted small"
        style="background: linear-gradient(135deg, #1a5276, #2e86c1);">
    <div class="container">
        <span class="text-white-50">
            &copy; <?= date('Y') ?> ShopManager &mdash; ICS 2203 Capstone Project
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
