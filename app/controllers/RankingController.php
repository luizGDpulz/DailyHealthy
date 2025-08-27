<?php
class RankingController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function index() {
        // Get full ranking (default 50 users)
        $users = $this->userModel->getRanking(50);
        
        // Get current user's rank
        $currentUserId = $_SESSION['user_id'];
        $userRank = 0;
        foreach ($users as $index => $user) {
            if ($user['id'] == $currentUserId) {
                $userRank = $index + 1;
                break;
            }
        }
        
        include __DIR__ . '/../../public/ranking.php';
    }
}
