<?php

class Habit {
    private $conn;
    private $table_name = "habits";

    public $id;
    public $title;
    public $description;
    public $points_base;
    public $frequency;
    public $created_by;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " (title, description, points_base, frequency, created_by) VALUES (:title, :description, :points_base, :frequency, :created_by)";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->points_base = htmlspecialchars(strip_tags($this->points_base));
        $this->frequency = htmlspecialchars(strip_tags($this->frequency));
        $this->created_by = htmlspecialchars(strip_tags($this->created_by));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":points_base", $this->points_base);
        $stmt->bindParam(":frequency", $this->frequency);
        $stmt->bindParam(":created_by", $this->created_by);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function readAll() {
        $query = "SELECT id, title, description, points_base, frequency, created_by, created_at FROM " . $this->table_name . " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    function readOne() {
        $query = "SELECT id, title, description, points_base, frequency, created_by, created_at FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->title = $row["title"];
            $this->description = $row["description"];
            $this->points_base = $row["points_base"];
            $this->frequency = $row["frequency"];
            $this->created_by = $row["created_by"];
            $this->created_at = $row["created_at"];
            return true;
        }

        return false;
    }

    function update() {
        $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, points_base = :points_base, frequency = :frequency WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->description = htmlspecialchars(strip_tags($this->description));
        $this->points_base = htmlspecialchars(strip_tags($this->points_base));
        $this->frequency = htmlspecialchars(strip_tags($this->frequency));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":points_base", $this->points_base);
        $stmt->bindParam(":frequency", $this->frequency);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}

?>

