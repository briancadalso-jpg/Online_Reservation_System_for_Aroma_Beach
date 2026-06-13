<?php
require_once __DIR__ . '/controller/system_core.php';

// If the user is already logged in as an admin, take them to their dashboard.
if (is_logged_in()) {
    header('Location: ' . role_home_url());
    exit();
}

// Otherwise, display the main selection page (src/home.php)
require_once __DIR__ . '/src/home.php';
