<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");
require_once("./../database/userOperations.php");
// Fetch only orders that are not "Done"
$orders = SelectFromTable('orders', ['order_id', 'user_id', 'room_number', 'total_amount', 'status', 'created_at']);
$orders = array_filter($orders, fn($order) => $order['status'] !== 'Done' && $order['status'] !== 'Canceled');

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
        <?php if (empty($orders)): ?>
            <div class="p-6 bg-white rounded-2xl shadow text-center text-gray-600">
                🎉 No active orders!
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="bg-white shadow rounded-2xl p-6 flex justify-between items-center hover:shadow-lg transition">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Order #<?= $order['order_id'] ?></h2>
                        <p class="text-gray-600 text-sm">Room: <?= htmlspecialchars($order['room_number']) ?></p>
                        <p class="text-gray-600 text-sm">Total: <strong>$<?= number_format($order['total_amount'], 2) ?></strong></p>
                        <p class="mt-1 text-sm <?= $order['status'] === 'Out for delivery' ? 'text-blue-600' : 'text-yellow-600' ?>">
                            Status: <?= $order['status'] ?>
                        </p>
                        <p class="text-xs text-gray-400 mt-1">Created: <?= $order['created_at'] ?></p>
                    </div>
                    <div class="flex gap-2">
                        <?php if ($order['status'] === 'Processing'): ?>
                            <form method="POST" action="./../controllers/deliver_order.php">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded-full shadow">
                                    🚚 Deliver
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if ($order['status'] === 'Out for delivery'): ?>
                            <form method="POST" action="complete_order.php">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
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
