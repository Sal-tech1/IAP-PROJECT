<?php
/**
 * includes/helpers.php
 * Small, reusable utility functions used across the application.
 */

if (!defined('APP_RUNNING')) exit;

// ─── Output escaping ────────────────────────────────────────────────────────

/** Safely echo a value – prevents XSS */
function esc(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ─── Flash messages (one-time notices stored in session) ────────────────────

function flashSet(string $type, string $message): void
{
    $_SESSION['flash'][$type][] = $message;
}

function flashGet(): array
{
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}

// ─── Pagination helper ──────────────────────────────────────────────────────

/**
 * Returns an associative array with: offset, limit, current page, total pages.
 *
 * @param int $totalRows  total number of rows in the result set
 * @param int $perPage    items per page (default 10)
 */
function paginate(int $totalRows, int $perPage = 10): array
{
    $page       = max(1, (int)($_GET['page'] ?? 1));
    $totalPages = max(1, (int)ceil($totalRows / $perPage));
    $page       = min($page, $totalPages);          // clamp to valid range

    return [
        'page'        => $page,
        'per_page'    => $perPage,
        'total_pages' => $totalPages,
        'total_rows'  => $totalRows,
        'offset'      => ($page - 1) * $perPage,
    ];
}

// ─── Cookie consent check ──────────────────────────────────────────────────

/** Returns true if the user has accepted cookies in this browser. */
function cookieConsentGiven(): bool
{
    return ($_COOKIE['consent'] ?? '') === 'true';
}
