<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $orderId = $_POST['order_id'];

    if (CancelOrder($orderId)) {  
        var_dump(CancelOrder($orderId) );
        exit;
    }
}
