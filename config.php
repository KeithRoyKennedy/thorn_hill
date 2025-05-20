<?php
/**
 * Database configuration
 */
define('DB_HOST', 'sandbox_assesment_db');
define('DB_NAME', 'thornhill');
define('DB_USER', 'assesment');     // Change to appropriate database user
define('DB_PASS', 'c6a9963594a7a305442e');     // Change to appropriate database password

/**
 * Error reporting settings
 */
ini_set('display_errors', 0);  // Set to 1 for development, 0 for production
error_reporting(E_ALL);

/**
 * Other settings
 */
define('DEBUG_MODE', false);   // Set to true for development, false for production
