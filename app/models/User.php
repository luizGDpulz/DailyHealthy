<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO users (name, email, password)
            VALUES (?, ?, ?)
        ");
        
        $stmt->execute([
            $data['name'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT)
        ]);
        
        return $this->db->lastInsertId();
    }
    
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        
        return false;
    }
    
    public function updatePoints($userId, $points) {
        $stmt = $this->db->prepare("
            UPDATE users 
            SET points = points + ?,
                level = CASE
                    WHEN (points + ?) >= 2500 THEN 'diamond'
                    WHEN (points + ?) >= 1000 THEN 'gold'
                    WHEN (points + ?) >= 300 THEN 'silver'
                    ELSE 'bronze'
                END
            WHERE id = ?
        ");
        
        return $stmt->execute([$points, $points, $points, $points, $userId]);
    }
    
    public function updateStreak($userId, $increment = true) {
        if ($increment) {
            $sql = "
                UPDATE users 
                SET current_streak = current_streak + 1,
                    best_streak = GREATEST(current_streak + 1, best_streak)
                WHERE id = ?
            ";
        } else {
            $sql = "UPDATE users SET current_streak = 0 WHERE id = ?";
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId]);
    }
    
    public function getRanking($limit = 10) {
        $stmt = $this->db->prepare("
            SELECT id, name, points, level, current_streak 
            FROM users 
            ORDER BY points DESC 
            LIMIT ?
        ");
        
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
