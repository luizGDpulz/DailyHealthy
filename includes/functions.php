<?php
/**
 * DailyHealthy - Funções Auxiliares
 * Funções de uso geral para o aplicativo.
 */

if (!function_exists("jsonResponse")) {
    function jsonResponse($success, $data = null, $message = null, $statusCode = 200, $meta = []) {
        http_response_code($statusCode);
        header("Content-Type: application/json");
        echo json_encode([
            "success" => $success,
            "data" => $data,
            "message" => $message,
            "meta" => $meta
        ]);
        exit;
    }
}

if (!function_exists("sanitize")) {
    function sanitize($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }
}

if (!function_exists("verifyCSRFToken")) {
    function verifyCSRFToken($token) {
        if (!isset($_SESSION["csrf_token"]) || $token !== $_SESSION["csrf_token"]) {
            return false;
        }
        return true;
    }
}

if (!function_exists("generateCSRFToken")) {
    function generateCSRFToken() {
        if (empty($_SESSION["csrf_token"])) {
            $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
        }
        return $_SESSION["csrf_token"];
    }
}

?>

