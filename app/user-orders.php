<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");
require_once("./../controllers/cancel_order.php");
$filter = [
    'date1' => isset($_GET['date1']) ? $_GET['date1'] : null,
    'date2' => isset($_GET['date2']) ? $_GET['date2'] : null
];

// Make sure to fetch the 'id' too!
$Defaultcolumns = ["order_id", "created_at", "status", "total_amount"];

// Pagination setup
$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = 10; 
$offset = ($page - 1) * $limit;

// Fetch paginated results
$orders = GetPaginatedOrders($filter, $Defaultcolumns, $limit, $offset);

// Fetch total count for pagination calculation
$totalOrders = CountFilteredOrders($filter);  // Count using your filter, not the array
$totalPages = ceil($totalOrders / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders Menu</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-10">

<div class="w-full max-w-5xl bg-white shadow-xl rounded-2xl p-8">
  <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">📦 Orders Overview</h2>

  <!-- Filter Form -->
  <form method="get" class="flex flex-col md:flex-row justify-center gap-4 mb-8">
    <input type="date" name="date1" value="<?php echo htmlspecialchars($filter['date1']); ?>" 
           class="border border-gray-300 rounded-lg p-2 w-44 shadow-sm focus:ring-2 focus:ring-indigo-500">
    <input type="date" name="date2" value="<?php echo htmlspecialchars($filter['date2']); ?>" 
           class="border border-gray-300 rounded-lg p-2 w-44 shadow-sm focus:ring-2 focus:ring-indigo-500">
    <button type="submit" 
            class="bg-indigo-600 text-white rounded-lg px-5 py-2 hover:bg-indigo-700 transition">Filter</button>
  </form>

  <?php if (empty($orders)): ?>
    <p class="text-center text-gray-500">No orders found for the selected date range.</p>
  <?php else: ?>
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto text-center text-gray-700">
        <thead>
          <tr class="bg-indigo-50">
            <th class="px-4 py-3 text-sm font-semibold">Date Created</th>
            <th class="px-4 py-3 text-sm font-semibold">Status</th>
            <th class="px-4 py-3 text-sm font-semibold">Total Paid</th>
            <th class="px-4 py-3 text-sm font-semibold">Action</th> <!-- New Action column -->
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          <?php foreach ($orders as $order): ?>
            <tr class="hover:bg-indigo-50">
              <td class="px-4 py-2"><?php echo htmlspecialchars($order['created_at']); ?></td>
              <td class="px-4 py-2"><?php echo htmlspecialchars($order['status']); ?></td>
              <td class="px-4 py-2 text-green-600 font-semibold">$<?php echo htmlspecialchars($order['total_amount']); ?></td>
              <td class="px-4 py-2">
                <?php if ($order['status'] === 'Processing'): ?>
                  <form method="POST" action="./../controllers/cancel_order.php" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                    <button type="submit" 
                            class="bg-red-500 text- white px-3 py-1 rounded hover:bg-red-600 transition">
                      Cancel
                    </button>
                  </form>
                <?php else: ?>
                  <span class="text-gray-400 italic">—</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination Controls -->
    <div class="flex justify-center mt-8 space-x-2">
      <?php if ($page > 1): ?>
        <a href="?date1=<?php echo urlencode($filter['date1']); ?>&date2=<?php echo urlencode($filter['date2']); ?>&page=<?php echo $page - 1; ?>" 
           class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">« Prev</a>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?date1=<?php echo urlencode($filter['date1']); ?>&date2=<?php echo urlencode($filter['date2']); ?>&page=<?php echo $i; ?>"
           class="px-4 py-2 rounded <?php echo ($i === $page) ? 'bg-indigo-600 text-white' : 'bg-gray-200 hover:bg-gray-300'; ?>">
           <?php echo $i; ?>
        </a>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <a href="?date1=<?php echo urlencode($filter['date1']); ?>&date2=<?php echo urlencode($filter['date2']); ?>&page=<?php echo $page + 1; ?>" 
           class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Next »</a>
      <?php endif; ?>
    </div>
  <?php endif; ?>
</div>

</body>
</html>
