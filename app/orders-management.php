<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");
require_once("./../database/user.php");

// Use the Order class to fetch all orders
$orderInstance = Order::getInstance();
$allOrders = $orderInstance->getAllOrders();

// Filter orders that are not "Done" or "Canceled"
$activeOrders = array_filter($allOrders, function($order) {
    return $order['status'] !== 'Done' && $order['status'] !== 'Canceled';
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Active Orders</h1>

    <div class="grid gap-6">
        <?php if (empty($activeOrders)): ?>
            <div class="p-6 bg-white rounded-2xl shadow text-center text-gray-600">
                🎉 No active orders!
            </div>
        <?php else: ?>
            <?php foreach ($activeOrders as $order): ?>
                <div class="bg-white shadow rounded-2xl p-6 flex justify-between items-center hover:shadow-lg transition">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            Order #<?= htmlspecialchars($order['id']) ?> - <?= htmlspecialchars($order['user_name']) ?>
                        </h2>
                        <p class="text-gray-600 text-sm">Room: <?= htmlspecialchars($order['room']) ?></p>
                        <p class="text-gray-600 text-sm">Total: <strong>$<?= number_format($order['total'], 2) ?></strong></p>
                        <p class="mt-1 text-sm <?= $order['status'] === 'Out for delivery' ? 'text-blue-600' : 'text-yellow-600' ?>">
                            Status: <?= htmlspecialchars($order['status']) ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Created: <?= htmlspecialchars($order['created_at']) ?></p>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($order['status'] === 'Processing'): ?>
                            <form method="POST" action="./../controllers/deliver_order.php">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded-full shadow">
                                    🚚 Deliver
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($order['status'] === 'Out for delivery'): ?>
                            <form method="POST" action="complete_order.php">
                                <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded-full shadow">
                                    ✅ Complete
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
