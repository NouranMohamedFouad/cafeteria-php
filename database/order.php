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


    /**
     * Get all orders for a specific user
     * 
     * @param int $userId User ID
     * @return array Array of orders with their items
     */
    public function getUserOrders($userId) {
        try {
            // Get all orders for the user
            $stmt = $this->db->prepare("
                SELECT o.* 
                FROM orders o
                WHERE o.user_id = ?
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$userId]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get items for each order
            foreach ($orders as &$order) {
                $stmt = $this->db->prepare("
                    SELECT oi.*, p.name as product_name, p.image_path
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $orders;
        } catch (PDOException $e) {
            error_log("Error getting user orders: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get orders for a specific user within a date range
     * 
     * @param int $userId User ID
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array Array of orders with their items
     */
    public function getUserOrdersByDateRange($userId, $startDate, $endDate) {
        try {
            // Adjust end date to include the entire day
            $endDate = date('Y-m-d', strtotime($endDate . ' +1 day'));
            
            // Get all orders for the user within date range
            $stmt = $this->db->prepare("
                SELECT o.* 
                FROM orders o
                WHERE o.user_id = ?
                AND o.created_at >= ?
                AND o.created_at < ?
                ORDER BY o.created_at DESC
            ");
            $stmt->execute([$userId, $startDate, $endDate]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get items for each order
            foreach ($orders as &$order) {
                $stmt = $this->db->prepare("
                    SELECT oi.*, p.name as product_name, p.image_path
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $orders;
        } catch (PDOException $e) {
            error_log("Error getting user orders by date range: " . $e->getMessage());
            return [];
        }
    }


    /**
     * Get the most recent orders for a specific user
     * 
     * @param int $userId User ID
     * @param int $limit Number of orders to return (default 5)
     * @return array Array of recent orders with their items
     */
    public function getLatestOrdersByUser($userId, $limit = 5) {
        try {
            // Get the most recent orders for the user
            $stmt = $this->db->prepare("
                SELECT o.* 
                FROM orders o
                WHERE o.user_id = :userId
                ORDER BY o.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get items for each order
            foreach ($orders as &$order) {
                $stmt = $this->db->prepare("
                    SELECT oi.*, p.name as product_name, p.image_path
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    WHERE oi.order_id = ?
                ");
                $stmt->execute([$order['id']]);
                $order['items'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return $orders;
        } catch (PDOException $e) {
            error_log("Error getting latest user orders: " . $e->getMessage());
            return [];
        }
    }
    /**
     * Get all items for a specific order
     * 
     * @param int $orderId Order ID
     * @return array|false Array of order items if found, false otherwise
     */
    public function getOrderItemsByOrderId($orderId) {
        try {
            $stmt = $this->db->prepare("
                SELECT oi.*, p.name as product_name, p.image_path
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt->execute([$orderId]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $items ?: false;
        } catch (PDOException $e) {
            error_log("Error fetching order items: " . $e->getMessage());
            return false;
        }
    }


}