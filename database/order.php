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

    // Change the status of an order, based on order Id passed to it
    // The only change that can be made is from processing to canceled
    // Incomplete function
    function ChangeOrderStatus(){
        try {
            $conn = ConnectPDO();
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
    
    



?>