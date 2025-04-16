<?php

require_once("./../includes/utils.php");
require_once("./../database/order.php");
require_once("./../database/user.php");

// Fetch all orders
$orderInstance = Order::getInstance();
$allOrders = $orderInstance->getAllOrders();

// Filter active orders
$activeOrders = array_filter($allOrders, function($order) {
    return $order['status'] !== 'completed' && $order['status'] !== 'cancelled';
});

// Create a User instance for lookups
$userInstance = User::getInstance();

// Helper function to fetch user name
function getUserName($userId, $userInstance) {
    $user = $userInstance->selectUserById($userId);
    return $user ? htmlspecialchars($user['name']) : "Unknown User";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Orders Dashboard</title>
    <link href="../assets/stylesheet.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen p-8">
<?php include '../includes/header.php'; ?>
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Active Orders</h1>

    <div class="grid gap-6">
        <?php if (empty($activeOrders)): ?>
            <div class="p-6 bg-white rounded-2xl shadow text-center text-gray-600">
                🎉 No active orders at the moment!
            </div>
        <?php else: ?>
            <?php foreach ($activeOrders as $order): ?>
                <?php 
                    $userName = getUserName($order['user_id'], $userInstance);
                    $orderItems = $orderInstance->getOrderItemsByOrderId($order['id']);
                ?>
                <div class="bg-white shadow rounded-2xl overflow-hidden hover:shadow-lg transition">
                    <div class="p-6 flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">
                                Order #<?= htmlspecialchars($order['id']) ?> — <?= $userName ?>
                            </h2>
                            <p class="text-gray-600 text-sm">Room: <?= htmlspecialchars($order['room']) ?></p>
                            <p class="text-gray-600 text-sm">Notes: <?= $order['notes'] ? htmlspecialchars($order['notes']) : 'No notes' ?></p>
                            
                            <p class="mt-1 text-sm 
                                <?php 
                                    echo match($order['status']) {
                                        'pending' => 'text-gray-500',
                                        'processing' => 'text-yellow-600',
                                        'completed' => 'text-green-600',
                                        'cancelled' => 'text-red-600',
                                        default => 'text-gray-400'
                                    };
                                ?>">
                                Status: <?= ucfirst($order['status']) ?>
                            </p>

                            <p class="text-xs text-gray-400 mt-1">Created: <?= date('F j, Y, g:i a', strtotime($order['created_at'])) ?></p>
                        </div>

                        <div class="flex gap-2">
                            <?php if ($order['status'] === 'pending'): ?>
                                <form method="POST" action="./../controllers/process_order.php">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white text-sm px-4 py-2 rounded-full shadow">
                                        🔄 Start Processing
                                    </button>
                                </form>
                            <?php elseif ($order['status'] === 'processing'): ?>
                                <form method="POST" action="./../controllers/complete_order.php">
                                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order['id']) ?>">
                                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded-full shadow">
                                        ✅ Mark Completed
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($orderItems): ?>
                        <div class="border-t border-gray-100 px-6 py-4 bg-gray-50">
                            <h3 class="font-medium text-gray-700 mb-2">Order Items</h3>
                            <div class="grid gap-3">
                                <?php foreach ($orderItems as $item): ?>
                                    <div class="flex justify-between items-center text-sm">
                                        <div class="flex items-center gap-3">
                                            <?php if (!empty($item['image_path'])): ?>
                                                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="w-10 h-10 object-cover rounded">
                                            <?php endif; ?>
                                            <div>
                                                <p class="font-medium text-gray-800"><?= htmlspecialchars($item['product_name']) ?></p>
                                                <p class="text-gray-500">Qty: <?= htmlspecialchars($item['quantity']) ?></p>
                                            </div>
                                        </div>
                                        <p class="font-medium">$<?= number_format($item['price'], 2) ?></p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="border-t border-gray-100 px-6 py-3 bg-gray-50 flex justify-between items-center">
                        <p class="text-sm text-gray-500">Order Total</p>
                        <p class="font-bold">$<?= number_format($order['total'], 2) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>