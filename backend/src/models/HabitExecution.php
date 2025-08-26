<?php

class HabitExecution {
    private $conn;
    private $table_name = "habit_executions";

    public $id;
    public $user_id;
    public $habit_id;
    public $executed_at;
    public $points_awarded;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " (user_id, habit_id, executed_at, points_awarded) VALUES (:user_id, :habit_id, :executed_at, :points_awarded)";

        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->habit_id = htmlspecialchars(strip_tags($this->habit_id));
        $this->executed_at = htmlspecialchars(strip_tags($this->executed_at));
        $this->points_awarded = htmlspecialchars(strip_tags($this->points_awarded));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":habit_id", $this->habit_id);
        $stmt->bindParam(":executed_at", $this->executed_at);
        $stmt->bindParam(":points_awarded", $this->points_awarded);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function checkTodayExecution($user_id, $habit_id, $date) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE user_id = :user_id AND habit_id = :habit_id AND executed_at = :date";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":habit_id", $habit_id);
        $stmt->bindParam(":date", $date);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'] > 0;
    }

    function getStreak($user_id, $habit_id) {
        $query = "SELECT executed_at FROM " . $this->table_name . " WHERE user_id = :user_id AND habit_id = :habit_id ORDER BY executed_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":habit_id", $habit_id);
        $stmt->execute();

        $executions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($executions)) {
            return 0;
        }

        $streak = 1;
        $currentDate = new DateTime($executions[0]['executed_at']);
        
        for ($i = 1; $i < count($executions); $i++) {
            $prevDate = new DateTime($executions[$i]['executed_at']);
            $diff = $currentDate->diff($prevDate)->days;
            
            if ($diff == 1) {
                $streak++;
                $currentDate = $prevDate;
            } else {
                break;
            }
        }

        return $streak;
    }

    function getUserExecutions($user_id, $from_date = null, $to_date = null) {
        $query = "SELECT he.*, h.title, h.description, h.points_base 
                  FROM " . $this->table_name . " he 
                  JOIN habits h ON he.habit_id = h.id 
                  WHERE he.user_id = :user_id";
        
        if ($from_date && $to_date) {
            $query .= " AND he.executed_at BETWEEN :from_date AND :to_date";
        }
        
        $query .= " ORDER BY he.executed_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        if ($from_date && $to_date) {
            $stmt->bindParam(":from_date", $from_date);
            $stmt->bindParam(":to_date", $to_date);
        }
        
        $stmt->execute();
        return $stmt;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND habit_id = :habit_id AND executed_at = :executed_at";

        $stmt = $this->conn->prepare($query);

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->habit_id = htmlspecialchars(strip_tags($this->habit_id));
        $this->executed_at = htmlspecialchars(strip_tags($this->executed_at));

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":habit_id", $this->habit_id);
        $stmt->bindParam(":executed_at", $this->executed_at);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?>

