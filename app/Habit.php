<?php
/**
 * DailyHealthy - Classe Habit
 * Model para gerenciamento de hábitos
 */

require_once __DIR__ . '/Database.php';

class Habit {
    
    /**
     * Buscar hábitos do usuário
     */
    public static function getUserHabits($userId, $activeOnly = true) {
        $sql = "
            SELECT 
                h.*,
                (
                    SELECT COUNT(*) 
                    FROM habit_executions he 
                    WHERE he.habit_id = h.id 
                    AND he.execution_date = CURDATE()
                ) as completed_today
            FROM habits h
            WHERE h.user_id = ?
        ";
        
        $params = [$userId];
        
        if ($activeOnly) {
            $sql .= " AND h.is_active = 1";
        }
        
        $sql .= " ORDER BY h.created_at ASC";
        
        return Database::fetchAll($sql, $params);
    }
    
    /**
     * Buscar hábito por ID
     */
    public static function findById($id) {
        $sql = "SELECT * FROM habits WHERE id = ?";
        return Database::fetchOne($sql, [$id]);
    }
    
    /**
     * Criar novo hábito
     */
    public static function create($data) {
        $sql = "
            INSERT INTO habits (user_id, title, description, points_base, category, color, icon) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        
        return Database::insert($sql, [
            $data['user_id'],
            $data['title'],
            $data['description'] ?? '',
            $data['points_base'] ?? 10,
            $data['category'] ?? 'geral',
            $data['color'] ?? '#4CAF50',
            $data['icon'] ?? 'target'
        ]);
    }
    
    /**
     * Atualizar hábito
     */
    public static function update($id, $data) {
        $sql = "
            UPDATE habits 
            SET title = ?, description = ?, points_base = ?, category = ?, color = ?, icon = ?, updated_at = NOW()
            WHERE id = ?
        ";
        
        return Database::execute($sql, [
            $data['title'],
            $data['description'] ?? '',
            $data['points_base'] ?? 10,
            $data['category'] ?? 'geral',
            $data['color'] ?? '#4CAF50',
            $data['icon'] ?? 'target',
            $id
        ]);
    }
    
    /**
     * Desativar hábito
     */
    public static function deactivate($id) {
        $sql = "UPDATE habits SET is_active = 0, updated_at = NOW() WHERE id = ?";
        return Database::execute($sql, [$id]);
    }
    
    /**
     * Verificar se hábito foi executado hoje
     */
    public static function isCompletedToday($habitId, $userId) {
        $sql = "
            SELECT id FROM habit_executions 
            WHERE habit_id = ? AND user_id = ? AND execution_date = CURDATE()
        ";
        
        $result = Database::fetchOne($sql, [$habitId, $userId]);
        return $result !== false;
    }
    
    /**
     * Marcar hábito como executado
     */
    public static function markAsCompleted($habitId, $userId, $date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        // Verificar se já foi executado hoje
        $sql = "
            SELECT id FROM habit_executions 
            WHERE habit_id = ? AND user_id = ? AND execution_date = ?
        ";
        
        $existing = Database::fetchOne($sql, [$habitId, $userId, $date]);
        if ($existing) {
            return false; // Já foi executado
        }
        
        // Buscar pontos do hábito
        $habit = self::findById($habitId);
        if (!$habit) {
            return false;
        }
        
        try {
            Database::beginTransaction();
            
            // Inserir execução
            $sql = "
                INSERT INTO habit_executions (user_id, habit_id, execution_date, points_earned) 
                VALUES (?, ?, ?, ?)
            ";
            
            Database::execute($sql, [$userId, $habitId, $date, $habit['points_base']]);
            
            // Atualizar pontos do usuário
            require_once __DIR__ . '/User.php';
            User::updatePoints($userId, $habit['points_base']);
            User::updateLastActivity($userId, $date);
            
            // Recalcular streak
            User::calculateStreak($userId);
            
            // Verificar novos badges
            $newBadges = User::checkAndAwardBadges($userId);
            
            Database::commit();
            
            return [
                'success' => true,
                'points_earned' => $habit['points_base'],
                'new_badges' => $newBadges
            ];
            
        } catch (Exception $e) {
            Database::rollback();
            return false;
        }
    }
    
