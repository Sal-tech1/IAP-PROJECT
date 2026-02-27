<?php
/**
 * includes/header.php
 * This file handles the opening HTML tags, the <head> section, 
 * the site-wide navigation bar, and the flash message system.
 */

// Security Check: Ensure this file is included through bootstrap.php
if (!defined('APP_RUNNING')) exit;

// Fallback for page titles if not specifically set in the parent file
$pageTitle  = $pageTitle  ?? 'IAP Project';
$user       = getCurrentUser();
$flash      = flashGet();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="DukaDash - Product Management System" />
    
    <link rel="icon" type="image/png" href="assets/favicon.jpeg">

    <title><?= esc($pageTitle) ?> | DukaDash</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" crossorigin="anonymous" />
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    
    <link rel="stylesheet" href="css/style.css" />
</head>

<body class="<?= ($user['theme_pref'] ?? '') === 'dark' ? 'theme-dark' : '' ?> d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark shadow-sm" style="background-color: var(--clr-primary);">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">
            <i class="bi bi-shop me-2"></i>DukaDash
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav ms-auto align-items-center">
<?php if (isLoggedIn()): ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="products.php">Inventory</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="settings.php">
                        <i class="bi bi-person-circle me-1"></i><?= esc($user['name']) ?>
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
                </li>
<?php else: ?>
                <li class="nav-item">
                    <a class="nav-link text-white" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="register.php">Register</a>
                </li>
<?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<?php if (!empty($flash)): ?>
<div class="container mt-3">
    <?php foreach ($flash as $type => $messages): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="alert alert-<?= esc($type) ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle me-2"></i><?= esc($msg) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main class="container py-4 flex-grow-1">