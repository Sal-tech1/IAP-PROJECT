<?php
/**
 * logout.php  –  Destroys the session and redirects to login.
 * No page is rendered — this is a pure action endpoint.
 */
require_once __DIR__ . '/backend/includes/bootstrap.php';

if (isLoggedIn()) {
    flashSet('success', 'You have been logged out successfully.');
    handleLogout();   // destroys session & redirects to login.php
}

// If not logged in, just bounce to login
header('Location: login.php');
exit;
