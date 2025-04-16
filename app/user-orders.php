<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");
require_once("./../database/user.php");

// Get Order instance
$orderInstance = Order::getInstance();

// Get filter parameters
$date1 = isset($_GET['date1']) ? $_GET['date1'] : null;
$date2 = isset($_GET['date2']) ? $_GET['date2'] : null;

// Get all orders (filtered by date if provided)
if ($date1 && $date2) {
    // Adjust end date to include the entire day
    $endDate = date('Y-m-d', strtotime($date2 . ' +1 day'));
    $orders = $orderInstance->getUserOrdersByDateRange(null, $date1, $endDate);
} else {
    $orders = $orderInstance->getAllOrders();
}

// Create User instance for lookups
$userInstance = User::getInstance();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders Overview | Caleteria</title>
  <link href="../assets/stylesheet.css" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    .order-card {
      transition: all 0.3s ease;
    }
    .order-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
  </style>
</head>
<body class="bg-gray-50">
<div class="background-overlay"></div>
<?php include '../includes/header.php'; ?>

<main class="container mx-auto px-4 py-8">
  <div class="max-w-6xl mx-auto">
    <div class="flex justify-between items-center mb-8">
      <h1 class="text-3xl font-bold text-gray-800">Orders Overview</h1>
      <div class="flex items-center space-x-4">
        <form method="get" class="flex items-center space-x-2">
          <input type="date" name="date1" value="<?php echo htmlspecialchars($date1); ?>" 
                class="border border-gray-300 rounded-lg p-2 shadow-sm focus:ring-2 focus:ring-indigo-500">
          <span>to</span>
          <input type="date" name="date2" value="<?php echo htmlspecialchars($date2); ?>" 
                class="border border-gray-300 rounded-lg p-2 shadow-sm focus:ring-2 focus:ring-indigo-500">
          <button type="submit" 
                  class="bg-indigo-600 text-white rounded-lg px-4 py-2 hover:bg-indigo-700 transition">
            Filter
          </button>
        </form>
      </div>
    </div>

    <?php if (empty($orders)): ?>
      <div class="bg-white rounded-xl shadow p-8 text-center">
        <p class="text-gray-500 text-lg">No orders found for the selected date range.</p>
      </div>
    <?php else: ?>
      <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="grid grid-cols-1 divide-y divide-gray-200">
          <?php foreach ($orders as $order): ?>
            <?php 
                $user = $userInstance->selectUserById($order['user_id']);
                $userName = $user ? htmlspecialchars($user['name']) : 'Unknown';
            ?>
            <div class="order-card p-6 hover:bg-gray-50">
              <div class="flex justify-between items-start">
                <div>
                  <div class="flex items-center space-x-4 mb-2">
                    <h3 class="text-xl font-semibold text-gray-800">Order #<?php echo htmlspecialchars($order['id']); ?></h3>
                    <span class="<?php 
                        echo match(strtolower($order['status'])) {
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                            default => 'bg-gray-100 text-gray-800'
                        };
                    ?> px-3 py-1 rounded-full text-sm">
                      <?php echo htmlspecialchars(ucfirst($order['status'])); ?>
                    </span>
                  </div>
                  <p class="text-gray-600">Customer: <?php echo $userName; ?></p>
                  <p class="text-gray-600">Date: <?php echo htmlspecialchars(date('M j, Y g:i a', strtotime($order['created_at']))); ?></p>
                  <p class="text-gray-600 mt-2">
                    Total: <span class="font-bold text-green-600">$<?php echo number_format($order['total'], 2); ?></span>
                  </p>
                </div>
                <div class="flex space-x-2">
                  <?php if (strtolower($order['status']) === 'processing'): ?>
                    <form method="POST" action="./../controllers/cancel_order.php" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                      <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                      <button type="submit" 
                              class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition">
                        Cancel Order
                      </button>
                    </form>
                  <?php elseif (strtolower($order['status']) === 'pending'): ?>
                    <form method="POST" action="./../controllers/process_order.php">
                      <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                      <button type="submit" 
                              class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition">
                        Process Order
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include '../includes/footer.php'; ?>
</body>
</html>