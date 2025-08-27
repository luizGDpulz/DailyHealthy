<?php
class CreateUsers {
    public static function up($pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                points INT DEFAULT 0,
                current_streak INT DEFAULT 0,
                best_streak INT DEFAULT 0,
                level ENUM('bronze', 'silver', 'gold', 'diamond') DEFAULT 'bronze',
                avatar VARCHAR(255) DEFAULT 'default.jpg',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");

        // Default user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@dailyhealthy.com']);
        
        if ($stmt->rowCount() == 0) {
            $hash = password_hash('admin123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, points, current_streak, level) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute(['Admin DailyHealthy', 'admin@dailyhealthy.com', $hash, 100, 5, 'silver']);
        }
    }
}
