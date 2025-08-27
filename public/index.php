<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Database.php';
require_once __DIR__ . '/../app/helpers/functions.php';

// Auto-load models and controllers
spl_autoload_register(function ($class) {
    $modelPath = __DIR__ . "/../app/models/{$class}.php";
    $controllerPath = __DIR__ . "/../app/controllers/{$class}.php";
    
    if (file_exists($modelPath)) {
        require_once $modelPath;
    } elseif (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
});

// Parse URL e força lowercase
$url = strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$url = str_replace('/dailyhealthy/public', '', $url);

// Se a URL estiver vazia, redireciona para a raiz
if (empty($url)) {
    $url = '/';
}

// Rotas de instalação (disponíveis mesmo sem autenticação)
$installRoutes = [
    '/install' => ['InstallController', 'index'],
    '/install/run' => ['InstallController', 'runMigrations'],
    '/check' => null // Rota especial para o diagnóstico do sistema
];

// Route definitions
$routes = [
    '/' => ['DashboardController', 'index'],
    '/login' => ['AuthController', 'loginPage'],
    '/auth/login' => ['AuthController', 'login'],
    '/register' => ['AuthController', 'registerPage'],
    '/auth/register' => ['AuthController', 'register'],
    '/auth/logout' => ['AuthController', 'logout'],
    '/habits' => ['HabitController', 'index'],
    '/habits/create' => ['HabitController', 'create'],
    '/habits/store' => ['HabitController', 'store'],
    '/habits/execute' => ['HabitController', 'execute'],
    '/ranking' => ['RankingController', 'index'],
    '/profile' => ['DashboardController', 'profile']
];

// Check if it's an installation route
if (isset($installRoutes[$url])) {
    if ($url === '/check') {
        include 'check.php';
        exit;
    }
    list($controller, $method) = $installRoutes[$url];
    $controllerInstance = new $controller();
    $controllerInstance->$method();
    exit;
}

// Check if route exists
if (isset($routes[$url])) {
    list($controller, $method) = $routes[$url];
    
    // Check if user is authenticated for protected routes
    if ($url !== '/login' && $url !== '/register' && 
        $url !== '/auth/login' && $url !== '/auth/register' && 
        !isset($_SESSION['user_id'])) {
        header('Location: /DailyHealthy/public/login');
        exit;
    }
    
    // Execute controller method
    $controllerInstance = new $controller();
    $controllerInstance->$method();
} else {
    // 404 Not Found
    header("HTTP/1.0 404 Not Found");
    include '404.php';
}
