<?php
/**
 * DailyHealthy - Script de Verificação do Sistema
 * Verifica se todos os componentes estão funcionando corretamente
 */

// Configurar headers
header('Content-Type: text/html; charset=utf-8');

// Verificar se é uma requisição web
$isWeb = isset($_SERVER['HTTP_HOST']);

if ($isWeb) {
    echo "<!DOCTYPE html>\n";
    echo "<html lang='pt-BR'>\n";
    echo "<head>\n";
    echo "<meta charset='UTF-8'>\n";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
    echo "<title>Verificação do Sistema - DailyHealthy</title>\n";
    echo "<style>\n";
    echo "body { font-family: Arial, sans-serif; max-width: 1000px; margin: 0 auto; padding: 20px; background: #f5f5f5; }\n";
    echo ".container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }\n";
    echo ".success { color: #4CAF50; font-weight: bold; }\n";
    echo ".error { color: #F44336; font-weight: bold; }\n";
    echo ".warning { color: #FF9800; font-weight: bold; }\n";
    echo ".info { color: #2196F3; font-weight: bold; }\n";
    echo ".check-item { margin: 15px 0; padding: 10px; border-left: 4px solid #ddd; background: #f9f9f9; }\n";
    echo ".check-item.success { border-left-color: #4CAF50; background: #e8f5e8; }\n";
    echo ".check-item.error { border-left-color: #F44336; background: #ffeaea; }\n";
    echo ".check-item.warning { border-left-color: #FF9800; background: #fff3e0; }\n";
    echo ".section { margin: 30px 0; }\n";
    echo ".section h3 { color: #333; border-bottom: 2px solid #4CAF50; padding-bottom: 10px; }\n";
    echo "pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }\n";
    echo ".stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }\n";
    echo ".stat-card { background: #f8f9fa; padding: 20px; border-radius: 8px; text-align: center; }\n";
    echo ".stat-number { font-size: 2em; font-weight: bold; color: #4CAF50; }\n";
    echo "</style>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<div class='container'>\n";
    echo "<h1>🔍 Verificação do Sistema DailyHealthy</h1>\n";
    echo "<p>Este script verifica se todos os componentes estão funcionando corretamente.</p>\n";
}

function checkItem($title, $condition, $successMsg, $errorMsg, $type = 'check') {
    global $isWeb;
    
    $status = $condition ? 'success' : 'error';
    $message = $condition ? $successMsg : $errorMsg;
    $icon = $condition ? '✅' : '❌';
    
    if ($isWeb) {
        echo "<div class='check-item $status'>\n";
        echo "<strong>$icon $title:</strong> $message\n";
        echo "</div>\n";
    } else {
        echo "[$status] $title: $message\n";
    }
    
    return $condition;
}

function section($title) {
    global $isWeb;
    
    if ($isWeb) {
        echo "<div class='section'>\n";
        echo "<h3>$title</h3>\n";
    } else {
        echo "\n=== $title ===\n";
    }
}

function endSection() {
    global $isWeb;
    
    if ($isWeb) {
        echo "</div>\n";
    }
}

// Iniciar verificações
$allChecks = true;

// 1. Verificar PHP e Extensões
section("🐘 PHP e Extensões");

$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
$allChecks &= checkItem(
    "Versão do PHP",
    $phpOk,
    "PHP $phpVersion (✓ Compatível)",
    "PHP $phpVersion (⚠️ Requer 7.4+)"
);

$pdoOk = extension_loaded('pdo');
$allChecks &= checkItem(
    "Extensão PDO",
    $pdoOk,
    "PDO está disponível",
    "PDO não está instalado"
);

$pdoMysqlOk = extension_loaded('pdo_mysql');
$allChecks &= checkItem(
    "Extensão PDO MySQL",
    $pdoMysqlOk,
    "PDO MySQL está disponível",
    "PDO MySQL não está instalado"
);

$mbstringOk = extension_loaded('mbstring');
$allChecks &= checkItem(
    "Extensão mbstring",
    $mbstringOk,
    "mbstring está disponível",
    "mbstring não está instalado"
);

$jsonOk = extension_loaded('json');
$allChecks &= checkItem(
    "Extensão JSON",
    $jsonOk,
    "JSON está disponível",
    "JSON não está instalado"
);

