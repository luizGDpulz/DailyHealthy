<?php

require_once __DIR__ . '/../models/Habit.php';
require_once __DIR__ . '/../models/HabitExecution.php';
require_once __DIR__ . '/../models/User.php';

class HabitController {
    private $conn;
    private $habit;
    private $habitExecution;
    private $user;

    public function __construct() {
        $this->conn = getDbConnection();
        $this->habit = new Habit($this->conn);
        $this->habitExecution = new HabitExecution($this->conn);
        $this->user = new User($this->conn);
    }

    public function index() {
        $stmt = $this->habit->readAll();
        $habits = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $habits[] = $row;
        }

        http_response_code(200);
        echo json_encode($habits);
    }

    public function show($id) {
        $this->habit->id = $id;

        if ($this->habit->readOne()) {
            $habit_arr = array(
                "id" => $this->habit->id,
                "title" => $this->habit->title,
                "description" => $this->habit->description,
                "points_base" => $this->habit->points_base,
                "frequency" => $this->habit->frequency,
                "created_by" => $this->habit->created_by,
                "created_at" => $this->habit->created_at
            );

            http_response_code(200);
            echo json_encode($habit_arr);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Habit not found."));
        }
    }

    public function create() {
        $data = json_decode(file_get_contents("php://input"));

        if (!empty($data->title) && !empty($data->points_base)) {
            $this->habit->title = $data->title;
            $this->habit->description = $data->description ?? '';
            $this->habit->points_base = $data->points_base;
            $this->habit->frequency = $data->frequency ?? 'daily';
            $this->habit->created_by = $data->created_by ?? null;

            if ($this->habit->create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Habit was created successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create habit."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create habit. Data is incomplete."));
        }
    }

    public function update($id) {
        $data = json_decode(file_get_contents("php://input"));

        $this->habit->id = $id;

        if ($this->habit->readOne()) {
            $this->habit->title = $data->title ?? $this->habit->title;
            $this->habit->description = $data->description ?? $this->habit->description;
            $this->habit->points_base = $data->points_base ?? $this->habit->points_base;
            $this->habit->frequency = $data->frequency ?? $this->habit->frequency;

            if ($this->habit->update()) {
                http_response_code(200);
                echo json_encode(array("message" => "Habit was updated successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to update habit."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Habit not found."));
        }
    }

    public function delete($id) {
        $this->habit->id = $id;

        if ($this->habit->readOne()) {
            if ($this->habit->delete()) {
                http_response_code(200);
                echo json_encode(array("message" => "Habit was deleted successfully."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Unable to delete habit."));
            }
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Habit not found."));
        }
    }

    public function execute($habit_id) {
        $data = json_decode(file_get_contents("php://input"));
        $user_id = $data->user_id ?? null;
        $date = $data->date ?? date('Y-m-d');

        if (!$user_id) {
            http_response_code(400);
            echo json_encode(array("message" => "User ID is required."));
            return;
        }

        // Check if habit exists
        $this->habit->id = $habit_id;
        if (!$this->habit->readOne()) {
            http_response_code(404);
            echo json_encode(array("message" => "Habit not found."));
            return;
        }

        // Check if already executed today
        if ($this->habitExecution->checkTodayExecution($user_id, $habit_id, $date)) {
            http_response_code(409);
            echo json_encode(array("message" => "Habit already executed today."));
            return;
        }

        try {
            $this->conn->beginTransaction();

            // Create execution record
            $this->habitExecution->user_id = $user_id;
            $this->habitExecution->habit_id = $habit_id;
            $this->habitExecution->executed_at = $date;
            $this->habitExecution->points_awarded = $this->habit->points_base;

            if ($this->habitExecution->create()) {
                // Update user points
                $this->user->id = $user_id;
                if ($this->user->findByEmail() || $this->user->readOne()) {
                    $new_points = $this->user->points + $this->habit->points_base;
                    $this->user->updatePoints($new_points);
                }

                // Calculate streak
                $streak = $this->habitExecution->getStreak($user_id, $habit_id);

                // Calculate streak bonus
                $streak_bonus = 0;
                if ($streak == 3) $streak_bonus = 5;
                elseif ($streak == 7) $streak_bonus = 20;
                elseif ($streak == 14) $streak_bonus = 50;

                if ($streak_bonus > 0) {
                    $new_points += $streak_bonus;
                    $this->user->updatePoints($new_points);
                }

                $this->conn->commit();

                http_response_code(200);
                echo json_encode(array(
                    "success" => true,
                    "points_awarded" => $this->habit->points_base + $streak_bonus,
                    "streak" => $streak,
                    "new_points_total" => $new_points,
                    "badges_awarded" => array() // TODO: Implement badge system
                ));
            } else {
                $this->conn->rollback();
                http_response_code(503);
                echo json_encode(array("message" => "Unable to execute habit."));
            }
        } catch (Exception $e) {
            $this->conn->rollback();
            http_response_code(500);
            echo json_encode(array("message" => "Server error: " . $e->getMessage()));
        }
    }

    public function getUserExecutions($user_id) {
        $from_date = $_GET['from'] ?? null;
        $to_date = $_GET['to'] ?? null;

        $stmt = $this->habitExecution->getUserExecutions($user_id, $from_date, $to_date);
        $executions = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $executions[] = $row;
        }

        http_response_code(200);
        echo json_encode($executions);
    }
}

?>

