<?php
/**
 * DailyHealthy - Script de Configuração Inicial
 * Execute este arquivo para configurar o banco de dados
 */

require_once __DIR__ . 
'/config/config.php';
// require_once __DIR__ . '/migrations/migrate.php'; // Removido, o conteúdo será incluído diretamente

// Verificar se é uma requisição web ou CLI
$isWeb = isset($_SERVER['HTTP_HOST']);

if ($isWeb) {
    // Configurar headers para web
    header('Content-Type: text/html; charset=utf-8');
    echo "<!DOCTYPE html>\n";
    echo "<html lang='pt-BR'>\n";
    echo "<head>\n";
    echo "<meta charset='UTF-8'>\n";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>\n";
    echo "<title>Setup - DailyHealthy</title>\n";
    echo "<style>\n";
    echo "body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }\n";
    echo ".success { color: #4CAF50; }\n";
    echo ".error { color: #F44336; }\n";
    echo ".info { color: #2196F3; }\n";
    echo "pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }\n";
    echo "</style>\n";
    echo "</head>\n";
    echo "<body>\n";
    echo "<h1>🎯 DailyHealthy - Configuração Inicial</h1>\n";
}

function output($message, $type = 'info') {
    global $isWeb;
    
    if ($isWeb) {
        echo "<p class='$type'>$message</p>\n";
        flush();
    } else {
        echo "[$type] $message\n";
    }
}

function outputPre($content) {
    global $isWeb;
    
    if ($isWeb) {
        echo "<pre>$content</pre>\n";
        flush();
    } else {
        echo "$content\n";
    }
}

try {
    output("Iniciando configuração do DailyHealthy...", 'info');
    
    // Verificar configurações
    output("Verificando configurações...", 'info');
    outputPre("Host: " . DB_HOST . "\nBanco: " . DB_NAME . "\nUsuário: " . DB_USER);
    
    // Testar conexão com o banco
    output("Testando conexão com o banco de dados...", 'info');
    
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ]
        );
        
        output("✅ Conexão com MySQL estabelecida com sucesso!", 'success');
        
    } catch (PDOException $e) {
        output("❌ Erro ao conectar com MySQL: " . $e->getMessage(), 'error');
        
        if ($isWeb) {
            echo "<h3>Instruções para XAMPP:</h3>\n";
            echo "<ol>\n";
            echo "<li>Certifique-se de que o XAMPP está rodando</li>\n";
            echo "<li>Inicie o Apache e MySQL no painel do XAMPP</li>\n";
            echo "<li>Verifique se as configurações em config/config.php estão corretas</li>\n";
            echo "<li>Tente novamente</li>\n";
            echo "</ol>\n";
            echo "</body></html>\n";
        }
        exit(1);
    }
    
    // Criar banco de dados se não existir
    output("Criando banco de dados se necessário...", 'info');
    
    try {
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        output("✅ Banco de dados '" . DB_NAME . "' criado/verificado!", 'success');
        
        // Conectar ao banco específico
        $pdo->exec("USE `" . DB_NAME . "`");
        
    } catch (PDOException $e) {
        output("❌ Erro ao criar banco de dados: " . $e->getMessage(), 'error');
        exit(1);
    }
    
    // Executar migrations
    output("Executando migrations...", 'info');
    
    // Capturar output das migrations
    ob_start();
    // Incluir o arquivo migrate.php diretamente para executar as migrations
    require_once __DIR__ . '/migrations/migrate.php';
    $migrationOutput = ob_get_clean();
    
    // A lógica de sucesso/falha das migrations já está dentro de migrate.php
    // Apenas exibir o output capturado
    outputPre($migrationOutput);
    
    // Verificar se dados foram criados (após as migrations)
    output("Verificando dados criados...", 'info');
    
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
        $userCount = $stmt->fetch()['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM habits");
        $habitCount = $stmt->fetch()['count'];
        
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM badges");
        $badgeCount = $stmt->fetch()['count'];
        
        outputPre("Usuários criados: $userCount\nHábitos criados: $habitCount\nBadges criados: $badgeCount");
        
    } catch (PDOException $e) {
        output("⚠️ Erro ao verificar dados: " . $e->getMessage(), 'error');
    }
    
    // Configuração concluída
    output("🎉 Configuração concluída com sucesso!", 'success');
    
    if ($isWeb) {
        echo "<h3>Próximos passos:</h3>\n";
        echo "<ol>\n";
        echo "<li><strong>Acesse a aplicação:</strong> <a href='index.php'>index.php</a></li>\n";
        echo "<li><strong>Faça login com:</strong><br>\n";
        echo "Email: <code>admin@dailyhealthy.com</code><br>\n";
        echo "Senha: <code>admin123</code></li>\n";
        echo "<li><strong>Explore o dashboard:</strong> <a href='dashboard.php'>dashboard.php</a></li>\n";
        echo "<li><strong>Veja o ranking:</strong> <a href='ranking.php'>ranking.php</a></li>\n";
        echo "</ol>\n";
        
        echo "<h3>Informações importantes:</h3>\n";
        echo "<ul>\n";
        echo "<li>O arquivo <code>.htaccess</code> já está configurado</li>\n";
        echo "<li>As APIs estão disponíveis em <code>/api/</code></li>\n";
        echo "<li>Para reinstalar, delete o banco '" . DB_NAME . "' e execute este script novamente</li>\n";
        echo "</ul>\n";
        
        echo "<div style='margin-top: 30px; padding: 20px; background: #e8f5e8; border-radius: 5px;'>\n";
        echo "<h4>✅ DailyHealthy está pronto para uso!</h4>\n";
        echo "<p>Sua aplicação de hábitos saudáveis está configurada e funcionando.</p>\n";
        echo "</div>\n";
        
        echo "</body></html>\n";
    } else {
        echo "\n=== CONFIGURAÇÃO CONCLUÍDA ===\n";
        echo "Acesse a aplicação em seu navegador\n";
        echo "Login: admin@dailyhealthy.com\n";
        echo "Senha: admin123\n";
    }
    
} catch (Exception $e) {
    output("❌ Erro durante a configuração: " . $e->getMessage(), 'error');
    
    if ($isWeb) {
        echo "<h3>Como resolver:</h3>\n";
        echo "<ol>\n";
        echo "<li>Verifique se o XAMPP está rodando</li>\n";
        echo "<li>Confirme as configurações em config/config.php</li>\n";
        echo "<li>Verifique os logs de erro do Apache/MySQL</li>\n";
        echo "<li>Tente executar este script novamente</li>\n";
        echo "</ol>\n";
        echo "</body></html>\n";
    }
    
    exit(1);
}
?>