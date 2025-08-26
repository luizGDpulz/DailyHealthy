<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

require_once "config.php";
require_once "src/controllers/AuthController.php";
require_once "src/controllers/HabitController.php";

$request_method = $_SERVER["REQUEST_METHOD"];
$request_uri = $_SERVER["REQUEST_URI"];

$path = parse_url($request_uri, PHP_URL_PATH);
$path_parts = explode("/", trim($path, "/"));

// Remove 'dailyhealthy' and 'backend' from URI if present, assuming it's hosted under /dailyhealthy/backend/
if (isset($path_parts[0]) && $path_parts[0] === 'dailyhealthy') {
    array_shift($path_parts); // remove 'dailyhealthy'
}
if (isset($path_parts[0]) && $path_parts[0] === 'backend') {
    array_shift($path_parts); // remove 'backend'
}

// Now, assume the next parts are api/v1/controller/action/id
if (isset($path_parts[0]) && $path_parts[0] === 'api' && isset($path_parts[1]) && $path_parts[1] === 'v1') {
    array_shift($path_parts); // remove 'api'
    array_shift($path_parts); // remove 'v1'
}

$controller = $path_parts[0] ?? '';
$action = $path_parts[1] ?? '';
$id = $path_parts[2] ?? '';

switch ($controller) {
    case 'auth':
        $authController = new AuthController();
        if ($action === 'register' && $request_method === 'POST') {
            $authController->register();
        } elseif ($action === 'login' && $request_method === 'POST') {
            $authController->login();
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Auth endpoint not found."));
        }
        break;
        
    case 'habits':
        $habitController = new HabitController();
        if ($request_method === 'GET' && empty($action)) {
            $habitController->index();
        } elseif ($request_method === 'POST' && empty($action)) {
            $habitController->create();
        } elseif ($request_method === 'GET' && !empty($action) && $action !== 'execute') {
            $habitController->show($action);
        } elseif ($request_method === 'PUT' && !empty($action)) {
            $habitController->update($action);
        } elseif ($request_method === 'DELETE' && !empty($action)) {
            $habitController->delete($action);
        } elseif ($request_method === 'POST' && $action === 'execute' && !empty($id)) {
            $habitController->execute($id);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Habit endpoint not found."));
        }
        break;
        
    case 'users':
        if ($request_method === 'GET' && !empty($action) && $id === 'executions') {
            $habitController = new HabitController();
            $habitController->getUserExecutions($action);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "User endpoint not found."));
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(array("message" => "Endpoint not found."));
        break;
}

?>

