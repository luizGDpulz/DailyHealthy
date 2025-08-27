<?php
/**
 * Migration: Criar tabela de usuários
 */

class CreateUsers {
    public static function up($pdo) {
        // Criar tabela users
        $sql = "
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                points INT DEFAULT 0,
                streak INT DEFAULT 0,
                last_activity DATE NULL,
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_points (points),
                INDEX idx_streak (streak)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($sql);
        
        // Verificar se usuário admin já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@dailyhealthy.com']);
        
        if ($stmt->rowCount() == 0) {
            // Criar usuário administrador padrão
            $adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, points, streak, last_activity) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                "Administrador",
                "admin@dailyhealthy.com", 
                $adminPassword,
                850,
                12,
                date('Y-m-d')
            ]);
            
            echo "     → Usuário admin criado\n";
        }
        
        // Criar usuários de exemplo para o ranking
        $exampleUsers = [
            ['Maria Silva', 'maria@email.com', 720, 8],
            ['João Santos', 'joao@email.com', 650, 5],
            ['Ana Costa', 'ana@email.com', 580, 15],
            ['Pedro Lima', 'pedro@email.com', 420, 3],
            ['Carla Oliveira', 'carla@email.com', 380, 7],
            ['Lucas Ferreira', 'lucas@email.com', 320, 2],
            ['Juliana Souza', 'juliana@email.com', 280, 4],
            ['Rafael Alves', 'rafael@email.com', 250, 1]
        ];
        
        foreach ($exampleUsers as $user) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$user[1]]);
            
            if ($stmt->rowCount() == 0) {
                $password = password_hash("123456", PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, points, streak, last_activity) 
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $user[0],
                    $user[1],
                    $password,
                    $user[2],
                    $user[3],
                    date('Y-m-d', strtotime('-' . rand(0, 7) . ' days'))
                ]);
            }
        }
        
        echo "     → Usuários de exemplo criados\n";
    }
}
?>

