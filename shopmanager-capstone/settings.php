<?php
/**
 * settings.php – User preferences (theme, language).
 */
require_once __DIR__ . '/includes/bootstrap.php';
requireAuth();

$pdo  = getDB();
$user = getCurrentUser();
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Invalid request.';
    } else {
        $theme = in_array($_POST['theme'] ?? '', ['light', 'dark']) ? $_POST['theme'] : 'light';
        $lang  = in_array($_POST['lang']  ?? '', ['en', 'fr', 'es'])  ? $_POST['lang']  : 'en';

        $stmt = $pdo->prepare('UPDATE users SET theme_pref = ?, lang_pref = ? WHERE id = ?');
        $stmt->execute([$theme, $lang, $_SESSION['user_id']]);

        if (($_COOKIE['consent'] ?? '') === 'true') {
            setcookie('theme', $theme, time() + 365 * 86400, '/', '', false, true);
        }
        flashSet('success', 'Preferences saved!');
        header('Location: settings.php');
        exit;
    }
}

$pageTitle = 'Settings';
include __DIR__ . '/includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h4 class="mb-4" style="color: var(--clr-primary);">Account Settings</h4>
                <form method="POST" action="settings.php">
                    <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />
                    <div class="mb-3">
                        <label class="form-label fw-bold">Theme Preference</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="theme" id="themeLight" value="light" <?= $user['theme_pref'] === 'light' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="themeLight">Light</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="theme" id="themeDark" value="dark" <?= $user['theme_pref'] === 'dark' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="themeDark">Dark</label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary-custom">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>