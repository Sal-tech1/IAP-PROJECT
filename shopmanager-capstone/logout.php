<?php
/**
 * logout.php – Destroys the session and redirects to login.
 */
require_once __DIR__ . '/includes/bootstrap.php';

if (isLoggedIn()) {
    flashSet('success', 'You have been logged out successfully.');
    handleLogout(); 
}

header('Location: login.php');
exit;