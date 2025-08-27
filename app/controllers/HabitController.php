<?php
class HabitController {
    private $habitModel;
    private $badgeModel;
    
    public function __construct() {
        $this->habitModel = new Habit();
        $this->badgeModel = new Badge();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        $status = $_GET['status'] ?? 'active';
        
        $habits = $this->habitModel->getUserHabits($userId, $status);
        include __DIR__ . '/../../public/habits.php';
    }
    
    public function create() {
        include __DIR__ . '/../../public/habits_create.php';
    }
    
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        if (!validateCsrfToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Invalid CSRF token');
            header('Location: /DailyHealthy/public/habits/create');
            exit;
        }
        
        $data = [
            'user_id' => $_SESSION['user_id'],
            'title' => sanitizeInput($_POST['title']),
            'description' => sanitizeInput($_POST['description']),
            'base_points' => (int) $_POST['base_points'],
            'frequency' => $_POST['frequency']
        ];
        
        if ($this->habitModel->create($data)) {
            // Check for badges (e.g., "Habits Created" milestone)
            $newBadges = $this->badgeModel->checkAndAward($_SESSION['user_id']);
            
            setFlashMessage('success', 'Habit created successfully');
            if ($newBadges) {
                setFlashMessage('badge', 'You earned new badges! Check your profile.');
            }
        } else {
            setFlashMessage('error', 'Error creating habit');
        }
        
        header('Location: /DailyHealthy/public/habits');
        exit;
    }
    
    public function execute() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $habitId = (int) $_POST['habit_id'];
        $userId = $_SESSION['user_id'];
        
        try {
            // Execute habit and get points earned
            $pointsEarned = $this->habitModel->execute($habitId, $userId);
            
            // Check for new badges
            $newBadges = $this->badgeModel->checkAndAward($userId);
            
            $response = [
                'success' => true,
                'points' => $pointsEarned,
                'message' => "Habit completed! You earned {$pointsEarned} points.",
                'newBadges' => $newBadges
            ];
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $habitId = (int) $_POST['id'];
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        if (!$this->habitModel->verifyOwnership($habitId, $userId)) {
            setFlashMessage('error', 'Unauthorized access');
            header('Location: /DailyHealthy/public/habits');
            exit;
        }
        
        $data = [
            'title' => sanitizeInput($_POST['title']),
            'description' => sanitizeInput($_POST['description']),
            'base_points' => (int) $_POST['base_points'],
            'frequency' => $_POST['frequency']
        ];
        
        if ($this->habitModel->update($habitId, $data)) {
            setFlashMessage('success', 'Habit updated successfully');
        } else {
            setFlashMessage('error', 'Error updating habit');
        }
        
        header('Location: /DailyHealthy/public/habits');
        exit;
    }
    
    public function archive() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $habitId = (int) $_POST['id'];
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        if (!$this->habitModel->verifyOwnership($habitId, $userId)) {
            setFlashMessage('error', 'Unauthorized access');
            header('Location: /DailyHealthy/public/habits');
            exit;
        }
        
        if ($this->habitModel->archive($habitId)) {
            setFlashMessage('success', 'Habit archived successfully');
        } else {
            setFlashMessage('error', 'Error archiving habit');
        }
        
        header('Location: /DailyHealthy/public/habits');
        exit;
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        $habitId = (int) $_POST['id'];
        $userId = $_SESSION['user_id'];
        
        // Verify ownership
        if (!$this->habitModel->verifyOwnership($habitId, $userId)) {
            setFlashMessage('error', 'Unauthorized access');
            header('Location: /DailyHealthy/public/habits');
            exit;
        }
        
        if ($this->habitModel->delete($habitId)) {
            setFlashMessage('success', 'Habit deleted successfully');
        } else {
            setFlashMessage('error', 'Error deleting habit');
        }
        
        header('Location: /DailyHealthy/public/habits');
        exit;
    }
}
