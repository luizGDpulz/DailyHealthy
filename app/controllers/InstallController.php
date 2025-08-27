<?php
class InstallController {
    private $installKey;
    
    public function __construct() {
        // Chave de instalação única gerada na primeira execução
        $this->installKey = '123456789'; // Você deve mudar isso para uma chave segura
    }
    
    public function index() {
        // Verifica se o sistema já está instalado
        if ($this->isInstalled() && !isset($_GET['force'])) {
            die('Sistema já está instalado. Use ?force=true para forçar a reinstalação.');
        }
        
        include __DIR__ . '/../../public/install.php';
    }
    
    public function runMigrations() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }
        
        // Verifica a chave de instalação
        if (!isset($_POST['install_key']) || $_POST['install_key'] !== $this->installKey) {
            die('Chave de instalação inválida');
        }
        
        try {
            // Inicia o buffer de saída para capturar as mensagens
            ob_start();
            
            require_once __DIR__ . '/../../config/config.php';
            require_once __DIR__ . '/../../app/Database.php';
            
            $pdo = Database::getInstance();
            
            // Executa as migrations
            $migrations = [
                'CreateUsers' => '001_create_users.php',
                'CreateHabits' => '002_create_habits.php',
                'CreateHabitExecutions' => '003_create_habit_executions.php',
                'CreateBadges' => '004_create_badges.php',
                'CreateUserBadges' => '005_create_user_badges.php'
            ];
            
            foreach ($migrations as $class => $file) {
                require_once __DIR__ . '/../../migrations/' . $file;
                echo "Executando: $class\n";
                $class::up($pdo);
                echo "✅ $class concluída\n";
            }
            
            // Marca como instalado
            $this->markAsInstalled();
            
            $output = ob_get_clean();
            echo nl2br(htmlspecialchars($output));
            
            echo "<br><br><strong>Sistema instalado com sucesso!</strong>";
            echo "<br><a href='/DailyHealthy/public/login'>Ir para o login</a>";
            
        } catch (Exception $e) {
            ob_end_clean();
            die('Erro durante a instalação: ' . $e->getMessage());
        }
    }
    
    private function isInstalled() {
        return file_exists(__DIR__ . '/../../config/installed.txt');
    }
    
    private function markAsInstalled() {
        file_put_contents(__DIR__ . '/../../config/installed.txt', date('Y-m-d H:i:s'));
    }
}
