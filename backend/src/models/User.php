<?php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $points;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    function create() {
        $query = "INSERT INTO " . $this->table_name . " (name, email, password_hash) VALUES (:name, :email, :password_hash)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));

        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":email", $this->email);
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(":password_hash", $password_hash);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    function findByEmail() {
        $query = "SELECT id, name, email, password_hash, points, created_at FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $this->email);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row["id"];
            $this->name = $row["name"];
            $this->email = $row["email"];
            $this->password = $row["password_hash"]; // This is the hashed password
            $this->points = $row["points"];
            $this->created_at = $row["created_at"];
            return true;
        }

        return false;
    }

    public function updatePoints($points) {
        $query = "UPDATE " . $this->table_name . " SET points = :points WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->points = htmlspecialchars(strip_tags($points));
        $this->id = htmlspecialchars(strip_tags($this->id));

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

?>

