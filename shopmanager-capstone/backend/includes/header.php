<?php
/**
 * includes/header.php
 * Shared top-of-page HTML: <head>, navbar, cookie banner, flash messages.
 *
 * Usage at the top of every page (AFTER bootstrap.php):
 *   $pageTitle = 'My Page';
 *   include __DIR__ . '/includes/header.php';
 */

if (!defined('APP_RUNNING')) exit;

$pageTitle  = $pageTitle  ?? 'ICS 2203 – Capstone App';
$user       = getCurrentUser();
$flash      = flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="A responsive product management web application built with PHP and MySQL." />
    <link rel="icon" type="image/png" href="frontend/assets/favicon.jpeg">

    <title><?= esc($pageTitle) ?> | DukaDash</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          crossorigin="anonymous" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <!-- Custom stylesheet -->
    <link rel="stylesheet" href="frontend/css/style.css" />
</head>
<body class="min-vh-100 d-flex flex-column">

<!-- ── Cookie Consent Banner ──────────────────────────────────────────────── -->
<div id="cookieBanner"
     class="fixed-bottom bg-dark text-white p-3 shadow-lg"
     style="display:none; z-index:1100;">
    <div class="container d-flex flex-wrap align-items-center justify-content-between gap-3">
        <p class="mb-0" style="max-width:600px;">
            <i class="bi bi-cookie me-2"></i>
            We use cookies to personalise your experience (theme &amp; language preference).
            No tracking cookies are set.
            Read our <a href="privacy.php" class="text-decoration-none text-warning">Privacy Policy</a>.
        </p>
        <div class="d-flex gap-2">
            <button class="btn btn-warning btn-sm fw-semibold" id="acceptCookies">Sure 😁</button>
            <button class="btn btn-outline-light btn-sm"       id="declineCookies">No, thanks 😓</button>
        </div>
    </div>
</div>

<!-- ── Navbar ─────────────────────────────────────────────────────────────── -->
<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background: rgba(0, 0, 0, 0.25); backdrop-filter: blur(8px)">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand fw-bolder fs-2" href="index.php">
            <i class="bi bi-shop me-2"></i>DukaDash
        </a>

        <!-- Hamburger toggle (mobile) -->
        <button class="navbar-toggler border-0" type="button"
                data-bs-toggle="collapse" data-bs-target="#navMenu"
                aria-controls="navMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Links -->
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
<?php if ($user): ?>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['SCRIPT_FILENAME']) === 'dashboard.php' ? 'active' : '' ?>"
                       href="dashboard.php">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['SCRIPT_FILENAME']) === 'products.php' ? 'active' : '' ?>"
                       href="products.php">
                        <i class="bi bi-box-seam me-1"></i>Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['SCRIPT_FILENAME']) === 'settings.php' ? 'active' : '' ?>"
                       href="settings.php">
                        <i class="bi bi-gear me-1"></i>Settings
                    </a>
                </li>
<?php endif; ?>
            </ul>

            <!-- Right-side auth block -->
            <ul class="navbar-nav">
<?php if ($user): ?>
                <li class="nav-item">
                    <span class="nav-link text-white-50 small">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= esc($user['name']) ?>
                        <?php if ($user['role'] === 'admin'): ?>
                            <span class="badge bg-warning text-dark ms-1">Admin</span>
                        <?php endif; ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">
                        <i class="bi bi-box-arrow-right me-1"></i>Logout
                    </a>
                </li>
<?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="login.php">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Login
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="register.php">
                        <i class="bi bi-person-plus me-1"></i>Register
                    </a>
                </li>
<?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- ── Flash Messages ─────────────────────────────────────────────────────── -->
<?php if (!empty($flash)): ?>
<div class="container mt-3">
    <?php foreach ($flash as $type => $messages): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="alert alert-<?= esc($type) ?> alert-dismissible fade show" role="alert">
                <?= esc($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- ── Page Content starts here ───────────────────────────────────────────── -->
<main class="container flex-grow-1 py-4">
