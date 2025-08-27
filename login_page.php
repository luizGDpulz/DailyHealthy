<?php
/**
 * DailyHealthy - Página Inicial/Login
 */

require_once __DIR__ . 
'/config/config.php';
require_once __DIR__ . 
'/app/Auth.php';
require_once __DIR__ . 
'/includes/functions.php'; // Incluir funções auxiliares

// Se já estiver logado, redirecionar para dashboard
Auth::requireGuest();

$pageTitle = 'Login - ' . SITE_NAME;
$pageDescription = SITE_DESCRIPTION;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <meta name="description" content="<?php echo $pageDescription; ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="container-sm">
            <div class="login-card">
                <div class="card">
                    <div class="card-header text-center">
                        <div class="logo-large">
                            <div class="logo-icon-large">
                                🎯
                            </div>
                            <h1 class="logo-text"><?php echo SITE_NAME; ?></h1>
                            <p class="logo-description"><?php echo SITE_DESCRIPTION; ?></p>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <form id="login-form" class="login-form">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    class="form-input" 
                                    placeholder="seu@email.com"
                                    required
                                    autocomplete="email"
                                >
                            </div>
                            
                            <div class="form-group">
                                <label for="password" class="form-label">Senha</label>
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password" 
                                    class="form-input" 
                                    placeholder="Sua senha"
                                    required
                                    autocomplete="current-password"
                                >
                            </div>
                            
                            <button type="submit" class="btn btn-primary btn-full btn-lg">
                                Entrar
                            </button>
                        </form>
                        
                        <div class="login-demo">
                            <div class="demo-info">
                                <h4>🚀 Demonstração</h4>
                                <p>Use as credenciais abaixo para testar:</p>
                                <div class="demo-credentials">
                                    <strong>Email:</strong> admin@dailyhealthy.com<br>
                                    <strong>Senha:</strong> admin123
                                </div>
                            </div>
                        </div>
                        
                        <div class="login-footer">
                            <p class="text-center">
                                Não tem uma conta? 
                                <a href="#" onclick="showRegisterForm()">Criar conta</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro -->
    <div id="register-modal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Criar Nova Conta</h3>
                <button type="button" class="modal-close" onclick="hideRegisterForm()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="register-form">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                    
                    <div class="form-group">
                        <label for="reg-name" class="form-label">Nome Completo</label>
                        <input 
                            type="text" 
                            id="reg-name" 
                            name="name" 
                            class="form-input" 
                            placeholder="Seu nome completo"
                            required
                            autocomplete="name"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-email" class="form-label">Email</label>
                        <input 
                            type="email" 
                            id="reg-email" 
                            name="email" 
                            class="form-input" 
                            placeholder="seu@email.com"
                            required
                            autocomplete="email"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-password" class="form-label">Senha</label>
                        <input 
                            type="password" 
                            id="reg-password" 
                            name="password" 
                            class="form-input" 
                            placeholder="Mínimo 6 caracteres"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="reg-confirm-password" class="form-label">Confirmar Senha</label>
                        <input 
                            type="password" 
                            id="reg-confirm-password" 
                            name="confirmPassword" 
                            class="form-input" 
                            placeholder="Confirme sua senha"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="hideRegisterForm()">
                    Cancelar
                </button>
                <button type="submit" form="register-form" class="btn btn-primary">
                    Criar Conta
                </button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/app.js"></script>
    
    <script>
        // Funções específicas da página de login
        function showRegisterForm() {
            document.getElementById(\'register-modal\').classList.add(\'active\');
            document.body.style.overflow = \'\';
        }
        
        function hideRegisterForm() {
            document.getElementById(\'register-modal\').classList.remove(\'active\');
            document.body.style.overflow = \'\';
            document.getElementById(\'register-form\').reset();
        }
        
        // Event listener para formulário de registro
        document.getElementById(\'register-form\').addEventListener(\'submit\', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const name = formData.get(\'name\');
            const email = formData.get(\'email\');
            const password = formData.get(\'password\');
            const confirmPassword = formData.get(\'confirmPassword\');
            
            // Validações
            if (!name || !email || !password || !confirmPassword) {
                DailyHealthy.Utils.showNotification(\'Por favor, preencha todos os campos\', \'error\');
                return;
            }
            
            if (password !== confirmPassword) {
                DailyHealthy.Utils.showNotification(\'As senhas não coincidem\', \'error\');
                return;
            }
            
            if (password.length < 6) {
                DailyHealthy.Utils.showNotification(\'A senha deve ter pelo menos 6 caracteres\', \'error\');
                return;
            }
            
            // Desabilitar botão
            const submitBtn = this.querySelector(\'button[type="submit"]\');
            const originalText = submitBtn.textContent;
            submitBtn.disabled = true;
            submitBtn.innerHTML = \'<span class="loading"></span> Criando...\';
            
            try {
                await DailyHealthy.Auth.register(name, email, password, confirmPassword);
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
        
        // Fechar modal com ESC
        document.addEventListener(\'keydown\', function(e) {
            if (e.key === \'Escape\') {
                hideRegisterForm();
            }
        });
        
        // Fechar modal clicando fora
        document.getElementById(\'register-modal\').addEventListener(\'click\', function(e) {
            if (e.target === this) {
                hideRegisterForm();
            }
        });
    </script>
    
    <style>
        /* Estilos específicos da página de login */
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: var(--spacing-lg);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .login-card {
            width: 100%;
            max-width: 400px;
        }
        
        .logo-large {
            margin-bottom: var(--spacing-lg);
        }
        
        .logo-icon-large {
            width: 80px;
            height: 80px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto var(--spacing-md);
            box-shadow: var(--shadow-lg);
        }
        
        .logo-text {
            color: var(--primary-color);
            margin-bottom: var(--spacing-sm);
            font-size: 2rem;
        }
        
        .logo-description {
            color: var(--gray-600);
            margin-bottom: 0;
        }
        
        .login-demo {
            margin: var(--spacing-xl) 0;
            padding: var(--spacing-lg);
            background: rgba(33, 150, 243, 0.1);
            border-radius: var(--border-radius);
            border-left: 4px solid var(--secondary-color);
        }
        
        .demo-info h4 {
            color: var(--secondary-color);
            margin-bottom: var(--spacing-sm);
            font-size: 1rem;
        }
        
        .demo-info p {
            margin-bottom: var(--spacing-sm);
            font-size: 0.875rem;
        }
        
        .demo-credentials {
            font-family: \'Courier New\', monospace;
            font-size: 0.875rem;
            background: var(--white);
            padding: var(--spacing-sm);
            border-radius: var(--border-radius);
            border: 1px solid var(--gray-200);
        }
        
        .login-footer {
            margin-top: var(--spacing-xl);
            padding-top: var(--spacing-lg);
            border-top: 1px solid var(--gray-200);
        }
        
        .login-footer p {
            margin: 0;
            color: var(--gray-600);
            font-size: 0.875rem;
        }
        
        .login-footer a {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .login-footer a:hover {
            color: var(--primary-dark);
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: var(--spacing-md);
            }
            
            .logo-icon-large {
                width: 60px;
                height: 60px;
                font-size: 1.5rem;
            }
            
            .logo-text {
                font-size: 1.75rem;
            }
        }
    </style>
</body>
</html>

