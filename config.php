<?php
/**
 * Kamirex Specialist Hospital - Project Configuration
 */

// Domain & Path Configuration
define('APP_NAME', 'Kamirex Specialist Hospital');
define('APP_URL', 'http://localhost/kamirex-hospital'); // Update this on deployment
define('BASE_PATH', __DIR__);

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kamirex_hms');

// Session Security
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Autoload Classes or include core functions
 */
require_once BASE_PATH . '/includes/functions/auth.php';
require_once BASE_PATH . '/includes/functions/database.php';
require_once BASE_PATH . '/includes/functions/utils.php';

// Error Reporting (Turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

/**
 * Currency & Localization
 */
define('CURRENCY', '₦');
date_default_timezone_set('Africa/Lagos');
?>