endSection();

// 2. Verificar Arquivos e Estrutura
section("📁 Estrutura de Arquivos");

$requiredFiles = [
    'config/config.php' => 'Arquivo de configuração',
    'app/Database.php' => 'Classe Database',
    'app/Auth.php' => 'Classe Auth',
    'app/User.php' => 'Classe User',
    'app/Habit.php' => 'Classe Habit',
    '.htaccess' => 'Configurações Apache',
    'index.php' => 'Página inicial',
    'dashboard.php' => 'Dashboard',
    'ranking.php' => 'Ranking',
    'setup.php' => 'Script de setup'
];

foreach ($requiredFiles as $file => $description) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $allChecks &= checkItem(
        $description,
        $exists,
        "Arquivo encontrado: $file",
        "Arquivo não encontrado: $file"
    );
}

$requiredDirs = [
    'api' => 'Pasta de APIs',
    'assets/css' => 'Pasta de estilos',
    'assets/js' => 'Pasta de JavaScript',
    'migrations' => 'Pasta de migrations'
];

foreach ($requiredDirs as $dir => $description) {
    $exists = is_dir(__DIR__ . '/' . $dir);
    $allChecks &= checkItem(
        $description,
        $exists,
        "Diretório encontrado: $dir",
        "Diretório não encontrado: $dir"
    );
}

endSection();

// 3. Verificar Configurações
section("⚙️ Configurações");

try {
    require_once __DIR__ . '/config/config.php';
    
    checkItem(
        "Arquivo de configuração",
        true,
        "config.php carregado com sucesso",
        "Erro ao carregar config.php"
    );
    
    $configOk = defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER');
    $allChecks &= checkItem(
        "Constantes de banco",
        $configOk,
        "Constantes de banco definidas",
        "Constantes de banco não definidas"
    );
    
} catch (Exception $e) {
    $allChecks &= checkItem(
        "Arquivo de configuração",
        false,
        "",
        "Erro ao carregar config.php: " . $e->getMessage()
    );
}

endSection();

// 4. Verificar Conexão com Banco
section("🗄️ Banco de Dados");

try {
    require_once __DIR__ . '/app/Database.php';
    
    // Testar conexão
    $testQuery = Database::fetchOne("SELECT 1 as test");
    $dbOk = ($testQuery && $testQuery['test'] == 1);
    
    $allChecks &= checkItem(
        "Conexão com banco",
        $dbOk,
        "Conexão estabelecida com sucesso",
        "Falha na conexão com banco"
    );
    
    if ($dbOk) {
        // Verificar tabelas
        $tables = ['users', 'habits', 'habit_executions', 'badges', 'user_badges'];
        
        foreach ($tables as $table) {
            try {
                $result = Database::fetchOne("SELECT COUNT(*) as count FROM $table");
                $tableOk = ($result !== false);
                $count = $tableOk ? $result['count'] : 0;
                
                checkItem(
                    "Tabela $table",
                    $tableOk,
                    "Tabela existe ($count registros)",
                    "Tabela não encontrada"
                );
                
            } catch (Exception $e) {
                checkItem(
                    "Tabela $table",
                    false,
                    "",
                    "Erro ao verificar tabela: " . $e->getMessage()
                );
            }
        }
    }
    
} catch (Exception $e) {
    $allChecks &= checkItem(
        "Conexão com banco",
        false,
        "",
        "Erro de conexão: " . $e->getMessage()
    );
}

endSection();

// 5. Verificar APIs
section("🔌 APIs");

$apiFiles = [
    'api/auth.php' => 'API de Autenticação',
    'api/habits.php' => 'API de Hábitos',
    'api/ranking.php' => 'API de Ranking',
    'api/badges.php' => 'API de Badges'
];

foreach ($apiFiles as $file => $description) {
    $exists = file_exists(__DIR__ . '/' . $file);
    checkItem(
        $description,
        $exists,
        "API disponível: $file",
        "API não encontrada: $file"
    );
}

endSection();

// 6. Verificar Permissões
section("🔐 Permissões");

$writableDirs = [
    '.' => 'Diretório raiz',
    'assets' => 'Pasta de assets'
];

