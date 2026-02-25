<?php
/**
 * includes/auth.php
 * All authentication logic lives here.
 *
 * Public functions:
 * isLoggedIn()          – bool
 * getCurrentUser()      – array|null
 * requireAuth()         – redirects to login if not authenticated
 * handleLogin()         – processes POST login form
 * handleRegister()      – processes POST registration form
 * handleLogout()        – destroys session, redirects
 * verifyCsrf()          – validates the CSRF token from a form POST
 */

if (!defined('APP_RUNNING')) exit;

// ─── Session helpers ────────────────────────────────────────────────────────

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function getCurrentUser(): ?array
{
    if (!isLoggedIn()) return null;

    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT id, name, email, role, theme_pref, lang_pref FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch() ?: null;
}

function requireAuth(): void
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// ─── CSRF ───────────────────────────────────────────────────────────────────

function verifyCsrf(): bool
{
    return isset($_POST['csrf_token']) &&
           hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token']);
}

// ─── Login ──────────────────────────────────────────────────────────────────

/**
 * Processes a POST login attempt.
 * Returns null on success (redirects internally).
 * Returns an error string on failure.
 */
function handleLogin(): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return null;
    if (!verifyCsrf()) return 'Invalid request. Please try again.';

    $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    // ── Validate inputs ───────────────────────────────────────────────────
    if (!$email) {
        return 'Please enter a valid email address.';
    }
    if (strlen($password) < 1) {
        return 'Please enter your password.';
    }

    // ── Query user ────────────────────────────────────────────────────────
    $pdo  = getDB();
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // ── Verify ────────────────────────────────────────────────────────────
    if (!$user || !password_verify($password, $user['password_hash'])) {
        // Generic message — do NOT reveal whether email exists
        return 'Invalid email or password.';
    }

    // ── Success: regenerate session, store data, redirect ────────────────
    session_regenerate_id(true);                       // prevent session fixation
    $_SESSION['user_id']   = $user['id'];
    $_SESSION['user_role'] = $user['role'];

    // Honour a pending redirect URL (stored before the login wall)
    $redirect = $_SESSION['redirect_after_login'] ?? 'dashboard.php';
    unset($_SESSION['redirect_after_login']);

    header("Location: {$redirect}");
    exit;
}

// ─── Register ───────────────────────────────────────────────────────────────

/**
 * Processes a POST registration attempt.
 * Returns null on success (redirects internally).
 * Returns an error string on failure.
 */
function handleRegister(): ?string
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return null;
    if (!verifyCsrf()) return 'Invalid request. Please try again.';

    $name     = trim($_POST['name']     ?? '');
    $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['password_confirm'] ?? '';

    // ── Validation ────────────────────────────────────────────────────────
    if (strlen($name) < 2 || strlen($name) > 100) {
        return 'Name must be between 2 and 100 characters.';
    }
    if (!$email) {
        return 'Please enter a valid email address.';
    }
    if (strlen($password) < 6) {
        return 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }

    $pdo = getDB();

    // ── Check uniqueness ──────────────────────────────────────────────────
    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return 'This email address is already registered.';
    }

    // ── Insert ────────────────────────────────────────────────────────────
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)'
    );
    $stmt->execute([
        htmlspecialchars(strip_tags($name)),
        $email,
        $hash,
        'user'                                         // new accounts are always "user"
    ]);

    // ── Auto-login after registration ─────────────────────────────────────
    session_regenerate_id(true);
    $_SESSION['user_id']   = $pdo->lastInsertId();
    $_SESSION['user_role'] = 'user';

    header('Location: dashboard.php');
    exit;
}

// ─── Logout ─────────────────────────────────────────────────────────────────

function handleLogout(): void
{
    // Wipe session data, regenerate ID, then destroy
    $_SESSION = [];
    session_regenerate_id(true);
    session_destroy();

    // Clear the session cookie from the browser
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => ($_SERVER['HTTPS'] ?? 'off') === 'on',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
    }

    header('Location: login.php');
    exit;
}
