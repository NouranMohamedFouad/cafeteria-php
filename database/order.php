<?php
const  DB_HOST = "localhost";
const  DB_USER = "root";
const  DB_PASSWORD = "password";
const  DB_NAME = "Haven_Brew_Cafeteria";
const DB_PORT = 3306;
$Defaultcolumns = ["created_at", "status", "total_amount"];
function ConnectPDO(){
    try{
        // Data Source Name (DSN) String
        $dsn = "mysql:host=127.0.0.1;dbname=Haven_Brew_Cafeteria;port=3306";
        // PDO connection creation
        $pdo = new PDO($dsn,"root","password");
        // var_dump($pdo);
    }
    catch(PDOException $e){
        // Display error message
        echo "". $e->getMessage();
    }
    return $pdo;
}

// We pass the table name and the selected columns from it in an array
function SelectFromTable($tableName, $columns){
    try {
        $conn = ConnectPDO();
        $columns = implode(", ", $columns);
        $query = "select $columns from $tableName";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $conn = null;
        // var_dump($data);
        return $data;
    }
    catch (Exception $e) {
        echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        return [];
    }
}

  

    // Select orders from the database based on the provided date range
    function FilterOrdersByDate($filter, $Defaultcolumns) {
        try {
            $conn = ConnectPDO();
    
            // Default columns
            
            $columns = implode(", ", $Defaultcolumns);
            
            // Table name (assumed to be orders)
            $tableName = "orders";
            var_dump($filter);
    
            // SQL query to fetch orders
            $query = "SELECT $columns FROM $tableName WHERE 1";
    
            // Bind parameters for date filtering
            $params = [];
            
            // Check if date1 and/or date2 are provided
            if ($filter['date1'] && $filter['date2']) {
                $query .= " AND created_at BETWEEN :date1 AND :date2";
                $params[':date1'] = $filter['date1'];
                $params[':date2'] = $filter['date2'];
            } elseif ($filter['date1']) {
                $query .= " AND created_at >= :date1";
                $params[':date1'] = $filter['date1'];
            } elseif ($filter['date2']) {
                $query .= " AND created_at <= :date2";
                $params[':date2'] = $filter['date2']. ' 23:59:59';
            }
    
            // Prepare and execute the query
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
    
            return $data;
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            return [];
        }
    }
    
    // Helper function to count the retrieved orders for the pagination function
    function CountFilteredOrders($orders)
    {
        return count($orders);
    }

    // Paginate the retrieved orders
    function GetPaginatedOrders($filter, $Defaultcolumns, $limit, $offset) {
        try {
            $conn = ConnectPDO();
            $columns = implode(", ", $Defaultcolumns);
            $tableName = "orders";
    
            $query = "SELECT $columns FROM $tableName WHERE 1";
            $params = [];
    
            // Apply date filters
            if ($filter['date1'] && $filter['date2']) {
                $query .= " AND created_at BETWEEN :date1 AND :date2";
                $params[':date1'] = $filter['date1'];
                $params[':date2'] = $filter['date2'];
            } elseif ($filter['date1']) {
                $query .= " AND created_at >= :date1";
                $params[':date1'] = $filter['date1'];
            } elseif ($filter['date2']) {
                $query .= " AND created_at <= :date2";
                $params[':date2'] = $filter['date2'] . ' 23:59:59';
            }
    
            // Append LIMIT and OFFSET
            $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    
            $stmt = $conn->prepare($query);
    
            // Bind normal params first
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
    
            // Bind limit and offset as integers
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $conn = null;
    
            return $data;
    
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            return [];
        }
    }
  // Change the status of an order from Processing to Canceled
    function CancelOrder($orderId) {
        if (!is_numeric($orderId) || $orderId <= 0) {
            return "Invalid order ID.";
        }
    
        $conn = ConnectPDO();
    
        try {
            $conn->beginTransaction();
    
            // Fetch current status
            $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = :id FOR UPDATE");
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$order) {
                $conn->rollBack();
                return "Order not found.";
            }
    
            if ($order['status'] !== 'Processing') {
                $conn->rollBack();
                return "Only orders in 'Processing' status can be cancelled.";
            }
    
            // Update status
            $update = $conn->prepare("UPDATE orders SET status = 'Canceled' WHERE order_id = :id");
            $update->execute([':id' => $orderId]);  
            $conn->commit();
            return "Order cancelled successfully.";
    
        } catch (Exception $e) {
            $conn->rollBack();
            return "Error cancelling order: " . $e->getMessage();
        }
    }
    function DeliverOrder($orderId) {
        if (!is_numeric($orderId) || $orderId <= 0) {
            return "Invalid order ID.";
        }
    
        $conn = ConnectPDO();
    
        try {
            $conn->beginTransaction();
    
            // Fetch current status
            $stmt = $conn->prepare("SELECT status FROM orders WHERE order_id = :id FOR UPDATE");
            $stmt->execute([':id' => $orderId]);
            $order = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if (!$order) {
                $conn->rollBack();
                return "Order not found.";
            }
    
            if ($order['status'] !== 'Processing') {
                $conn->rollBack();
                return "Only orders in 'processing' status can be delivered.";
            }
    
            // Update status
            $update = $conn->prepare("UPDATE orders SET status = 'Out for delivery' WHERE order_id = :id");
            $update->execute([':id' => $orderId]);  
            $conn->commit();
            return "Order is out for delivery.";
    
        } catch (Exception $e) {
            $conn->rollBack();
            return "Error delivering order: " . $e->getMessage();
        }
    }
?>