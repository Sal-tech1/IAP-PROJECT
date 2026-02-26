
<?php
/**
 * includes/bootstrap.php
 * Entry-point for every page.  Include this FIRST, before any output.
 *
 *   require_once __DIR__ . '/includes/bootstrap.php';
 *
 * Responsibilities:
 *   • Load environment variables
 *   • Configure & start a secure session
 *   • Include shared helpers (db, auth, audit)
 */

// ── 1. Suppress direct access to include files ──────────────────────────────
// (Each include checks for this constant)
define('APP_RUNNING', true);

// ── 2. Load .env ─────────────────────────────────────────────────────────────
require_once __DIR__ . '/env.php';
loadEnv();

// ── 3. Basic error settings (honour APP_DEBUG) ──────────────────────────────
if (getenv('APP_DEBUG') === 'true') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE);
}

// ── 4. Secure session configuration ──────────────────────────────────────────
session_set_cookie_params([
    'lifetime' => 0,                          // browser session only
    'path'     => '/',
    'domain'   => '',                         // current domain
    'secure'   => ($_SERVER['HTTPS'] ?? 'off') === 'on',  // Secure flag on HTTPS
    'httponly' => true,                       // JS cannot read session cookie
    'samesite' => 'Strict',                   // CSRF mitigation
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 5. CSRF token — generate once per session ───────────────────────────────
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── 6. Include shared modules ────────────────────────────────────────────────
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/audit.php';
require_once __DIR__ . '/helpers.php';
