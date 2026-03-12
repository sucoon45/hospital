<?php
/**
 * Kamirex Specialist Hospital - Project Configuration
 */

// Domain & Path Configuration
define('APP_NAME', 'Kamirex Specialist Hospital');

// Dynamically determine the app URL to support local subdirectories and live servers
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://";
$host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dir = str_replace('\\', '/', __DIR__);
$app_path = str_replace($doc_root, '', $dir);
if ($app_path === $dir) {
    $app_path = ''; // Fallback in case of mismatch
}
define('APP_URL', rtrim($protocol . $host . $app_path, '/'));

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
