<?php
/**
 * User Logout API
 * SSC Batch '94
 */

require_once '../../config/config.php';

// Clear session
session_unset();
session_destroy();

// Clear remember me cookie
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect to login page
redirect('../../views/auth/login.html');
?>