foreach ($writableDirs as $dir => $description) {
    $writable = is_writable(__DIR__ . '/' . $dir);
    checkItem(
        "Escrita em $description",
        $writable,
        "Permissão de escrita OK",
        "Sem permissão de escrita"
    );
}

endSection();

// 7. Estatísticas do Sistema
if (class_exists('Database')) {
    section("📊 Estatísticas do Sistema");
    
    try {
        $userCount = Database::fetchOne("SELECT COUNT(*) as count FROM users")['count'] ?? 0;
        $habitCount = Database::fetchOne("SELECT COUNT(*) as count FROM habits")['count'] ?? 0;
        $executionCount = Database::fetchOne("SELECT COUNT(*) as count FROM habit_executions")['count'] ?? 0;
        $badgeCount = Database::fetchOne("SELECT COUNT(*) as count FROM badges")['count'] ?? 0;
        
        if ($isWeb) {
            echo "<div class='stats'>\n";
            echo "<div class='stat-card'><div class='stat-number'>$userCount</div><div>Usuários</div></div>\n";
            echo "<div class='stat-card'><div class='stat-number'>$habitCount</div><div>Hábitos</div></div>\n";
            echo "<div class='stat-card'><div class='stat-number'>$executionCount</div><div>Execuções</div></div>\n";
            echo "<div class='stat-card'><div class='stat-number'>$badgeCount</div><div>Badges</div></div>\n";
            echo "</div>\n";
        } else {
            echo "Usuários: $userCount\n";
            echo "Hábitos: $habitCount\n";
            echo "Execuções: $executionCount\n";
            echo "Badges: $badgeCount\n";
        }
        
    } catch (Exception $e) {
        if ($isWeb) {
            echo "<p class='error'>Erro ao obter estatísticas: " . $e->getMessage() . "</p>\n";
        } else {
            echo "Erro ao obter estatísticas: " . $e->getMessage() . "\n";
        }
    }
    
    endSection();
}

// 8. Resultado Final
section("🎯 Resultado da Verificação");

if ($allChecks) {
    if ($isWeb) {
        echo "<div class='check-item success'>\n";
        echo "<h3>🎉 Sistema Funcionando Perfeitamente!</h3>\n";
        echo "<p>Todos os componentes estão funcionando corretamente. O DailyHealthy está pronto para uso.</p>\n";
        echo "<p><strong>Próximos passos:</strong></p>\n";
        echo "<ul>\n";
        echo "<li><a href='index.php'>Acessar a aplicação</a></li>\n";
        echo "<li>Fazer login com: admin@dailyhealthy.com / admin123</li>\n";
        echo "<li>Explorar o dashboard e funcionalidades</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
    } else {
        echo "\n✅ SISTEMA FUNCIONANDO PERFEITAMENTE!\n";
        echo "Todos os componentes estão OK. DailyHealthy pronto para uso.\n";
    }
} else {
    if ($isWeb) {
        echo "<div class='check-item error'>\n";
        echo "<h3>⚠️ Problemas Encontrados</h3>\n";
        echo "<p>Alguns componentes apresentaram problemas. Verifique os itens marcados com ❌ acima.</p>\n";
        echo "<p><strong>Soluções recomendadas:</strong></p>\n";
        echo "<ul>\n";
        echo "<li>Execute o script setup.php se ainda não executou</li>\n";
        echo "<li>Verifique se Apache e MySQL estão rodando no XAMPP</li>\n";
        echo "<li>Confirme as configurações em config/config.php</li>\n";
        echo "<li>Consulte o arquivo INSTALACAO_XAMPP.md</li>\n";
        echo "</ul>\n";
        echo "</div>\n";
    } else {
        echo "\n❌ PROBLEMAS ENCONTRADOS\n";
        echo "Verifique os itens marcados com erro acima.\n";
    }
}

endSection();

if ($isWeb) {
    echo "<div style='margin-top: 30px; padding: 20px; background: #f0f0f0; border-radius: 5px; text-align: center;'>\n";
    echo "<p><strong>🎯 DailyHealthy - Sistema de Hábitos Saudáveis</strong></p>\n";
    echo "<p>Para suporte, consulte a documentação completa no README.md</p>\n";
    echo "</div>\n";
    echo "</div>\n";
    echo "</body></html>\n";
}
?>

