<?php
/**
 * Database configuration
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'staff_database');
define('DB_USER', 'root');     // Change to appropriate database user
define('DB_PASS', '');         // Change to appropriate database password

/**
 * Error reporting settings
 */
ini_set('display_errors', 0);  // Set to 1 for development, 0 for production
error_reporting(E_ALL);

/**
 * Other settings
 */
define('DEBUG_MODE', false);   // Set to true for development, false for production
