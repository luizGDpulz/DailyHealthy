<?php
/**
 * DailyHealthy - Sistema de Migrations
 * Executa todas as migrations em ordem
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Database.php';

echo "=== DailyHealthy - Sistema de Migrations ===\n";
echo "Iniciando processo de migração...\n\n";

// Criar banco de dados se não existir
echo "1. Verificando/Criando banco de dados...\n";
if (Database::createDatabaseIfNotExists()) {
    echo "   ✓ Banco de dados verificado/criado com sucesso\n\n";
} else {
    echo "   ✗ Erro ao criar banco de dados\n";
    exit(1);
}

// Obter conexão
$pdo = Database::getInstance();

// Lista de migrations em ordem
$migrations = [
    '001_create_users.php' => 'CreateUsers',
    '002_create_habits.php' => 'CreateHabits', 
    '003_create_habit_executions.php' => 'CreateHabitExecutions',
    '004_create_badges.php' => 'CreateBadges',
    '005_create_user_badges.php' => 'CreateUserBadges'
];

echo "2. Executando migrations...\n";

foreach ($migrations as $file => $className) {
    $filePath = __DIR__ . '/' . $file;
    
    if (!file_exists($filePath)) {
        echo "   ✗ Arquivo de migration não encontrado: $file\n";
        continue;
    }
    
    echo "   Executando: $file ($className)...\n";
    
    try {
        require_once $filePath;
        
        if (class_exists($className)) {
            $className::up($pdo);
            echo "   ✓ Migration $file executada com sucesso\n";
        } else {
            echo "   ✗ Classe $className não encontrada em $file\n";
        }
    } catch (Exception $e) {
        echo "   ✗ Erro ao executar $file: " . $e->getMessage() . "\n";
    }
}

echo "\n3. Verificando estrutura do banco...\n";

// Verificar se todas as tabelas foram criadas
$tables = ['users', 'habits', 'habit_executions', 'badges', 'user_badges'];
$allTablesCreated = true;

foreach ($tables as $table) {
    if (Database::tableExists($table)) {
        echo "   ✓ Tabela '$table' criada\n";
    } else {
        echo "   ✗ Tabela '$table' não encontrada\n";
        $allTablesCreated = false;
    }
}

echo "\n=== Resultado Final ===\n";
if ($allTablesCreated) {
    echo "✓ Todas as migrations foram executadas com sucesso!\n";
    echo "✓ Banco de dados DailyHealthy está pronto para uso.\n";
    echo "✓ Usuário admin criado: admin@dailyhealthy.com / admin123\n";
} else {
    echo "✗ Algumas migrations falharam. Verifique os erros acima.\n";
}

echo "\n=== Próximos Passos ===\n";
echo "1. Acesse: http://localhost/dailyhealthy/\n";
echo "2. Faça login com: admin@dailyhealthy.com / admin123\n";
echo "3. Comece a usar o DailyHealthy!\n";
?>

