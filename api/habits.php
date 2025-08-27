<?php
/**
 * DailyHealthy - API de Hábitos
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Auth.php';
require_once __DIR__ . '/../app/Habit.php';
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
    $input = json_decode(file_get_contents('php://input'), true);
    $userId = Auth::getCurrentUserId();
    
    switch ($method) {
        case 'GET':
            handleGet($userId);
            break;
            
        case 'POST':
            handlePost($input, $userId);
            break;
            
        case 'PUT':
            handlePut($input, $userId);
            break;
            
        case 'DELETE':
            handleDelete($input, $userId);
            break;
            
        default:
            jsonResponse(false, null, 'Método não permitido', 405);
    }
    
} catch (Exception $e) {
    error_log("Erro na API de Hábitos: " . $e->getMessage());
    jsonResponse(false, null, 'Erro interno do servidor', 500);
}

/**
 * Tratar requisições GET
 */
function handleGet($userId) {
    $habitId = $_GET['id'] ?? null;
    
    if ($habitId) {
        getHabitDetails($habitId, $userId);
    } else {
        getUserHabits($userId);
    }
}

/**
 * Tratar requisições POST
 */
function handlePost($input, $userId) {
    if (!$input) {
        jsonResponse(false, null, 'Dados não fornecidos');
        return;
    }
    
    createHabit($input, $userId);
}

/**
 * Tratar requisições PUT
 */
function handlePut($input, $userId) {
    if (!$input) {
        jsonResponse(false, null, 'Dados não fornecidos');
        return;
    }
    
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'toggle':
            toggleHabit($input, $userId);
            break;
            
        case 'update':
            updateHabit($input, $userId);
            break;
            
        default:
            jsonResponse(false, null, 'Ação não especificada');
    }
}

/**
 * Tratar requisições DELETE
 */
function handleDelete($input, $userId) {
    if (!$input) {
        jsonResponse(false, null, 'Dados não fornecidos');
        return;
    }
    
    deleteHabit($input, $userId);
}

/**
 * Obter hábitos do usuário
 */
function getUserHabits($userId) {
    $habits = Habit::getUserHabits($userId);
    
    if ($habits !== false) {
        jsonResponse(true, $habits);
    } else {
        jsonResponse(false, null, 'Erro ao buscar hábitos');
    }
}

/**
 * Obter detalhes de um hábito específico
 */
function getHabitDetails($habitId, $userId) {
    $habit = Habit::findById($habitId);
    
    if (!$habit) {
        jsonResponse(false, null, 'Hábito não encontrado', 404);
        return;
    }
    
    // Verificar se o hábito pertence ao usuário
    if ($habit['user_id'] != $userId) {
        jsonResponse(false, null, 'Acesso negado', 403);
        return;
    }
    
    // Obter estatísticas do hábito
    $stats = Habit::getHabitStats($habitId, $userId);
    
    jsonResponse(true, $stats);
}

/**
 * Criar novo hábito
 */
function createHabit($input, $userId) {
    // Validações
    $title = sanitize($input['title'] ?? '');
    $description = sanitize($input['description'] ?? '');
    $pointsBase = intval($input['points_base'] ?? 10);
    $category = sanitize($input['category'] ?? 'geral');
    $color = sanitize($input['color'] ?? '#4CAF50');
    
    if (empty($title)) {
        jsonResponse(false, null, 'Título é obrigatório');
        return;
    }
    
    if (strlen($title) > 150) {
        jsonResponse(false, null, 'Título muito longo (máximo 150 caracteres)');
        return;
    }
    
    if (strlen($description) > 500) {
        jsonResponse(false, null, 'Descrição muito longa (máximo 500 caracteres)');
        return;
    }
    
    if ($pointsBase < 1 || $pointsBase > 100) {
        jsonResponse(false, null, 'Pontos devem estar entre 1 e 100');
        return;
    }
    
    // Validar cor (formato hex)
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        $color = '#4CAF50'; // Cor padrão se inválida
    }
    
    $habitData = [
        'user_id' => $userId,
        'title' => $title,
        'description' => $description,
        'points_base' => $pointsBase,
        'category' => $category,
        'color' => $color
    ];
    
    $habitId = Habit::create($habitData);
    
    if ($habitId) {
        $habit = Habit::findById($habitId);
        jsonResponse(true, $habit, 'Hábito criado com sucesso');
    } else {
        jsonResponse(false, null, 'Erro ao criar hábito');
    }
}

