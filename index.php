<?php
/**
 * DailyHealthy - Roteador Principal
 * Este arquivo atua como o "front controller", direcionando todas as requisições.
 */

// Iniciar sessão PHP (necessário para autenticação)
session_start();

// Incluir configurações e classes essenciais
require_once __DIR__ . 
'/config/config.php';
require_once __DIR__ . 
'/app/Auth.php';
require_once __DIR__ . 
'/app/Database.php';
require_once __DIR__ . 
'/app/User.php';
require_once __DIR__ . 
'/app/Habit.php';
require_once __DIR__ . 
'/includes/functions.php'; // Incluir funções auxiliares

// Obter o caminho da requisição (URL amigável)
$requestUri = trim(parse_url($_SERVER[
'REQUEST_URI'
], PHP_URL_PATH), 
'/'
);

// Remover o subdiretório se a aplicação estiver em um (ex: /dailyhealthy/)
$basePath = 
'dailyhealthy'
; // Altere para o nome da sua pasta no htdocs
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = trim(substr($requestUri, strlen($basePath)), 
'/'
);
}

// Roteamento
switch ($requestUri) {
    case 
''
: // Página inicial (login)
    case 
'index'
:
    case 
'login'
:
        require __DIR__ . 
'/login_page.php'
; // A página de login original
        break;

    case 
'dashboard'
:
        Auth::requireAuth(); // Requer autenticação para acessar o dashboard
        require __DIR__ . 
'/dashboard.php'
;
        break;

    case 
'ranking'
:
        Auth::requireAuth(); // Requer autenticação para acessar o ranking
        require __DIR__ . 
'/ranking.php'
;
        break;

    case 
'logout'
:
        require __DIR__ . 
'/logout.php'
;
        break;

    // Rotas da API
    case 
'api/auth'
:
        require __DIR__ . 
'/api/auth.php'
;
        break;
    case 
'api/habits'
:
        require __DIR__ . 
'/api/habits.php'
;
        break;
    case 
'api/ranking'
:
        require __DIR__ . 
'/api/ranking.php'
;
        break;
    case 
'api/badges'
:
        require __DIR__ . 
'/api/badges.php'
;
        break;

    case 
'setup'
:
        require __DIR__ . 
'/setup.php'
;
        break;

    case 
'verificar'
:
        require __DIR__ . 
'/verificar.php'
;
        break;

    default:
        // Se a rota não for encontrada, retorna 404
        http_response_code(404);
        echo 
'<h1>404 - Página Não Encontrada</h1><p>A página que você está procurando não existe.</p>'
;
        break;
}

?>

