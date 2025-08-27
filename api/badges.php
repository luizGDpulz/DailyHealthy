<?php
/**
 * DailyHealthy - API de Badges
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
    error_log("Erro na API de Badges: " . $e->getMessage());
    jsonResponse(false, null, 'Erro interno do servidor', 500);
}

/**
 * Tratar requisições GET
 */
function handleGet($userId) {
    $action = $_GET['action'] ?? 'user_badges';
    
    switch ($action) {
        case 'user_badges':
            getUserBadges($userId);
            break;
            
        case 'available_badges':
            getAvailableBadges($userId);
            break;
            
        case 'check_new_badges':
            checkNewBadges($userId);
            break;
            
        default:
            getUserBadges($userId);
    }
}

/**
 * Obter badges do usuário
 */
function getUserBadges($userId) {
    $badges = User::getUserBadges($userId);
    
    if ($badges !== false) {
        // Adicionar informações extras para cada badge
        foreach ($badges as &$badge) {
            // Formatar data de conquista
            if ($badge['earned_at']) {
                $earnedDate = new DateTime($badge['earned_at']);
                $badge['earned_at_formatted'] = $earnedDate->format('d/m/Y');
                $badge['earned_at_relative'] = getRelativeTime($earnedDate);
            }
            
            // Adicionar ícone emoji baseado no tipo
            $badge['emoji'] = getBadgeEmoji($badge['icon']);
        }
        
        jsonResponse(true, $badges, null, 200, [
            'total_badges' => count($badges)
        ]);
    } else {
        jsonResponse(false, null, 'Erro ao buscar badges do usuário');
    }
}

/**
 * Obter badges disponíveis (todos os badges do sistema)
 */
function getAvailableBadges($userId) {
    $sql = "
        SELECT 
            b.*,
            CASE 
                WHEN ub.id IS NOT NULL THEN 1 
                ELSE 0 
            END as is_earned,
            ub.earned_at
        FROM badges b
        LEFT JOIN user_badges ub ON b.id = ub.badge_id AND ub.user_id = ?
        WHERE b.is_active = 1
        ORDER BY 
            is_earned DESC,
            CASE b.type
                WHEN 'special' THEN 1
                WHEN 'points' THEN 2
                WHEN 'streak' THEN 3
                WHEN 'habits' THEN 4
                ELSE 5
            END,
            b.points_required ASC,
            b.streak_required ASC,
            b.habits_required ASC
    ";
    
    $badges = Database::fetchAll($sql, [$userId]);
    
    if ($badges !== false) {
        // Adicionar informações extras
        foreach ($badges as &$badge) {
            $badge['emoji'] = getBadgeEmoji($badge['icon']);
            $badge['is_earned'] = (bool) $badge['is_earned'];
            
            if ($badge['earned_at']) {
                $earnedDate = new DateTime($badge['earned_at']);
                $badge['earned_at_formatted'] = $earnedDate->format('d/m/Y');
                $badge['earned_at_relative'] = getRelativeTime($earnedDate);
            }
            
            // Adicionar progresso para badges não conquistados
            if (!$badge['is_earned']) {
                $badge['progress'] = calculateBadgeProgress($badge, $userId);
            }
        }
        
        // Separar badges conquistados e não conquistados
        $earned = array_filter($badges, function($badge) {
            return $badge['is_earned'];
        });
        
        $available = array_filter($badges, function($badge) {
            return !$badge['is_earned'];
        });
        
        jsonResponse(true, [
            'earned' => array_values($earned),
            'available' => array_values($available)
        ], null, 200, [
            'total_earned' => count($earned),
            'total_available' => count($available)
        ]);
    } else {
        jsonResponse(false, null, 'Erro ao buscar badges disponíveis');
    }
}

/**
 * Verificar e atribuir novos badges
 */
function checkNewBadges($userId) {
    $newBadges = User::checkAndAwardBadges($userId);
    
    if ($newBadges !== false) {
        jsonResponse(true, $newBadges, null, 200, [
            'new_badges_count' => count($newBadges)
        ]);
    } else {
        jsonResponse(false, null, 'Erro ao verificar novos badges');
    }
}

/**
 * Obter emoji do badge baseado no ícone
 */
function getBadgeEmoji($iconName) {
    $icons = [
        'award' => '🏆',
        'star' => '⭐',
        'medal' => '🏅',
        'trophy' => '🏆',
        'flame' => '🔥',
        'zap' => '⚡',
        'fire' => '🔥',
        'heart' => '❤️',
        'footprints' => '👣',
        'sunrise' => '🌅',
        'moon' => '🌙',
        'list' => '📋',
        'calendar' => '📅',
        'target' => '🎯',
        'crown' => '👑',
        'gem' => '💎',
        'rocket' => '🚀',
        'lightning' => '⚡',
        'sparkles' => '✨'
    ];
    
    return $icons[$iconName] ?? '🏆';
}

/**
 * Calcular progresso para um badge não conquistado
 */
function calculateBadgeProgress($badge, $userId) {
    $user = User::findById($userId);
    if (!$user) return 0;
    
    switch ($badge['type']) {
        case 'points':
            if ($badge['points_required'] > 0) {
                return min(100, ($user['points'] / $badge['points_required']) * 100);
            }
            break;
            
        case 'streak':
            if ($badge['streak_required'] > 0) {
                return min(100, ($user['streak'] / $badge['streak_required']) * 100);
            }
            break;
            
        case 'habits':
            if ($badge['habits_required'] > 0) {
                $sql = "SELECT COUNT(*) as count FROM habits WHERE user_id = ? AND is_active = 1";
                $result = Database::fetchOne($sql, [$userId]);
                $habitsCount = $result['count'] ?? 0;
                return min(100, ($habitsCount / $badge['habits_required']) * 100);
            }
            break;
            
        case 'special':
            // Badges especiais têm lógica própria
            return 0;
    }
    
    return 0;
}

/**
 * Obter tempo relativo
 */
function getRelativeTime($date) {
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days == 0) {
        if ($diff->h == 0) {
            if ($diff->i == 0) {
                return 'Agora mesmo';
            } else {
                return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
            }
        } else {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
        }
    } elseif ($diff->days == 1) {
        return 'Ontem';
    } elseif ($diff->days < 7) {
        return $diff->days . ' dias atrás';
    } elseif ($diff->days < 30) {
        $weeks = floor($diff->days / 7);
        return $weeks . ' semana' . ($weeks > 1 ? 's' : '') . ' atrás';
    } elseif ($diff->days < 365) {
        $months = floor($diff->days / 30);
        return $months . ' mês' . ($months > 1 ? 'es' : '') . ' atrás';
    } else {
        $years = floor($diff->days / 365);
        return $years . ' ano' . ($years > 1 ? 's' : '') . ' atrás';
    }
}
?>

