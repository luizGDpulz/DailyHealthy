<?php
/**
 * DailyHealthy - API de Autenticação
 */

require_once __DIR__ . 
'/../config/config.php';
require_once __DIR__ . 
'/../app/Auth.php';
require_once __DIR__ . 
'/../app/User.php';
require_once __DIR__ . 
'/../includes/functions.php'; // Incluir funções auxiliares

// Headers para API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Tratar requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true);
    
    switch ($method) {
        case 'GET':
            handleGet();
            break;
            
        case 'POST':
            handlePost($input);
            break;
            
        case 'DELETE':
            handleDelete();
            break;
            
        default:
            jsonResponse(false, null, 'Método não permitido', 405);
    }
    
} catch (Exception $e) {
    error_log("Erro na API de Auth: " . $e->getMessage());
    jsonResponse(false, null, 'Erro interno do servidor', 500);
}

/**
 * Tratar requisições GET
 */
function handleGet() {
    $action = $_GET['action'] ?? '';
    
    switch ($action) {
        case 'user_stats':
            getUserStats();
            break;
            
        case 'check_auth':
            checkAuth();
            break;
            
        default:
            jsonResponse(false, null, 'Ação não especificada');
    }
}

/**
 * Tratar requisições POST
 */
function handlePost($input) {
    if (!$input) {
        jsonResponse(false, null, 'Dados não fornecidos');
        return;
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'login':
            handleLogin($input);
            break;
            
        case 'register':
            handleRegister($input);
            break;
            
        default:
            jsonResponse(false, null, 'Ação não especificada');
    }
}

/**
 * Tratar requisições DELETE
 */
function handleDelete() {
    handleLogout();
}

/**
 * Fazer login
 */
function handleLogin($input) {
    // Validar CSRF token
    // O token CSRF é gerado na página de login e enviado via AJAX.
    // A função verifyCSRFToken() verifica se o token enviado corresponde ao token na sessão.
    if (!verifyCSRFToken($input['csrf_token'] ?? '')) {
        jsonResponse(false, null, 'Token CSRF inválido', 403); // 403 Forbidden
        return;
    }
    
    $email = sanitize($input['email'] ?? '');
    $password = $input['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        jsonResponse(false, null, 'Email e senha são obrigatórios');
        return;
    }
    
    $result = Auth::login($email, $password);
    
    if ($result['success']) {
        jsonResponse(true, $result['user'], $result['message']);
    } else {
        jsonResponse(false, null, $result['message']);
    }
}

/**
 * Registrar usuário
 */
function handleRegister($input) {
    // Validar CSRF token
    if (!verifyCSRFToken($input['csrf_token'] ?? '')) {
        jsonResponse(false, null, 'Token CSRF inválido', 403);
        return;
    }
    
    $name = sanitize($input['name'] ?? '');
    $email = sanitize($input['email'] ?? '');
    $password = $input['password'] ?? '';
    $confirmPassword = $input['confirmPassword'] ?? '';
    
    if (empty($name) || empty($email) || empty($password) || empty($confirmPassword)) {
        jsonResponse(false, null, 'Todos os campos são obrigatórios');
        return;
    }
    
    $result = Auth::register($name, $email, $password, $confirmPassword);
    
    if ($result['success']) {
        jsonResponse(true, $result['user'], $result['message']);
    } else {
        jsonResponse(false, null, $result['message']);
    }
}

/**
 * Fazer logout
 */
function handleLogout() {
    $result = Auth::logout();
    
    if ($result['success']) {
        jsonResponse(true, null, $result['message']);
    } else {
        jsonResponse(false, null, $result['message']);
    }
}

/**
 * Verificar autenticação
 */
function checkAuth() {
    if (Auth::isLoggedIn()) {
        $user = Auth::getCurrentUser();
        jsonResponse(true, [
            'authenticated' => true,
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'points' => $user['points'],
                'streak' => $user['streak']
            ]
        ]);
    } else {
        jsonResponse(false, ['authenticated' => false], 'Usuário não autenticado');
    }
}

/**
 * Obter estatísticas do usuário
 */
function getUserStats() {
    if (!Auth::isLoggedIn()) {
        jsonResponse(false, null, 'Usuário não autenticado', 401);
        return;
    }
    
    $userId = Auth::getCurrentUserId();
    $stats = User::getUserStats($userId);
    
    if ($stats) {
        // Recalcular streak para garantir que está atualizado
        User::calculateStreak($userId);
        
        // Buscar dados atualizados
        $updatedUser = User::findById($userId);
        $stats['user'] = $updatedUser;
        
        // Atualizar sessão
        Auth::updateSessionUser();
        
        jsonResponse(true, $stats);
    } else {
        jsonResponse(false, null, 'Erro ao obter estatísticas');
    }
}
?>

