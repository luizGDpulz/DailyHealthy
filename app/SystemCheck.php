<?php
class SystemCheck {
    private $checks = [];
    private $errors = [];
    
    public function __construct() {
        $this->runChecks();
    }
    
    private function runChecks() {
        // Verifica permissões de diretório
        $this->checkDirectoryPermissions();
        
        // Verifica configuração do PHP
        $this->checkPHPConfiguration();
        
        // Verifica conexão com banco de dados
        $this->checkDatabaseConnection();
        
        // Verifica módulos do Apache
        $this->checkApacheModules();
    }
    
    private function checkDirectoryPermissions() {
        $directories = [
            'config' => __DIR__ . '/../../config',
            'public/assets/images' => __DIR__ . '/../assets/images',
            'public/assets/images/avatars' => __DIR__ . '/../assets/images/avatars'
        ];
        
        foreach ($directories as $name => $path) {
            if (!is_writable($path)) {
                $this->errors[] = "O diretório '$name' não tem permissão de escrita";
            } else {
                $this->checks[] = "Permissões do diretório '$name' OK";
            }
        }
    }
    
    private function checkPHPConfiguration() {
        $requirements = [
            'PHP Version' => [
                'required' => '8.0.0',
                'current' => PHP_VERSION,
                'check' => version_compare(PHP_VERSION, '8.0.0', '>=')
            ],
            'PDO MySQL' => [
                'required' => true,
                'current' => extension_loaded('pdo_mysql'),
                'check' => extension_loaded('pdo_mysql')
            ],
            'display_errors' => [
                'required' => false,
                'current' => ini_get('display_errors'),
                'check' => !ini_get('display_errors') || ini_get('display_errors') === 'Off'
            ]
        ];
        
        foreach ($requirements as $name => $requirement) {
            if (!$requirement['check']) {
                $this->errors[] = "Requisito '$name' não atendido. Requerido: {$requirement['required']}, Atual: {$requirement['current']}";
            } else {
                $this->checks[] = "Requisito '$name' OK";
            }
        }
    }
    
    private function checkDatabaseConnection() {
        try {
            $pdo = Database::getInstance();
            $this->checks[] = "Conexão com o banco de dados OK";
            
            // Verifica se as tabelas existem
            $tables = ['users', 'habits', 'habit_executions', 'badges', 'user_badges'];
            foreach ($tables as $table) {
                $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
                if ($stmt->rowCount() === 0) {
                    $this->errors[] = "Tabela '$table' não encontrada";
                } else {
                    $this->checks[] = "Tabela '$table' OK";
                }
            }
        } catch (PDOException $e) {
            $this->errors[] = "Erro na conexão com o banco de dados: " . $e->getMessage();
        }
    }
    
    private function checkApacheModules() {
        if (function_exists('apache_get_modules')) {
            $modules = apache_get_modules();
            $requiredModules = ['mod_rewrite'];
            
            foreach ($requiredModules as $module) {
                if (!in_array($module, $modules)) {
                    $this->errors[] = "Módulo Apache '$module' não está habilitado";
                } else {
                    $this->checks[] = "Módulo Apache '$module' OK";
                }
            }
        } else {
            $this->checks[] = "Não foi possível verificar módulos do Apache";
        }
    }
    
    public function getChecks() {
        return $this->checks;
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function isSystemOK() {
        return empty($this->errors);
    }
}
