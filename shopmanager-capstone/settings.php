<?php
/**
 * settings.php  –  User preferences (theme, language).
 *
 * GET  → render the settings page
 * POST → save preferences to DB + cookies (if consent given)
 */
require_once __DIR__ . '/backend/includes/bootstrap.php';
requireAuth();

$pdo  = getDB();
$user = getCurrentUser();
$error = null;

// ── Handle POST (save preferences) ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf()) {
        $error = 'Invalid request.';
    } else {
        $theme = in_array($_POST['theme'] ?? '', ['light', 'dark']) ? $_POST['theme'] : 'light';
        $lang  = in_array($_POST['lang']  ?? '', ['en', 'fr', 'es'])  ? $_POST['lang']  : 'en';

        // Persist in DB
        $stmt = $pdo->prepare('UPDATE users SET theme_pref = ?, lang_pref = ? WHERE id = ?');
        $stmt->execute([$theme, $lang, $_SESSION['user_id']]);

        // Persist in cookies ONLY if consent was given
        if (($_COOKIE['consent'] ?? '') === 'true') {
            $cookieOpts = [
                'expires'  => time() + 365 * 86400,
                'path'     => '/',
                'secure'   => ($_SERVER['HTTPS'] ?? 'off') === 'on',
                'httponly' => false,           // JS needs to read theme cookie
                'samesite' => 'Strict',
            ];
            setcookie('theme', $theme, $cookieOpts);
            setcookie('lang',  $lang,  $cookieOpts);
        }

        flashSet('success', 'Preferences saved successfully.');
        header('Location: settings.php');
        exit;
    }
}

// Re-fetch user so the form always shows current values
$user = getCurrentUser();

$pageTitle = 'Settings';
include __DIR__ . '/backend/includes/header.php';
?>

<!-- ── Page heading ───────────────────────────────────────────────────────── -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0 fw-bold" style="color: var(--clr-primary);">
            <i class="bi bi-gear me-2"></i>Settings
        </h2>
        <small class="text-clr-muted">Personalise your experience</small>
    </div>
</div>

<!-- ── Cookie consent notice ──────────────────────────────────────────────── -->
<?php if (($_COOKIE['consent'] ?? '') !== 'true'): ?>
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="bi bi-info-circle me-2"></i>
    You have not accepted cookies yet. Preferences will be saved to your account but
    <strong>not</strong> stored in browser cookies until you accept via the cookie banner.
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── Error alert ────────────────────────────────────────────────────────── -->
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="bi bi-exclamation-triangle me-2"></i>
    <?= esc($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- ── Preferences card ───────────────────────────────────────────────────── -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="settings.php">
                    <input type="hidden" name="csrf_token" value="<?= esc($_SESSION['csrf_token']) ?>" />

                    <!-- Theme -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Display Theme</label>
                        <div class="row g-3">
                            <!-- Light option -->
                            <div class="col-6">
                                <label class="d-block border rounded p-3 text-center
                                    <?= $user['theme_pref'] === 'light' ? 'border-primary shadow-sm' : '' ?>"
                                    style="cursor:pointer; border-width:<?= $user['theme_pref'] === 'light' ? '2px' : '1px' ?>;">
                                    <input type="radio" name="theme" value="light"
                                           class="d-none"
                                           <?= $user['theme_pref'] === 'light' ? 'checked' : '' ?> />
                                    <div class="rounded mb-2" style="height:36px;background:linear-gradient(135deg,#f0f4f8,#ffffff);"></div>
                                    <small class="fw-semibold">Light</small>
                                </label>
                            </div>
                            <!-- Dark option -->
                            <div class="col-6">
                                <label class="d-block border rounded p-3 text-center
                                    <?= $user['theme_pref'] === 'dark' ? 'border-primary shadow-sm' : '' ?>"
                                    style="cursor:pointer; border-width:<?= $user['theme_pref'] === 'dark' ? '2px' : '1px' ?>;">
                                    <input type="radio" name="theme" value="dark"
                                           class="d-none"
                                           <?= $user['theme_pref'] === 'dark' ? 'checked' : '' ?> />
                                    <div class="rounded mb-2" style="height:36px;background:linear-gradient(135deg,#1c2833,#2c3e50);"></div>
                                    <small class="fw-semibold">Dark</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Language -->
                    <div class="mb-4">
                        <label for="settingsLang" class="form-label fw-semibold">Language</label>
                        <select id="settingsLang" name="lang" class="form-select" style="max-width:220px;">
                            <option value="en" <?= $user['lang_pref'] === 'en' ? 'selected' : '' ?>>🇬🇧 English</option>
                            <option value="fr" <?= $user['lang_pref'] === 'fr' ? 'selected' : '' ?>>🇫🇷 French</option>
                            <option value="es" <?= $user['lang_pref'] === 'es' ? 'selected' : '' ?>>🇪🇸 Spanish</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-check-lg me-1"></i>Save Preferences
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Apply theme change live (without page reload)
$extraScript = <<<JS
// Listen for theme radio changes and apply immediately via CSS class
document.querySelectorAll('input[name="theme"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.value === 'dark') {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }
    });
});
JS;

include __DIR__ . '/backend/includes/footer.php';
?>