/**
 * Alternar estado do hábito (marcar/desmarcar como concluído)
 */
function toggleHabit($input, $userId) {
    $habitId = intval($input['habit_id'] ?? 0);
    
    if (!$habitId) {
        jsonResponse(false, null, 'ID do hábito é obrigatório');
        return;
    }
    
    // Verificar se o hábito existe e pertence ao usuário
    $habit = Habit::findById($habitId);
    if (!$habit || $habit['user_id'] != $userId) {
        jsonResponse(false, null, 'Hábito não encontrado', 404);
        return;
    }
    
    // Verificar se já foi completado hoje
    $isCompleted = Habit::isCompletedToday($habitId, $userId);
    
    if ($isCompleted) {
        // Desmarcar hábito
        $result = Habit::markAsIncomplete($habitId, $userId);
        
        if ($result) {
            jsonResponse(true, [
                'completed' => false,
                'points_removed' => $result['points_removed'],
                'message' => 'Hábito desmarcado'
            ]);
        } else {
            jsonResponse(false, null, 'Erro ao desmarcar hábito');
        }
    } else {
        // Marcar hábito como concluído
        $result = Habit::markAsCompleted($habitId, $userId);
        
        if ($result && $result['success']) {
            jsonResponse(true, [
                'completed' => true,
                'points_earned' => $result['points_earned'],
                'new_badges' => $result['new_badges'],
                'message' => 'Hábito concluído!'
            ]);
        } else {
            jsonResponse(false, null, 'Erro ao marcar hábito como concluído');
        }
    }
}

/**
 * Atualizar hábito
 */
function updateHabit($input, $userId) {
    $habitId = intval($input['habit_id'] ?? 0);
    
    if (!$habitId) {
        jsonResponse(false, null, 'ID do hábito é obrigatório');
        return;
    }
    
    // Verificar se o hábito existe e pertence ao usuário
    $habit = Habit::findById($habitId);
    if (!$habit || $habit['user_id'] != $userId) {
        jsonResponse(false, null, 'Hábito não encontrado', 404);
        return;
    }
    
    // Validações
    $title = sanitize($input['title'] ?? $habit['title']);
    $description = sanitize($input['description'] ?? $habit['description']);
    $pointsBase = intval($input['points_base'] ?? $habit['points_base']);
    $category = sanitize($input['category'] ?? $habit['category']);
    $color = sanitize($input['color'] ?? $habit['color']);
    
    if (empty($title)) {
        jsonResponse(false, null, 'Título é obrigatório');
        return;
    }
    
    if (strlen($title) > 150) {
        jsonResponse(false, null, 'Título muito longo (máximo 150 caracteres)');
        return;
    }
    
    if (strlen($description) > 500) {
        jsonResponse(false, null, 'Descrição muito longa (máximo 500 caracteres)');
        return;
    }
    
    if ($pointsBase < 1 || $pointsBase > 100) {
        jsonResponse(false, null, 'Pontos devem estar entre 1 e 100');
        return;
    }
    
    // Validar cor (formato hex)
    if (!preg_match('/^#[0-9A-Fa-f]{6}$/', $color)) {
        $color = $habit['color']; // Manter cor atual se inválida
    }
    
    $habitData = [
        'title' => $title,
        'description' => $description,
        'points_base' => $pointsBase,
        'category' => $category,
        'color' => $color
    ];
    
    $success = Habit::update($habitId, $habitData);
    
    if ($success) {
        $updatedHabit = Habit::findById($habitId);
        jsonResponse(true, $updatedHabit, 'Hábito atualizado com sucesso');
    } else {
        jsonResponse(false, null, 'Erro ao atualizar hábito');
    }
}

/**
 * Deletar hábito
 */
function deleteHabit($input, $userId) {
    $habitId = intval($input['habit_id'] ?? 0);
    
    if (!$habitId) {
        jsonResponse(false, null, 'ID do hábito é obrigatório');
        return;
    }
    
    // Verificar se o hábito existe e pertence ao usuário
    $habit = Habit::findById($habitId);
    if (!$habit || $habit['user_id'] != $userId) {
        jsonResponse(false, null, 'Hábito não encontrado', 404);
        return;
    }
    
    $success = Habit::deactivate($habitId);
    
    if ($success) {
        jsonResponse(true, null, 'Hábito removido com sucesso');
    } else {
        jsonResponse(false, null, 'Erro ao remover hábito');
    }
}
?>

