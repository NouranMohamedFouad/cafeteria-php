<?php
require_once "../includes/utils.php";
require_once "../includes/EnvManager.php";

// Load environment variables
EnvManager::load();

class DatabaseConnection {
    private static $instance = null;
    private $connection;
    
    // private constructor to prevent direct instantiation
    private function __construct() {
        try {
            // Build DSN with SSL options
            $dsn = sprintf(
                "mysql:host=%s;dbname=%s;port=%s;sslmode=REQUIRED",
                EnvManager::get('DB_HOST', 'localhost'),
                EnvManager::get('DB_NAME', 'cafeteria'),
                EnvManager::get('DB_PORT', '3306')
            );
            
            // SSL options for PDO
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
                PDO::MYSQL_ATTR_SSL_CA => false
            ];
            
            $this->connection = new PDO(
                $dsn, 
                EnvManager::get('DB_USER', 'root'),
                EnvManager::get('DB_PASSWORD', ''),
                $options
            );
            
            $this->initializeDatabase();
        } catch (PDOException $e) {
            displayError($e->getMessage());
        }
    }
    
    // get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new DatabaseConnection();
        }
        return self::$instance;
    }
    
    private function initializeDatabase() {
        try {
            // create categories table
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS categories (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL UNIQUE,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
            
            // create products table
            $this->connection->exec("
                CREATE TABLE IF NOT EXISTS products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(255) NOT NULL,
                    price DECIMAL(10, 2) NOT NULL,
                    category_id INT NOT NULL,
                    image_path VARCHAR(255) NOT NULL,
                    availability ENUM('available', 'unavailable') NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (category_id) REFERENCES categories(id)
                )
            ");
            
            // add default categories
            $stmt = $this->connection->query("SELECT COUNT(*) FROM categories");
            if ($stmt->fetchColumn() == 0) {
                $defaultCategories = ['Hot Drinks', 'Cold Drinks', 'Desserts'];
                $insertStmt = $this->connection->prepare("INSERT INTO categories (name) VALUES (?)");
                foreach ($defaultCategories as $category) {
                    $insertStmt->execute([$category]);
                }
            }
            
        } catch (PDOException $e) {
            error_log("Database initialization error: " . $e->getMessage());
            throw $e;
        }
    }

    // return connection
    public function getConnection() {
        return $this->connection;
    }
    
    // prevent cloning of the instance
    private function __clone() {}
    
}

// function in case any old code still uses connect_to_db not instance->getConnection()
function connect_to_db() {
    return DatabaseConnection::getInstance()->getConnection();
}