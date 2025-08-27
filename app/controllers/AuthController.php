<?php
class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function loginPage() {
        include __DIR__ . '/../../public/login.php';
    }
    
    public function registerPage() {
        include __DIR__ . '/../../public/register.php';
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        if (!validateCsrfToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Invalid CSRF token');
            header('Location: /dailyhealthy/public/login');
            exit;
        }
        
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        $user = $this->userModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_level'] = $user['level'];
            
            header('Location: /dailyhealthy/public/');
        } else {
            setFlashMessage('error', 'Invalid email or password');
            header('Location: /dailyhealthy/public/login');
        }
        exit;
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        if (!validateCsrfToken($_POST['csrf_token'])) {
            setFlashMessage('error', 'Invalid CSRF token');
            header('Location: /DailyHealthy/public/register');
            exit;
        }
        
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
            'password' => $_POST['password']
        ];
        
        // Validate input
        if (!validateEmail($data['email'])) {
            setFlashMessage('error', 'Invalid email format');
            header('Location: /DailyHealthy/public/register');
            exit;
        }
        
        if (!isPasswordStrong($data['password'])) {
            setFlashMessage('error', 'Password must be at least 8 characters and contain uppercase, lowercase, and numbers');
            header('Location: /dailyhealthy/public/register');
            exit;
        }
        
        try {
            $userId = $this->userModel->create($data);
            
            if ($userId) {
                setFlashMessage('success', 'Account created successfully. Please login.');
                header('Location: /dailyhealthy/public/login');
            } else {
                setFlashMessage('error', 'Error creating account');
                header('Location: /dailyhealthy/public/register');
            }
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                setFlashMessage('error', 'Email already registered');
            } else {
                setFlashMessage('error', 'Database error');
            }
            header('Location: /dailyhealthy/public/register');
        }
        exit;
    }
    
    public function logout() {
        session_destroy();
        header('Location: /dailyhealthy/public/login');
        exit;
    }
}
