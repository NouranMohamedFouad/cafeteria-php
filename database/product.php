<?php
require_once "databaseConnection.php";

class ProductDB {
    private static $instance = null;
    private $db;
    
    // private constructor to prevent direct instantiation
    private function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }
    
    // get the singleton instance
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new ProductDB();
        }
        return self::$instance;
    }
    
    public function addProduct($name, $price, $categoryId, $imagePath) {
        try {
            $stmt = $this->db->prepare("INSERT INTO products (name, price, category_id, image_path, availability) 
                                        VALUES (:name, :price, :category_id, :image_path, :availability)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':image_path', $imagePath);
            
            // Bind the availability explicitly as 'available'
            $availability = 'available';
            $stmt->bindParam(':availability', $availability);
    
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding product: " . $e->getMessage());
            return false;
        }
    }
    
    
    public function getCategories() {
        try {
            $stmt = $this->db->query("SELECT id, name FROM categories ORDER BY name");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }
    
    public function addCategory($name) {
        try {
            // if category already exists -> return false, else add the new category
            $stmt = $this->db->prepare("SELECT id FROM categories WHERE name = :name");
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            
            if ($stmt->fetch()) {
                return false; // category exists
            }
            
            // add the new category
            $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (:name)");
            $stmt->bindParam(':name', $name);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error adding category: " . $e->getMessage());
            return false;
        }
    }
    

    public function getAllProductsWithCategories() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                ORDER BY p.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching products: " . $e->getMessage());
            return [];
        }
    }

    public function deleteProduct($productId) {
        try {
            // First get the image path to delete the file
            $stmt = $this->db->prepare("SELECT image_path FROM products WHERE id = :id");
            $stmt->bindParam(':id', $productId);
            $stmt->execute();
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($product) {
                // Delete the product
                $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
                $stmt->bindParam(':id', $productId);
                $result = $stmt->execute();
                
                // If deletion was successful, delete the image file
                if ($result && file_exists($product['image_path'])) {
                    unlink($product['image_path']);
                }
                
                return $result;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    public function getProductById($productId) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, c.name as category_name 
                FROM products p 
                JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id
            ");
            $stmt->bindParam(':id', $productId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching product: " . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($productId, $name, $price, $categoryId, $availability, $imagePath = null) {
        try {
            if ($imagePath) {
                // Update with new image
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET name = :name, price = :price, category_id = :category_id, 
                        availability = :availability, image_path = :image_path 
                    WHERE id = :id
                ");
                $stmt->bindParam(':image_path', $imagePath);
            } else {
                // Update without changing image
                $stmt = $this->db->prepare("
                    UPDATE products 
                    SET name = :name, price = :price, category_id = :category_id, 
                        availability = :availability 
                    WHERE id = :id
                ");
            }
            
            $stmt->bindParam(':id', $productId);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $categoryId);
            $stmt->bindParam(':availability', $availability);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }

    // prevent cloning of the instance
    private function __clone() {}
    

}

// function getProductDB() {
//     return ProductDB::getInstance();
// }