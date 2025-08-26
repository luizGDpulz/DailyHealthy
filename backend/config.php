<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'dailyhealthy_db');
define('DB_USER', 'root');
define('DB_PASS', '');

// PDO connection
function getDbConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

?>