    /**
     * Desmarcar hábito (remover execução)
     */
    public static function markAsIncomplete($habitId, $userId, $date = null) {
        if ($date === null) {
            $date = date('Y-m-d');
        }
        
        // Buscar execução
        $sql = "
            SELECT * FROM habit_executions 
            WHERE habit_id = ? AND user_id = ? AND execution_date = ?
        ";
        
        $execution = Database::fetchOne($sql, [$habitId, $userId, $date]);
        if (!$execution) {
            return false; // Não foi executado
        }
        
        try {
            Database::beginTransaction();
            
            // Remover execução
            $sql = "
                DELETE FROM habit_executions 
                WHERE habit_id = ? AND user_id = ? AND execution_date = ?
            ";
            
            Database::execute($sql, [$habitId, $userId, $date]);
            
            // Remover pontos do usuário
            require_once __DIR__ . '/User.php';
            User::updatePoints($userId, -$execution['points_earned']);
            
            // Recalcular streak
            User::calculateStreak($userId);
            
            Database::commit();
            
            return [
                'success' => true,
                'points_removed' => $execution['points_earned']
            ];
            
        } catch (Exception $e) {
            Database::rollback();
            return false;
        }
    }
    
    /**
     * Obter histórico de execuções do hábito
     */
    public static function getExecutionHistory($habitId, $userId, $days = 30) {
        $sql = "
            SELECT 
                execution_date,
                points_earned,
                notes,
                created_at
            FROM habit_executions 
            WHERE habit_id = ? AND user_id = ?
            AND execution_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
            ORDER BY execution_date DESC
        ";
        
        return Database::fetchAll($sql, [$habitId, $userId, $days]);
    }
    
    /**
     * Obter estatísticas do hábito
     */
    public static function getHabitStats($habitId, $userId) {
        $habit = self::findById($habitId);
        if (!$habit) return null;
        
        // Total de execuções
        $sql = "SELECT COUNT(*) as total_executions FROM habit_executions WHERE habit_id = ? AND user_id = ?";
        $totalExecutions = Database::fetchOne($sql, [$habitId, $userId]);
        
        // Execuções este mês
        $sql = "
            SELECT COUNT(*) as month_executions 
            FROM habit_executions 
            WHERE habit_id = ? AND user_id = ?
            AND MONTH(execution_date) = MONTH(CURDATE())
            AND YEAR(execution_date) = YEAR(CURDATE())
        ";
        $monthExecutions = Database::fetchOne($sql, [$habitId, $userId]);
        
        // Streak atual do hábito
        $sql = "
            SELECT execution_date 
            FROM habit_executions 
            WHERE habit_id = ? AND user_id = ?
            AND execution_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY execution_date DESC
        ";
        
        $executions = Database::fetchAll($sql, [$habitId, $userId]);
        $streak = self::calculateHabitStreak($executions);
        
        // Total de pontos ganhos
        $sql = "SELECT SUM(points_earned) as total_points FROM habit_executions WHERE habit_id = ? AND user_id = ?";
        $totalPoints = Database::fetchOne($sql, [$habitId, $userId]);
        
        return [
            'habit' => $habit,
            'total_executions' => $totalExecutions['total_executions'],
            'month_executions' => $monthExecutions['month_executions'],
            'current_streak' => $streak,
            'total_points' => $totalPoints['total_points'] ?? 0
        ];
    }
    
    /**
     * Calcular streak específico do hábito
     */
    private static function calculateHabitStreak($executions) {
        if (empty($executions)) return 0;
        
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
        
        if (!$hasToday && !$hasYesterday) {
            return 0;
        }
        
        $checkDate = $hasToday ? $currentDate : $yesterday;
        
        foreach ($executions as $execution) {
            if ($execution['execution_date'] === $checkDate) {
                $streak++;
                $checkDate = date('Y-m-d', strtotime($checkDate . ' -1 day'));
            } else {
                break;
            }
        }
        
        return $streak;
    }
    
    /**
     * Obter hábitos mais populares
     */
    public static function getPopularHabits($limit = 10) {
        $sql = "
            SELECT 
                h.title,
                h.category,
                COUNT(DISTINCT h.user_id) as users_count,
                COUNT(he.id) as total_executions,
                AVG(h.points_base) as avg_points
            FROM habits h
            LEFT JOIN habit_executions he ON h.id = he.habit_id
            WHERE h.is_active = 1
            GROUP BY h.title, h.category
            HAVING users_count > 1
            ORDER BY users_count DESC, total_executions DESC
            LIMIT ?
        ";
        
        return Database::fetchAll($sql, [$limit]);
    }
}
?>

