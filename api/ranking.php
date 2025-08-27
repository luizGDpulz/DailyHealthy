<?php
/**
 * DailyHealthy - API de Ranking
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/User.php';

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

// Verificar autenticação
if (!Auth::isLoggedIn()) {
    jsonResponse(false, null, 'Usuário não autenticado', 401);
    exit;
}

try {
    $method = $_SERVER['REQUEST_METHOD'];
    $userId = Auth::getCurrentUserId();
    
    switch ($method) {
        case 'GET':
            handleGet($userId);
            break;
            
        default:
            jsonResponse(false, null, 'Método não permitido', 405);
    }
    
} catch (Exception $e) {
    error_log("Erro na API de Ranking: " . $e->getMessage());
    jsonResponse(false, null, 'Erro interno do servidor', 500);
}

/**
 * Tratar requisições GET
 */
function handleGet($userId) {
    $action = $_GET['action'] ?? 'ranking';
    
    switch ($action) {
        case 'ranking':
            getRanking($userId);
            break;
            
        case 'user_position':
            getUserPosition($userId);
            break;
            
        default:
            getRanking($userId);
    }
}

/**
 * Obter ranking de usuários
 */
function getRanking($userId) {
    $limit = intval($_GET['limit'] ?? 20);
    
    // Limitar entre 5 e 50 usuários
    if ($limit < 5) $limit = 5;
    if ($limit > 50) $limit = 50;
    
    $ranking = User::getRanking($limit);
    
    if ($ranking !== false) {
        // Adicionar informações extras para cada usuário
        foreach ($ranking as &$user) {
            // Formatar última atividade
            if ($user['last_activity']) {
                $lastActivity = new DateTime($user['last_activity']);
                $now = new DateTime();
                $diff = $now->diff($lastActivity);
                
                if ($diff->days == 0) {
                    $user['last_activity_text'] = 'Hoje';
                } elseif ($diff->days == 1) {
                    $user['last_activity_text'] = 'Ontem';
                } else {
                    $user['last_activity_text'] = $diff->days . ' dias atrás';
                }
            } else {
                $user['last_activity_text'] = 'Nunca';
            }
            
            // Adicionar indicador se é o usuário atual
            $user['is_current_user'] = ($user['id'] == $userId);
        }
        
        jsonResponse(true, $ranking, null, 200, [
            'current_user_id' => $userId,
            'total_users' => count($ranking)
        ]);
    } else {
        jsonResponse(false, null, 'Erro ao buscar ranking');
    }
}

/**
 * Obter posição específica do usuário no ranking
 */
function getUserPosition($userId) {
    $position = User::getUserRankPosition($userId);
    $userStats = User::getUserStats($userId);
    
    if ($position && $userStats) {
        $data = [
            'position' => $position,
            'user' => $userStats['user'],
            'stats' => [
                'habits_total' => $userStats['habits_total'],
                'habits_completed_today' => $userStats['habits_completed_today'],
                'total_executions' => $userStats['total_executions'],
                'badges_count' => $userStats['badges_count']
            ]
        ];
        
        jsonResponse(true, $data);
    } else {
        jsonResponse(false, null, 'Erro ao buscar posição do usuário');
    }
}
?>

