<?php
class Habit {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO habits (user_id, title, description, base_points, frequency)
            VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $data['user_id'],
            $data['title'],
            $data['description'],
            $data['base_points'] ?? 5,
            $data['frequency'] ?? 'daily'
        ]);
    }
    
    public function getUserHabits($userId, $status = 'active') {
        $stmt = $this->db->prepare("
            SELECT h.*, 
                   (SELECT COUNT(*) 
                    FROM habit_executions he 
                    WHERE he.habit_id = h.id 
                    AND DATE(he.executed_at) = CURDATE()) as executed_today
            FROM habits h
            WHERE h.user_id = ? AND h.status = ?
            ORDER BY h.created_at DESC
        ");
        
        $stmt->execute([$userId, $status]);
        return $stmt->fetchAll();
    }
    
    public function execute($habitId, $userId) {
        // Start transaction
        $this->db->beginTransaction();
        
        try {
            // Get habit details
            $stmt = $this->db->prepare("SELECT * FROM habits WHERE id = ? AND user_id = ?");
            $stmt->execute([$habitId, $userId]);
            $habit = $stmt->fetch();
            
            if (!$habit) {
                throw new Exception("Habit not found");
            }
            
            // Calculate points with streak bonus
            $userStmt = $this->db->prepare("SELECT current_streak, level FROM users WHERE id = ?");
            $userStmt->execute([$userId]);
            $user = $userStmt->fetch();
            
            $streakBonus = $user['current_streak'] * 2;
            $levelMultiplier = $this->getLevelMultiplier($user['level']);
            $totalPoints = ($habit['base_points'] + $streakBonus) * $levelMultiplier;
            
            // Record execution
            $execStmt = $this->db->prepare("
                INSERT INTO habit_executions (habit_id, user_id, points_earned)
                VALUES (?, ?, ?)
            ");
            $execStmt->execute([$habitId, $userId, $totalPoints]);
            
            // Update user points and streak
            $user = new User();
            $user->updatePoints($userId, $totalPoints);
            $user->updateStreak($userId, true);
            
            $this->db->commit();
            return $totalPoints;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    private function getLevelMultiplier($level) {
        switch ($level) {
            case 'diamond': return 2.0;
            case 'gold': return 1.5;
            case 'silver': return 1.2;
            default: return 1.0;
        }
    }
    
    public function getStats($userId) {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(DISTINCT h.id) as total_habits,
                COUNT(DISTINCT he.id) as total_executions,
                COALESCE(SUM(he.points_earned), 0) as total_points
            FROM habits h
            LEFT JOIN habit_executions he ON h.id = he.habit_id
            WHERE h.user_id = ?
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
}
