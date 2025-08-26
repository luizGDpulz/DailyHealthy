<?php

require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $conn;
    private $user;

    public function __construct() {
        $this->conn = getDbConnection();
        $this->user = new User($this->conn);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->name) && !empty($data->email) && !empty($data->password)) {
            $this->user->name = $data->name;
            $this->user->email = $data->email;
            $this->user->password = $data->password;

            if ($this->user->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "User was successfully registered."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to register user."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to register user. Data is incomplete."));
        }
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->email) && !empty($data->password)) {
            $this->user->email = $data->email;

            if ($this->user->findByEmail()) {
                if (password_verify($data->password, $this->user->password)) {
                    http_response_code(200);
                    echo json_encode(array(
                        "message" => "Successful login.",
                        "id" => $this->user->id,
                        "name" => $this->user->name,
                        "email" => $this->user->email,
                        "points" => $this->user->points
                    ));
                } else {
                    http_response_code(401);
                    echo json_encode(array("message" => "Login failed. Wrong password."));
                }
            } else {
                http_response_code(404);
                echo json_encode(array("message" => "Login failed. User not found."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to login. Data is incomplete."));
        }
    }
}

?>

