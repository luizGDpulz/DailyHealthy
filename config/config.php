<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'dailyhealthy');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('APP_NAME', 'DailyHealthy');
define('APP_URL', 'http://localhost/dailyhealthy');
define('APP_VERSION', '1.0.0');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
session_start();

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Time zone
date_default_timezone_set('America/Sao_Paulo');

// Security
define('CSRF_TOKEN_SECRET', 'your-secret-key-here');
