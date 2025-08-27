<?php
/**
 * Migration: Criar tabela de hábitos
 */

class CreateHabits {
    public static function up($pdo) {
        // Criar tabela habits
        $sql = "
            CREATE TABLE IF NOT EXISTS habits (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(150) NOT NULL,
                description TEXT,
                points_base INT DEFAULT 10,
                category VARCHAR(50) DEFAULT 'geral',
                color VARCHAR(7) DEFAULT '#4CAF50',
                icon VARCHAR(50) DEFAULT 'target',
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_category (category),
                INDEX idx_active (is_active)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($sql);
        
        // Obter ID do usuário admin
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute(['admin@dailyhealthy.com']);
        $adminUser = $stmt->fetch();
        
        if ($adminUser) {
            $adminId = $adminUser['id'];
            
            // Hábitos padrão para o admin
            $defaultHabits = [
                [
                    'title' => 'Beber 2L de água',
                    'description' => 'Manter hidratação adequada durante o dia',
                    'points' => 10,
                    'category' => 'saude',
                    'color' => '#2196F3',
                    'icon' => 'droplets'
                ],
                [
                    'title' => 'Exercitar-se 30min',
                    'description' => 'Atividade física diária para manter o corpo ativo',
                    'points' => 20,
                    'category' => 'exercicio',
                    'color' => '#FF9800',
                    'icon' => 'dumbbell'
                ],
                [
                    'title' => 'Meditar 10min',
                    'description' => 'Momento de mindfulness e relaxamento',
                    'points' => 15,
                    'category' => 'mental',
                    'color' => '#9C27B0',
                    'icon' => 'brain'
                ],
                [
                    'title' => 'Comer 5 porções de frutas/vegetais',
                    'description' => 'Alimentação saudável e balanceada',
                    'points' => 15,
                    'category' => 'alimentacao',
                    'color' => '#4CAF50',
                    'icon' => 'apple'
                ],
                [
                    'title' => 'Ler 20 páginas',
                    'description' => 'Desenvolver o hábito da leitura diária',
                    'points' => 12,
                    'category' => 'educacao',
                    'color' => '#795548',
                    'icon' => 'book'
                ],
                [
                    'title' => 'Dormir 8 horas',
                    'description' => 'Garantir sono reparador e qualidade de vida',
                    'points' => 18,
                    'category' => 'saude',
                    'color' => '#3F51B5',
                    'icon' => 'moon'
                ]
            ];
            
            foreach ($defaultHabits as $habit) {
                // Verificar se o hábito já existe
                $stmt = $pdo->prepare("SELECT id FROM habits WHERE user_id = ? AND title = ?");
                $stmt->execute([$adminId, $habit['title']]);
                
                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO habits (user_id, title, description, points_base, category, color, icon) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $adminId,
                        $habit['title'],
                        $habit['description'],
                        $habit['points'],
                        $habit['category'],
                        $habit['color'],
                        $habit['icon']
                    ]);
                }
            }
            
            echo "     → Hábitos padrão criados para admin\n";
        }
        
        // Criar alguns hábitos para outros usuários também
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email != ? LIMIT 3");
        $stmt->execute(['admin@dailyhealthy.com']);
        $otherUsers = $stmt->fetchAll();
        
        $commonHabits = [
            ['Beber água', 'Hidratação diária', 8, 'saude'],
            ['Caminhar', 'Exercício leve', 12, 'exercicio'],
            ['Estudar', 'Desenvolvimento pessoal', 15, 'educacao']
        ];
        
        foreach ($otherUsers as $user) {
            foreach ($commonHabits as $habit) {
                $stmt = $pdo->prepare("SELECT id FROM habits WHERE user_id = ? AND title = ?");
                $stmt->execute([$user['id'], $habit[0]]);
                
                if ($stmt->rowCount() == 0) {
                    $stmt = $pdo->prepare("
                        INSERT INTO habits (user_id, title, description, points_base, category) 
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $user['id'],
                        $habit[0],
                        $habit[1],
                        $habit[2],
                        $habit[3]
                    ]);
                }
            }
        }
        
        echo "     → Hábitos de exemplo criados\n";
    }
}
?>

