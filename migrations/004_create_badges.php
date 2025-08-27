<?php
/**
 * Migration: Criar tabela de badges/conquistas
 */

class CreateBadges {
    public static function up($pdo) {
        // Criar tabela badges
        $sql = "
            CREATE TABLE IF NOT EXISTS badges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT,
                icon VARCHAR(50) DEFAULT 'award',
                color VARCHAR(7) DEFAULT '#FFD700',
                points_required INT DEFAULT 0,
                streak_required INT DEFAULT 0,
                habits_required INT DEFAULT 0,
                type ENUM('points', 'streak', 'habits', 'special') DEFAULT 'points',
                is_active BOOLEAN DEFAULT TRUE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_type (type),
                INDEX idx_points (points_required),
                INDEX idx_streak (streak_required)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";
        
        $pdo->exec($sql);
        
        // Badges padrão do sistema
        $defaultBadges = [
            // Badges de Pontos
            [
                'name' => 'Primeiro Passo',
                'description' => 'Ganhe seus primeiros 10 pontos',
                'icon' => 'footprints',
                'color' => '#4CAF50',
                'points_required' => 10,
                'type' => 'points'
            ],
            [
                'name' => 'Iniciante',
                'description' => 'Acumule 100 pontos',
                'icon' => 'star',
                'color' => '#2196F3',
                'points_required' => 100,
                'type' => 'points'
            ],
            [
                'name' => 'Dedicado',
                'description' => 'Acumule 500 pontos',
                'icon' => 'medal',
                'color' => '#FF9800',
                'points_required' => 500,
                'type' => 'points'
            ],
            [
                'name' => 'Expert',
                'description' => 'Acumule 1000 pontos',
                'icon' => 'trophy',
                'color' => '#FFD700',
                'points_required' => 1000,
                'type' => 'points'
            ],
            
            // Badges de Streak
            [
                'name' => 'Consistente',
                'description' => 'Mantenha um streak de 3 dias',
                'icon' => 'flame',
                'color' => '#FF5722',
                'streak_required' => 3,
                'type' => 'streak'
            ],
            [
                'name' => 'Determinado',
                'description' => 'Mantenha um streak de 7 dias',
                'icon' => 'zap',
                'color' => '#FF9800',
                'streak_required' => 7,
                'type' => 'streak'
            ],
            [
                'name' => 'Imparável',
                'description' => 'Mantenha um streak de 30 dias',
                'icon' => 'fire',
                'color' => '#F44336',
                'streak_required' => 30,
                'type' => 'streak'
            ],
            
            // Badges de Hábitos
            [
                'name' => 'Organizador',
                'description' => 'Crie 5 hábitos diferentes',
                'icon' => 'list',
                'color' => '#9C27B0',
                'habits_required' => 5,
                'type' => 'habits'
            ],
            [
                'name' => 'Planejador',
                'description' => 'Crie 10 hábitos diferentes',
                'icon' => 'calendar',
                'color' => '#673AB7',
                'habits_required' => 10,
                'type' => 'habits'
            ],
            
            // Badges Especiais
            [
                'name' => 'Bem-vindo',
                'description' => 'Complete seu primeiro hábito',
                'icon' => 'heart',
                'color' => '#E91E63',
                'type' => 'special'
            ],
            [
                'name' => 'Madrugador',
                'description' => 'Complete hábitos antes das 8h',
                'icon' => 'sunrise',
                'color' => '#FFC107',
                'type' => 'special'
            ],
            [
                'name' => 'Noturno',
                'description' => 'Complete hábitos após as 22h',
                'icon' => 'moon',
                'color' => '#3F51B5',
                'type' => 'special'
            ]
        ];
        
        foreach ($defaultBadges as $badge) {
            // Verificar se o badge já existe
            $stmt = $pdo->prepare("SELECT id FROM badges WHERE name = ?");
            $stmt->execute([$badge['name']]);
            
            if ($stmt->rowCount() == 0) {
                $stmt = $pdo->prepare("
                    INSERT INTO badges (name, description, icon, color, points_required, streak_required, habits_required, type) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $badge['name'],
                    $badge['description'],
                    $badge['icon'],
                    $badge['color'],
                    $badge['points_required'] ?? 0,
                    $badge['streak_required'] ?? 0,
                    $badge['habits_required'] ?? 0,
                    $badge['type']
                ]);
            }
        }
        
        echo "     → Badges padrão criados\n";
    }
}
?>

