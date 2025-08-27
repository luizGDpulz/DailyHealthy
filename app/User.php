<?php
/**
 * DailyHealthy - Classe User
 * Model para gerenciamento de usuários
 */

require_once __DIR__ . '/Database.php';

class User {
    
    /**
     * Buscar usuário por email
     */
    public static function findByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ? AND is_active = 1";
        return Database::fetchOne($sql, [$email]);
    }
    
    /**
     * Buscar usuário por ID
     */
    public static function findById($id) {
        $sql = "SELECT * FROM users WHERE id = ? AND is_active = 1";
        return Database::fetchOne($sql, [$id]);
    }
    
    /**
     * Criar novo usuário
     */
    public static function create($data) {
        $sql = "
            INSERT INTO users (name, email, password, created_at) 
            VALUES (?, ?, ?, NOW())
        ";
        
        $hashedPassword = password_hash($data['password'], HASH_ALGO);
        
        return Database::insert($sql, [
            $data['name'],
            $data['email'],
            $hashedPassword
        ]);
    }
    
    /**
     * Verificar senha
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Atualizar pontos do usuário
     */
    public static function updatePoints($userId, $points) {
        $sql = "UPDATE users SET points = points + ?, updated_at = NOW() WHERE id = ?";
        return Database::execute($sql, [$points, $userId]);
    }
    
    /**
     * Atualizar streak do usuário
     */
    public static function updateStreak($userId, $streak) {
        $sql = "UPDATE users SET streak = ?, updated_at = NOW() WHERE id = ?";
        return Database::execute($sql, [$streak, $userId]);
    }
    
    /**
     * Atualizar última atividade
     */
    public static function updateLastActivity($userId, $date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        $sql = "UPDATE users SET last_activity = ?, updated_at = NOW() WHERE id = ?";
        return Database::execute($sql, [$date, $userId]);
    }
    
    /**
     * Obter ranking de usuários
     */
    public static function getRanking($limit = 10) {
        $sql = "
            SELECT 
                id,
                name,
                email,
                points,
                streak,
                last_activity,
                (SELECT COUNT(*) FROM user_badges WHERE user_id = users.id) as badges_count
            FROM users 
            WHERE is_active = 1 
            ORDER BY points DESC, streak DESC, name ASC 
            LIMIT ?
        ";
        
        return Database::fetchAll($sql, [$limit]);
    }
    
    /**
     * Obter posição do usuário no ranking
     */
    public static function getUserRankPosition($userId) {
        $sql = "
            SELECT COUNT(*) + 1 as position
            FROM users u1
            WHERE u1.is_active = 1 
            AND (
                u1.points > (SELECT points FROM users WHERE id = ?) 
                OR (
                    u1.points = (SELECT points FROM users WHERE id = ?) 
                    AND u1.id < ?
                )
            )
        ";
        
        $result = Database::fetchOne($sql, [$userId, $userId, $userId]);
        return $result ? $result['position'] : 1;
    }
    
    /**
     * Obter estatísticas do usuário
     */
    public static function getUserStats($userId) {
        // Estatísticas básicas
        $user = self::findById($userId);
        if (!$user) return null;
        
        // Total de hábitos
        $sql = "SELECT COUNT(*) as total FROM habits WHERE user_id = ? AND is_active = 1";
        $habitsCount = Database::fetchOne($sql, [$userId]);
        
        // Hábitos completados hoje
        $sql = "
            SELECT COUNT(*) as completed_today 
            FROM habit_executions 
            WHERE user_id = ? AND execution_date = CURDATE()
        ";
        $completedToday = Database::fetchOne($sql, [$userId]);
        
        // Total de execuções
        $sql = "SELECT COUNT(*) as total_executions FROM habit_executions WHERE user_id = ?";
        $totalExecutions = Database::fetchOne($sql, [$userId]);
        
        // Badges conquistadas
        $sql = "SELECT COUNT(*) as badges_count FROM user_badges WHERE user_id = ?";
        $badgesCount = Database::fetchOne($sql, [$userId]);
        
        // Posição no ranking
        $rankPosition = self::getUserRankPosition($userId);
        
        return [
            'user' => $user,
            'habits_total' => $habitsCount['total'],
            'habits_completed_today' => $completedToday['completed_today'],
            'total_executions' => $totalExecutions['total_executions'],
            'badges_count' => $badgesCount['badges_count'],
            'rank_position' => $rankPosition
        ];
    }
    
    /**
     * Calcular e atualizar streak do usuário
     */
    public static function calculateStreak($userId) {
        // Buscar execuções dos últimos 30 dias
        $sql = "
            SELECT DISTINCT execution_date 
            FROM habit_executions 
            WHERE user_id = ? 
            AND execution_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY execution_date DESC
        ";
        
        $executions = Database::fetchAll($sql, [$userId]);
        
        if (empty($executions)) {
            self::updateStreak($userId, 0);
            return 0;
        }
        
        $streak = 0;
        $currentDate = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Verificar se tem execução hoje ou ontem
        $hasToday = false;
        $hasYesterday = false;
        
        foreach ($executions as $execution) {
            if ($execution['execution_date'] === $currentDate) {
                $hasToday = true;
            }
            if ($execution['execution_date'] === $yesterday) {
                $hasYesterday = true;
            }
        }
        
        // Se não tem execução hoje nem ontem, streak é 0
        if (!$hasToday && !$hasYesterday) {
            self::updateStreak($userId, 0);
            return 0;
        }
        
        // Calcular streak consecutivo
        $checkDate = $hasToday ? $currentDate : $yesterday;
        
        foreach ($executions as $execution) {
            if ($execution['execution_date'] === $checkDate) {
                $streak++;
                $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
            } else {
                break;
            }
        }
        
        self::updateStreak($userId, $streak);
        return $streak;
    }
    
    /**
     * Obter badges do usuário
     */
    public static function getUserBadges($userId) {
        $sql = "
            SELECT 
                b.id,
                b.name,
                b.description,
                b.icon,
                b.color,
                b.type,
                ub.earned_at
            FROM user_badges ub
            JOIN badges b ON ub.badge_id = b.id
            WHERE ub.user_id = ?
            ORDER BY ub.earned_at DESC
        ";
        
        return Database::fetchAll($sql, [$userId]);
    }
    
    /**
     * Verificar e atribuir novos badges
     */
    public static function checkAndAwardBadges($userId) {
        $user = self::findById($userId);
        if (!$user) return [];
        
        $newBadges = [];
        
        // Verificar badges de pontos
        $sql = "
            SELECT b.id, b.name 
            FROM badges b
            WHERE b.type = 'points' 
            AND b.points_required <= ?
            AND b.id NOT IN (
                SELECT badge_id FROM user_badges WHERE user_id = ?
            )
        ";
        
        $pointsBadges = Database::fetchAll($sql, [$user['points'], $userId]);
        
        foreach ($pointsBadges as $badge) {
            $sql = "INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)";
            Database::execute($sql, [$userId, $badge['id']]);
            $newBadges[] = $badge['name'];
        }
        
        // Verificar badges de streak
        $sql = "
            SELECT b.id, b.name 
            FROM badges b
            WHERE b.type = 'streak' 
            AND b.streak_required <= ?
            AND b.id NOT IN (
                SELECT badge_id FROM user_badges WHERE user_id = ?
            )
        ";
        
        $streakBadges = Database::fetchAll($sql, [$user['streak'], $userId]);
        
        foreach ($streakBadges as $badge) {
            $sql = "INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)";
            Database::execute($sql, [$userId, $badge['id']]);
            $newBadges[] = $badge['name'];
        }
        
        return $newBadges;
    }
}
?>

