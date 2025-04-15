<?php
require_once "databaseConnection.php";

class Order {
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Order();
        }
        return self::$instance;
    }
    
    /**
     * Create a new order
     * 
     * @param int $userId User ID
     * @param string $room Room number
     * @param string $notes Order notes
     * @param float $total Total order amount
     * @return int|false The order ID if successful, false otherwise
     */
    public function createOrder($userId, $room, $notes, $total) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("INSERT INTO orders (user_id, room, notes, total, status, created_at) 
                                       VALUES (?, ?, ?, ?, 'pending', NOW())");
            $stmt->execute([$userId, $room, $notes, $total]);
            
            $orderId = $this->db->lastInsertId();
            
            $this->db->commit();
            return $orderId;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error creating order: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Add order items to an existing order
     * 
     * @param int $orderId Order ID
     * @param array $items Array of order items (product_id, quantity, price)
     * @return bool True if successful, false otherwise
     */
    public function addOrderItems($orderId, $items) {
        try {
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                       VALUES (?, ?, ?, ?)");
            
            foreach ($items as $item) {
                $stmt->execute([
                    $orderId,
                    $item['productId'],
                    $item['quantity'],
                    $item['price']
                ]);
            }
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error adding order items: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all orders with user information
     * 
     * @return array Array of orders
     */
    public function getAllOrders() {
        try {
            $stmt = $this->db->prepare("
                SELECT o.*, u.name as user_name 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting orders: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get order details including items
     * 
     * @param int $orderId Order ID
     * @return array|false Order details if found, false otherwise
     */
    public function getOrderDetails($orderId) {
        try {
            // Get order info
            $stmt = $this->db->prepare("
                SELECT o.*, u.name as user_name 
                FROM orders o
                JOIN users u ON o.user_id = u.id
                WHERE o.id = ?
            ");
            $stmt->execute([$orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$order) {
                return false;
            }
            
            // Get order items
            $stmt = $this->db->prepare("
                SELECT oi.*, p.name as product_name, p.image_path
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $order;
        } catch (PDOException $e) {
            error_log("Error getting order details: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update order status
     * 
     * @param int $orderId Order ID
     * @param string $status New status
     * @return bool True if successful, false otherwise
     */
    public function updateOrderStatus($orderId, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $orderId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error updating order status: " . $e->getMessage());
            return false;
        }
    }
}