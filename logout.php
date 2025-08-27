<?php
/**
 * DailyHealthy - Página de Logout
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/Auth.php';

// Fazer logout
Auth::logout();

// Redirecionar para página inicial
header('Location: index.php');
exit;
?>

