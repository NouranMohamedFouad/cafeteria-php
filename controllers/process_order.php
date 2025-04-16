<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");

$orderInstance = Order::getInstance();
$status='processing';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    if ($orderInstance->updateOrderStatus($orderId,$status)) {  
        // var_dump($orderInstance->updateOrderStatus($orderId,$status));
        header("Location: ../app/orders-management.php");
        exit;
    }
}
