<?php
class Badge {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function checkAndAward($userId) {
        $badges = $this->getAvailableBadges($userId);
        $awarded = [];
        
        foreach ($badges as $badge) {
            if ($this->qualifiesForBadge($userId, $badge)) {
                $this->awardBadge($userId, $badge['id']);
                $awarded[] = $badge;
            }
        }
        
        return $awarded;
    }
    
    private function getAvailableBadges($userId) {
        $stmt = $this->db->prepare("
            SELECT b.* 
            FROM badges b
            LEFT JOIN user_badges ub ON b.id = ub.badge_id AND ub.user_id = ?
            WHERE ub.id IS NULL
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    private function qualifiesForBadge($userId, $badge) {
        $value = 0;
        
        switch ($badge['condition_type']) {
            case 'first_habit_completed':
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count 
                    FROM habit_executions 
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);
                $value = $stmt->fetch()['count'];
                break;
                
            case 'streak_7_days':
            case 'streak_30_days':
                $stmt = $this->db->prepare("
                    SELECT current_streak 
                    FROM users 
                    WHERE id = ?
                ");
                $stmt->execute([$userId]);
                $value = $stmt->fetch()['current_streak'];
                break;
                
            case 'habits_created':
                $stmt = $this->db->prepare("
                    SELECT COUNT(*) as count 
                    FROM habits 
                    WHERE user_id = ?
                ");
                $stmt->execute([$userId]);
                $value = $stmt->fetch()['count'];
                break;
                
            case 'points_earned':
                $stmt = $this->db->prepare("
                    SELECT points 
                    FROM users 
                    WHERE id = ?
                ");
                $stmt->execute([$userId]);
                $value = $stmt->fetch()['points'];
                break;
        }
        
        return $value >= $badge['condition_value'];
    }
    
    private function awardBadge($userId, $badgeId) {
        $stmt = $this->db->prepare("
            INSERT INTO user_badges (user_id, badge_id)
            VALUES (?, ?)
        ");
        
        return $stmt->execute([$userId, $badgeId]);
    }
    
    public function getUserBadges($userId) {
        $stmt = $this->db->prepare("
            SELECT b.*, ub.earned_at
            FROM badges b
            INNER JOIN user_badges ub ON b.id = ub.badge_id
            WHERE ub.user_id = ?
            ORDER BY ub.earned_at DESC
        ");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
