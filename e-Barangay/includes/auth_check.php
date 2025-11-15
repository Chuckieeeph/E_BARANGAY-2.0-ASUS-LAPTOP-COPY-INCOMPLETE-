<?php
// includes/auth_check.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * require_role -- ensure current user has the specified role.
 * If not logged in or incorrect role, send to the login page.
 */
function require_role($role) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /e-barangay/public/login.php");
        exit;
    }
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $role) {
        // Optionally destroy session for safety
        // session_unset(); session_destroy();
        header("Location: /e-barangay/public/login.php");
        exit;
    }
}
