<?php
/**
 * DailyHealthy - Classe Auth
 * Sistema de autenticação e sessões
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class Auth {
    
    /**
     * Fazer login do usuário
     */
    public static function login($email, $password) {
        // Validar email
        if (!validateEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email inválido'
            ];
        }
        
        // Buscar usuário
        $user = User::findByEmail($email);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuário não encontrado'
            ];
        }
        
        // Verificar senha
        if (!User::verifyPassword($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Senha incorreta'
            ];
        }
        
        // Criar sessão
        self::createSession($user);
        
        // Atualizar última atividade
        User::updateLastActivity($user['id']);
        
        return [
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'points' => $user['points'],
                'streak' => $user['streak']
            ]
        ];
    }
    
    /**
     * Registrar novo usuário
     */
    public static function register($name, $email, $password, $confirmPassword) {
        // Validações
        if (empty($name) || strlen($name) < 2) {
            return [
                'success' => false,
                'message' => 'Nome deve ter pelo menos 2 caracteres'
            ];
        }
        
        if (!validateEmail($email)) {
            return [
                'success' => false,
                'message' => 'Email inválido'
            ];
        }
        
        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => 'Senha deve ter pelo menos 6 caracteres'
            ];
        }
        
        if ($password !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Senhas não coincidem'
            ];
        }
        
        // Verificar se email já existe
        $existingUser = User::findByEmail($email);
        if ($existingUser) {
            return [
                'success' => false,
                'message' => 'Email já está em uso'
            ];
        }
        
        try {
            // Criar usuário
            $userId = User::create([
                'name' => sanitize($name),
                'email' => sanitize($email),
                'password' => $password
            ]);
            
            if ($userId) {
                // Buscar usuário criado
                $user = User::findById($userId);
                
                // Criar sessão
                self::createSession($user);
                
                // Dar badge de boas-vindas
                $sql = "SELECT id FROM badges WHERE name = 'Bem-vindo'";
                $badge = Database::fetchOne($sql);
                if ($badge) {
                    $sql = "INSERT INTO user_badges (user_id, badge_id) VALUES (?, ?)";
                    Database::execute($sql, [$userId, $badge['id']]);
                }
                
                return [
                    'success' => true,
                    'message' => 'Usuário criado com sucesso',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'points' => $user['points'],
                        'streak' => $user['streak']
                    ]
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Erro desconhecido ao criar usuário'
        ];
    }
    
    /**
     * Fazer logout
     */
    public static function logout() {
        // Limpar dados da sessão
        $_SESSION = [];
        
        // Destruir cookie de sessão
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destruir sessão
        session_destroy();
        
        return [
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ];
    }
    
    /**
     * Verificar se usuário está logado
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Obter usuário atual da sessão
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        // Verificar se dados do usuário estão na sessão
        if (isset($_SESSION['user_data'])) {
            return $_SESSION['user_data'];
        }
        
        // Buscar dados atualizados do banco
        $user = User::findById($_SESSION['user_id']);
        if ($user) {
            $_SESSION['user_data'] = $user;
            return $user;
        }
        
        // Se usuário não existe mais, fazer logout
        self::logout();
        return null;
    }
    
    /**
     * Obter ID do usuário atual
     */
    public static function getCurrentUserId() {
        return self::isLoggedIn() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Criar sessão do usuário
     */
    private static function createSession($user) {
        // Regenerar ID da sessão por segurança
        session_regenerate_id(true);
        
        // Armazenar dados na sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_data'] = $user;
        $_SESSION['login_time'] = time();
        $_SESSION['csrf_token'] = generateCSRFToken();
        
        // Configurar tempo de vida da sessão
        ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    }
    
    /**
     * Atualizar dados do usuário na sessão
     */
    public static function updateSessionUser() {
        if (!self::isLoggedIn()) {
            return false;
        }
        
        $user = User::findById($_SESSION['user_id']);
        if ($user) {
            $_SESSION['user_data'] = $user;
            return true;
        }
        
        return false;
    }
    
    /**
     * Middleware para verificar autenticação
     */
    public static function requireAuth() {
        if (!self::isLoggedIn()) {
            // Se for requisição AJAX, retornar JSON
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
                jsonResponse(false, null, 'Usuário não autenticado');
            }
            
            // Redirecionar para login
            header('Location: ' . BASE_URL . 'login.php');
            exit;
        }
        
        // Verificar se sessão não expirou
        if (isset($_SESSION['login_time']) && 
            (time() - $_SESSION['login_time']) > SESSION_LIFETIME) {
            
            self::logout();
            
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                
                jsonResponse(false, null, 'Sessão expirada');
            }
            
            header('Location: ' . BASE_URL . 'login.php?expired=1');
            exit;
        }
    }
    
    /**
     * Middleware para verificar se usuário NÃO está logado
     */
    public static function requireGuest() {
        if (self::isLoggedIn()) {
            header('Location: ' . BASE_URL . 'dashboard.php');
            exit;
        }
    }
    
    /**
     * Verificar token CSRF
     */
    public static function verifyCsrfToken($token) {
        return verifyCSRFToken($token);
    }
    
    /**
     * Gerar token CSRF
     */
    public static function getCsrfToken() {
        return generateCSRFToken();
    }
    
    /**
     * Alterar senha do usuário
     */
    public static function changePassword($userId, $currentPassword, $newPassword, $confirmPassword) {
        // Validações
        if (strlen($newPassword) < 6) {
            return [
                'success' => false,
                'message' => 'Nova senha deve ter pelo menos 6 caracteres'
            ];
        }
        
        if ($newPassword !== $confirmPassword) {
            return [
                'success' => false,
                'message' => 'Senhas não coincidem'
            ];
        }
        
        // Buscar usuário
        $user = User::findById($userId);
        if (!$user) {
            return [
                'success' => false,
                'message' => 'Usuário não encontrado'
            ];
        }
        
        // Verificar senha atual
        if (!User::verifyPassword($currentPassword, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Senha atual incorreta'
            ];
        }
        
        try {
            // Atualizar senha
            $hashedPassword = password_hash($newPassword, HASH_ALGO);
            $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
            Database::execute($sql, [$hashedPassword, $userId]);
            
            return [
                'success' => true,
                'message' => 'Senha alterada com sucesso'
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao alterar senha: ' . $e->getMessage()
            ];
        }
    }
}
?>

