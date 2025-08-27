<?php
/**
 * Migration: Criar tabela de execuções de hábitos
 */

class CreateHabitExecutions {
    public static function up($pdo) {
        // Criar tabela habit_executions
        $sql = "
            CREATE TABLE IF NOT EXISTS habit_executions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                habit_id INT NOT NULL,
                execution_date DATE NOT NULL,
                points_earned INT NOT NULL,
                notes TEXT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (habit_id) REFERENCES habits(id) ON DELETE CASCADE,
                UNIQUE KEY unique_execution (user_id, habit_id, execution_date),
                INDEX idx_user_date (user_id, execution_date),
                INDEX idx_habit_date (habit_id, execution_date),
                INDEX idx_execution_date (execution_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($sql);
        
        // Criar algumas execuções de exemplo para simular histórico
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@dailyhealthy.com']);
        $adminUser = $stmt->fetch();
        
        if ($adminUser) {
            $adminId = $adminUser['id'];
            
            // Buscar hábitos do admin
            $stmt = $pdo->prepare("SELECT id, points_base FROM habits WHERE user_id = ?");
            $stmt->execute([$adminId]);
            $habits = $stmt->fetchAll();
            
            // Criar execuções dos últimos 7 dias
            for ($i = 0; $i < 7; $i++) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                // Para cada dia, executar alguns hábitos aleatoriamente
                foreach ($habits as $habit) {
                    // 70% de chance de ter executado o hábito
                    if (rand(1, 100) <= 70) {
                        $stmt = $pdo->prepare("
                            SELECT id FROM habit_executions 
                            WHERE user_id = ? AND habit_id = ? AND execution_date = ?
                        ");
                        $stmt->execute([$adminId, $habit['id'], $date]);
                        
                        if ($stmt->rowCount() == 0) {
                            $stmt = $pdo->prepare("
                                INSERT INTO habit_executions (user_id, habit_id, execution_date, points_earned) 
                                VALUES (?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                $adminId,
                                $habit['id'],
                                $date,
                                $habit['points_base']
                            ]);
                        }
                    }
                }
            }
            
            echo "     → Execuções de exemplo criadas para admin\n";
        }
        
        // Criar execuções para outros usuários também
        $stmt = $pdo->prepare("
            SELECT u.id as user_id, h.id as habit_id, h.points_base 
            FROM users u 
            JOIN habits h ON u.id = h.user_id 
            WHERE u.email != ? 
            LIMIT 20
        ");
        $stmt->execute(['admin@dailyhealthy.com']);
        $userHabits = $stmt->fetchAll();
        
        foreach ($userHabits as $uh) {
            // Criar execuções dos últimos 3 dias
            for ($i = 0; $i < 3; $i++) {
                $date = date('Y-m-d', strtotime("-$i days"));
                
                // 50% de chance de ter executado
                if (rand(1, 100) <= 50) {
                    $stmt = $pdo->prepare("
                        SELECT id FROM habit_executions 
                        WHERE user_id = ? AND habit_id = ? AND execution_date = ?
                    ");
                    $stmt->execute([$uh['user_id'], $uh['habit_id'], $date]);
                    
                    if ($stmt->rowCount() == 0) {
                        $stmt = $pdo->prepare("
                            INSERT INTO habit_executions (user_id, habit_id, execution_date, points_earned) 
                            VALUES (?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $uh['user_id'],
                            $uh['habit_id'],
                            $date,
                            $uh['points_base']
                        ]);
                    }
                }
            }
        }
        
        echo "     → Execuções de exemplo criadas para outros usuários\n";
    }
}
?>

