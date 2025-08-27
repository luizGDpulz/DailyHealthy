<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Database.php';

$pdo = Database::getInstance();

$migrations = [
    'CreateUsers' => '001_create_users.php',
    'CreateHabits' => '002_create_habits.php',
    'CreateHabitExecutions' => '003_create_habit_executions.php',
    'CreateBadges' => '004_create_badges.php',
    'CreateUserBadges' => '005_create_user_badges.php'
];

foreach ($migrations as $class => $file) {
    require_once __DIR__ . '/' . $file;
    echo "Executando: $class\n";
    $class::up($pdo);
    echo "✅ $class concluída\n";
}
