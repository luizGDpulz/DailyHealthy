<?php
class CreateBadges {
    public static function up($pdo) {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS badges (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                description TEXT NOT NULL,
                condition_type ENUM(
                    'first_habit_completed',
                    'streak_7_days',
                    'streak_30_days',
                    'habits_created',
                    'points_earned'
                ) NOT NULL,
                condition_value INT NOT NULL,
                icon_path VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Insert default badges
        $badges = [
            ['Primeiro Passo', 'Complete seu primeiro hábito', 'first_habit_completed', 1, 'first_step.png'],
            ['Uma Semana!', 'Mantenha uma streak de 7 dias', 'streak_7_days', 7, 'week_streak.png'],
            ['Disciplinado', 'Mantenha uma streak de 30 dias', 'streak_30_days', 30, 'month_streak.png'],
            ['Criativo', 'Crie 10 hábitos diferentes', 'habits_created', 10, 'creative.png'],
            ['Milionário', 'Acumule 1000 pontos', 'points_earned', 1000, 'millionaire.png']
        ];

        $stmt = $pdo->prepare("
            INSERT INTO badges (name, description, condition_type, condition_value, icon_path)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($badges as $badge) {
            $stmt->execute($badge);
        }
    }
}
