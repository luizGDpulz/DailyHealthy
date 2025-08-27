<?php
class CreateHabits {
    public static function up($pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS habits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(100) NOT NULL,
                description TEXT,
                base_points INT DEFAULT 5,
                frequency ENUM('daily', 'weekly', 'monthly') DEFAULT 'daily',
                status ENUM('active', 'archived') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
    }
}
