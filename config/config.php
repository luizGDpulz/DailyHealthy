<?php
/**
 * DailyHealthy - Configurações do Sistema
 * Arquivo de configuração para XAMPP
 */

// Configurações do Banco de Dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'dailyhealthy');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configurações da Aplicação
define('BASE_URL', 'http://localhost/dailyhealthy/');
define('SITE_NAME', 'DailyHealthy');
define('SITE_DESCRIPTION', 'Transforme sua vida com hábitos saudáveis');

// Configurações de Sessão
define('SESSION_NAME', 'dailyhealthy_session');
define('SESSION_LIFETIME', 3600 * 24); // 24 horas

// Configurações de Segurança
define('HASH_ALGO', PASSWORD_DEFAULT);
define('CSRF_TOKEN_NAME', 'csrf_token');

// Configurações de Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Erro (Desenvolvimento)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sessão se não estiver ativa
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// Função para gerar CSRF Token
function generateCSRFToken() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

// Função para verificar CSRF Token
function verifyCSRFToken($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

// Função para sanitizar dados
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

// Função para validar email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Função para resposta JSON
function jsonResponse($success, $data = null, $message = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'message' => $message
    ]);
    exit;
}
?>

