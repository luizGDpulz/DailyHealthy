<?php
class DashboardController {
    private $habitModel;
    private $userModel;
    private $badgeModel;
    
    public function __construct() {
        $this->habitModel = new Habit();
        $this->userModel = new User();
        $this->badgeModel = new Badge();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get today's habits
        $habits = $this->habitModel->getUserHabits($userId);
        
        // Get user stats
        $stats = $this->habitModel->getStats($userId);
        
        // Get user's badges
        $badges = $this->badgeModel->getUserBadges($userId);
        
        // Get top 5 users for sidebar
        $topUsers = $this->userModel->getRanking(5);
        
        include __DIR__ . '/../../public/dashboard.php';
    }
    
    public function profile() {
        $userId = $_SESSION['user_id'];
        
        // Get user stats
        $stats = $this->habitModel->getStats($userId);
        
        // Get all user's badges
        $badges = $this->badgeModel->getUserBadges($userId);
        
        // Get user's rank position
        $ranking = $this->userModel->getRanking();
        $userRank = array_search($userId, array_column($ranking, 'id')) + 1;
        
        include __DIR__ . '/../../public/profile.php';
    }
    
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)
        ];
        
        // Handle avatar upload if present
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar = $this->handleAvatarUpload($_FILES['avatar']);
            if ($avatar) {
                $data['avatar'] = $avatar;
            }
        }
        
        // Update user profile
        if ($this->userModel->update($userId, $data)) {
            setFlashMessage('success', 'Profile updated successfully');
        } else {
            setFlashMessage('error', 'Error updating profile');
        }
        
        header('Location: /DailyHealthy/public/profile');
        exit;
    }
    
    private function handleAvatarUpload($file) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            setFlashMessage('error', 'Invalid file type. Only JPG, PNG and GIF are allowed.');
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            setFlashMessage('error', 'File too large. Maximum size is 5MB.');
            return false;
        }
        
        $fileName = uniqid() . '_' . basename($file['name']);
        $uploadPath = __DIR__ . '/../../public/assets/images/avatars/' . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            return $fileName;
        }
        
        setFlashMessage('error', 'Error uploading file.');
        return false;
    }
}
