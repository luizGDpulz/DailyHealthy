<?php
/**
 * Migration: Criar tabela de badges dos usuários
 */

class CreateUserBadges {
    public static function up($pdo) {
        // Criar tabela user_badges
        $sql = "
            CREATE TABLE IF NOT EXISTS user_badges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                badge_id INT NOT NULL,
                earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (badge_id) REFERENCES badges(id) ON DELETE CASCADE,
                UNIQUE KEY unique_user_badge (user_id, badge_id),
                INDEX idx_user_id (user_id),
                INDEX idx_badge_id (badge_id),
                INDEX idx_earned_at (earned_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($sql);
        
        // Atribuir alguns badges para o usuário admin baseado em seus pontos e streak
        $stmt = $pdo->prepare("SELECT id, points, streak FROM users WHERE email = ?");
        $stmt->execute(['admin@dailyhealthy.com']);
        $adminUser = $stmt->fetch();
        
        if ($adminUser) {
            $adminId = $adminUser['id'];
            $adminPoints = $adminUser['points'];
            $adminStreak = $adminUser['streak'];
            
            // Buscar badges que o admin deveria ter baseado em pontos
            $stmt = $pdo->prepare("
                SELECT id FROM badges 
                WHERE type = 'points' AND points_required <= ? 
                ORDER BY points_required ASC
            ");
            $stmt->execute([$adminPoints]);
            $pointsBadges = $stmt->fetchAll();
            
            foreach ($pointsBadges as $badge) {
                $stmt = $pdo->prepare("
                    SELECT id FROM user_badges 
                    WHERE user_id = ? AND badge_id = ?
                ");
                $stmt->execute([$adminId, $badge['id']]);
                
                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_badges (user_id, badge_id, earned_at) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $adminId,
                        $badge['id'],
                        date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
                    ]);
                }
            }
            
            // Buscar badges que o admin deveria ter baseado em streak
            $stmt = $pdo->prepare("
                SELECT id FROM badges 
                WHERE type = 'streak' AND streak_required <= ? 
                ORDER BY streak_required ASC
            ");
            $stmt->execute([$adminStreak]);
            $streakBadges = $stmt->fetchAll();
            
            foreach ($streakBadges as $badge) {
                $stmt = $pdo->prepare("
                    SELECT id FROM user_badges 
                    WHERE user_id = ? AND badge_id = ?
                ");
                $stmt->execute([$adminId, $badge['id']]);
                
                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_badges (user_id, badge_id, earned_at) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $adminId,
                        $badge['id'],
                        date('Y-m-d H:i:s', strtotime('-' . rand(1, 20) . ' days'))
                    ]);
                }
            }
            
            // Dar badge de "Bem-vindo" para o admin
            $stmt = $pdo->prepare("SELECT id FROM badges WHERE name = 'Bem-vindo'");
            $stmt->execute();
            $welcomeBadge = $stmt->fetch();
            
            if ($welcomeBadge) {
                $stmt = $pdo->prepare("
                    SELECT id FROM user_badges 
                    WHERE user_id = ? AND badge_id = ?
                ");
                $stmt->execute([$adminId, $welcomeBadge['id']]);
                
                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_badges (user_id, badge_id, earned_at) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $adminId,
                        $welcomeBadge['id'],
                        date('Y-m-d H:i:s', strtotime('-25 days'))
                    ]);
                }
            }
            
            echo "     → Badges atribuídos ao admin\n";
        }
        
        // Atribuir alguns badges para outros usuários também
        $stmt = $pdo->prepare("
            SELECT id, points, streak FROM users 
            WHERE email != ? AND points > 0 
            LIMIT 5
        ");
        $stmt->execute(['admin@dailyhealthy.com']);
        $otherUsers = $stmt->fetchAll();
        
        foreach ($otherUsers as $user) {
            // Badge de bem-vindo para todos
            $stmt = $pdo->prepare("SELECT id FROM badges WHERE name = 'Bem-vindo'");
            $stmt->execute();
            $welcomeBadge = $stmt->fetch();
            
            if ($welcomeBadge) {
                $stmt = $pdo->prepare("
                    SELECT id FROM user_badges 
                    WHERE user_id = ? AND badge_id = ?
                ");
                $stmt->execute([$user['id'], $welcomeBadge['id']]);
                
                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO user_badges (user_id, badge_id, earned_at) 
                        VALUES (?, ?, ?)
                    ");
                    $stmt->execute([
                        $user['id'],
                        $welcomeBadge['id'],
                        date('Y-m-d H:i:s', strtotime('-' . rand(5, 15) . ' days'))
                    ]);
                }
            }
            
            // Badges baseados em pontos
            if ($user['points'] >= 100) {
                $stmt = $pdo->prepare("SELECT id FROM badges WHERE name = 'Iniciante'");
                $stmt->execute();
                $badge = $stmt->fetch();
                
                if ($badge) {
                    $stmt = $pdo->prepare("
                        SELECT id FROM user_badges 
                        WHERE user_id = ? AND badge_id = ?
                    ");
                    $stmt->execute([$user['id'], $badge['id']]);
                    
                    if ($stmt->rowCount() == 0) {
                        $stmt = $pdo->prepare("
                            INSERT INTO user_badges (user_id, badge_id, earned_at) 
                            VALUES (?, ?, ?)
                        ");
                        $stmt->execute([
                            $user['id'],
                            $badge['id'],
                            date('Y-m-d H:i:s', strtotime('-' . rand(1, 10) . ' days'))
                        ]);
                    }
                }
            }
        }
        
        echo "     → Badges atribuídos a outros usuários\n";
    }
}
?>

