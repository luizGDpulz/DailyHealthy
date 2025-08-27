<?php
class CreateHabitExecutions {
    public static function up($pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS habit_executions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                habit_id INT NOT NULL,
                user_id INT NOT NULL,
                points_earned INT NOT NULL,
                executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }
}
