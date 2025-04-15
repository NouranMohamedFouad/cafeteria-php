<?php
require_once("./../includes/utils.php");
require_once("./../database/order.php");

$filter = [
    'date1' => isset($_GET['date1']) ? $_GET['date1'] : null,
    'date2' => isset($_GET['date2']) ? $_GET['date2'] : null
];
$Defaultcolumns = ["created_at", "status", "total_amount"];

// Call the function to retrieve filtered orders
$orders = FilterOrdersByDate($filter,$Defaultcolumns);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Orders Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      border: none;
      border-radius: 16px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      transition: 0.3s ease-in-out;
    }
    .card:hover {
      transform: scale(1.02);
    }
    .container {
      max-width: 90%;
    }
    .section-title {
      font-weight: 600;
      color: #2d3436;
    }
    .form-inline input {
      width: 150px;
      margin-right: 10px;
    }
  </style>
</head>
<body>

<div class="container py-5">
  <h2 class="text-center mb-4 section-title">Orders Menu</h2>

  <!-- Date Filter Form -->
  <form method="get" class="form-inline mb-4">
    <div class="d-flex justify-content-center">
      <input type="date" name="date1" value="<?php echo htmlspecialchars($filter['date1']); ?>" class="form-control me-2">
      <input type="date" name="date2" value="<?php echo htmlspecialchars($filter['date2']); ?>" class="form-control me-2">
      <button type="submit" class="btn btn-primary">Filter</button>
    </div>
  </form>

  <?php if (empty($orders)): ?>
    <p class="text-center">No orders found for the selected date range.</p>
  <?php else: ?>
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Date Created</th>
          <th>Status</th>
          <th>Total Paid</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $order): ?>
          <tr>
            <td><?php echo htmlspecialchars($order['created_at']); ?></td>
            <td><?php echo htmlspecialchars($order['status']); ?></td>
            <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

