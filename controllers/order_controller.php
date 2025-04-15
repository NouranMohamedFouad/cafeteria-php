<?php
// Turn off output buffering and start a new buffer
ob_end_clean();
ob_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../database/order.php';
require_once '../database/product.php';
require_once '../database/user.php';
require_once '../includes/utils.php';

class OrderController {
    private $orderDB;
    private $productDB;
    private $userDB;
    
    public function __construct() {
        $this->orderDB = Order::getInstance();
        $this->productDB = ProductDB::getInstance();
        $this->userDB = User::getInstance();
    }
    
    /**
     * Handle order creation
     */
    public function handleCreateOrder() {
        // Check if request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendJsonResponse(false, 'Invalid request method');
            return;
        }
        
        // Get JSON data from request
        $jsonData = file_get_contents('php://input');
        $data = json_decode($jsonData, true);
        
        if (!$data) {
            $this->sendJsonResponse(false, 'Invalid JSON data');
            return;
        }
        
        // Validate required fields
        if (empty($data['userId']) || empty($data['room']) || empty($data['items']) || !is_array($data['items'])) {
            $this->sendJsonResponse(false, 'Missing required fields');
            return;
        }
        
        // Validate user exists
        $user = $this->userDB->selectUserById($data['userId']);
        if (!$user) {
            $this->sendJsonResponse(false, 'Invalid user');
            return;
        }
        
        // Validate items
        if (count($data['items']) === 0) {
            $this->sendJsonResponse(false, 'Order must contain at least one item');
            return;
        }
        
        // Validate each item
        $validatedItems = [];
        $total = 0;
        
        foreach ($data['items'] as $item) {
            if (empty($item['productId']) || empty($item['quantity']) || $item['quantity'] < 1) {
                $this->sendJsonResponse(false, 'Invalid item data');
                return;
            }
            
            // Verify product exists and is available
            $product = $this->productDB->getProductById($item['productId']);
            if (!$product || $product['availability'] !== 'available') {
                $this->sendJsonResponse(false, 'Product not available: ' . ($product['name'] ?? 'Unknown'));
                return;
            }
            
            // Use the actual price from database for security
            $price = $product['price'];
            $itemTotal = $price * $item['quantity'];
            $total += $itemTotal;
            
            $validatedItems[] = [
                'productId' => $item['productId'],
                'quantity' => $item['quantity'],
                'price' => $price
            ];
        }
        
        // Create order
        $notes = $data['notes'] ?? '';
        $orderId = $this->orderDB->createOrder($data['userId'], $data['room'], $notes, $total);
        
        if (!$orderId) {
            $this->sendJsonResponse(false, 'Failed to create order');
            return;
        }
        
        // Add order items
        $success = $this->orderDB->addOrderItems($orderId, $validatedItems);
        
        if (!$success) {
            $this->sendJsonResponse(false, 'Failed to add order items');
            return;
        }
        
        // Return success response
        $this->sendJsonResponse(true, 'Order created successfully', [
            'orderId' => $orderId,
            'total' => $total
        ]);
    }
    
    /**
     * Send JSON response
     * 
     * @param bool $success Success status
     * @param string $message Response message
     * @param array $data Additional data
     */
    private function sendJsonResponse($success, $message, $data = []) {
        // Clear any previous output
        ob_end_clean();
        
        // Set JSON headers
        header('Content-Type: application/json');
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        
        // Output JSON response
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }
}

// Instantiate and handle the request
$controller = new OrderController();
$controller->handleCreateOrder();