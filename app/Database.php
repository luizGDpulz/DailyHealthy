<?php
/**
 * DailyHealthy - Classe Database
 * Gerenciamento de conexão com banco de dados usando PDO
 */

class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Erro de conexão com o banco de dados: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->connection;
    }
    
    // Prevenir clonagem
    private function __clone() {}
    
    // Prevenir deserialização
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Executar query com parâmetros
     */
    public static function query($sql, $params = []) {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }
    
    /**
     * Buscar um registro
     */
    public static function fetchOne($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetch();
    }
    
    /**
     * Buscar múltiplos registros
     */
    public static function fetchAll($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->fetchAll();
    }
    
    /**
     * Inserir registro e retornar ID
     */
    public static function insert($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return self::getInstance()->lastInsertId();
    }
    
    /**
     * Atualizar/Deletar e retornar número de linhas afetadas
     */
    public static function execute($sql, $params = []) {
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
    
    /**
     * Iniciar transação
     */
    public static function beginTransaction() {
        return self::getInstance()->beginTransaction();
    }
    
    /**
     * Confirmar transação
     */
    public static function commit() {
        return self::getInstance()->commit();
    }
    
    /**
     * Reverter transação
     */
    public static function rollback() {
        return self::getInstance()->rollback();
    }
    
    /**
     * Verificar se tabela existe
     */
    public static function tableExists($tableName) {
        $sql = "SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = ? AND table_name = ? LIMIT 1";
        $stmt = self::query($sql, [DB_NAME, $tableName]);
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Criar banco de dados se não existir
     */
    public static function createDatabaseIfNotExists() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            return true;
        } catch (PDOException $e) {
            error_log("Erro ao criar banco de dados: " . $e->getMessage());
            return false;
        }
    }
}
?>

