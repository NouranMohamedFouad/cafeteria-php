<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/utils.php';
require_once '../database/databaseConnection.php';
require_once '../database/user.php';
require_once '../database/order.php';

// Session check for admin
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$userDB = User::getInstance();
$orderDB = Order::getInstance();

// Get all users for the initial table
$users = $userDB->selectData();

// Check if a specific user is selected
$selectedUserId = isset($_GET['userId']) ? intval($_GET['userId']) : null;
$selectedUser = null;
$userOrders = [];

// Date range filters
$startDate = isset($_GET['startDate']) ? $_GET['startDate'] : '';
$endDate = isset($_GET['endDate']) ? $_GET['endDate'] : '';

if ($selectedUserId) {
    $selectedUser = $userDB->selectUserById($selectedUserId);
    
    // Get orders for the selected user with optional date filtering
    if (!empty($startDate) && !empty($endDate)) {
        $userOrders = $orderDB->getUserOrdersByDateRange($selectedUserId, $startDate, $endDate);
    } else {
        $userOrders = $orderDB->getUserOrders($selectedUserId);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Order Checks | Bean & Crust</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/stylesheet.css" rel="stylesheet">
</head>
<body>
<div class="background-overlay"></div>
    
    <?php include '../includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 p-4">
                <h1 class="mb-4">User Order Checks</h1>
                
                <?php if (!$selectedUserId): ?>
                <!-- Users Table -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Select User to View Orders</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Room</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($user[0]) ?></td>
                                        <td><?= htmlspecialchars($user[1]) ?></td>
                                        <td><?= htmlspecialchars($user[4]) ?></td>
                                        <td>
                                            <a href="checks?userId=<?= $user[0] ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye me-1"></i> View Orders
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <?php else: ?>
                <!-- User Orders View -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Orders for <?= htmlspecialchars($selectedUser['name']) ?></h3>
                        <a href="checks" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Users
                        </a>
                    </div>
                    <div class="card-body">
                        <!-- Date Range Filter -->
                        <form action="checks" method="GET" class="row mb-4">
                            <input type="hidden" name="userId" value="<?= $selectedUserId ?>">
                            <div class="col-md-4">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" id="startDate" name="startDate" class="form-control" value="<?= $startDate ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" id="endDate" name="endDate" class="form-control" value="<?= $endDate ?>">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">Filter</button>
                                <?php if (!empty($startDate) || !empty($endDate)): ?>
                                <a href="checks?userId=<?= $selectedUserId ?>" class="btn btn-secondary">Clear</a>
                                <?php endif; ?>
                            </div>
                        </form>
                        
                        <?php if (empty($userOrders)): ?>
                        <div class="alert alert-info">
                            No orders found for this user<?= (!empty($startDate) && !empty($endDate)) ? ' in the selected date range' : '' ?>.
                        </div>
                        <?php else: ?>
                        
                        <!-- Orders Accordion -->
                        <div class="accordion" id="ordersAccordion">
                            <?php foreach ($userOrders as $index => $order): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $index ?>">
                                    <button class="accordion-button <?= ($index > 0) ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="<?= ($index === 0) ? 'true' : 'false' ?>" aria-controls="collapse<?= $index ?>">
                                        <div class="d-flex justify-content-between w-100 me-3">
                                            <span>Order #<?= htmlspecialchars($order['id']) ?></span>
                                            <span>Date: <?= date('M d, Y H:i', strtotime($order['created_at'])) ?></span>
                                            <span class="badge bg-<?= getStatusBadgeClass($order['status']) ?>"><?= ucfirst(htmlspecialchars($order['status'])) ?></span>
                                            <span>Total: $<?= number_format($order['total'], 2) ?></span>
                                        </div>
                                    </button>
                                </h2>
                                <div id="collapse<?= $index ?>" class="accordion-collapse collapse <?= ($index === 0) ? 'show' : '' ?>" aria-labelledby="heading<?= $index ?>" data-bs-parent="#ordersAccordion">
                                    <div class="accordion-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <p><strong>Room:</strong> <?= htmlspecialchars($order['room']) ?></p>
                                                <?php if (!empty($order['notes'])): ?>
                                                <p><strong>Notes:</strong> <?= htmlspecialchars($order['notes']) ?></p>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-6 text-md-end">
                                                <p><strong>Status:</strong> <?= ucfirst(htmlspecialchars($order['status'])) ?></p>
                                                <p><strong>Order Date:</strong> <?= date('F d, Y H:i:s', strtotime($order['created_at'])) ?></p>
                                            </div>
                                        </div>
                                        
                                        <h5 class="mb-3">Order Items</h5>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Price</th>
                                                        <th>Quantity</th>
                                                        <th>Subtotal</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($order['items'] as $item): ?>
                                                    <tr>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                                <?= htmlspecialchars($item['product_name']) ?>
                                                            </div>
                                                        </td>
                                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                                        <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th colspan="3" class="text-end">Total:</th>
                                                        <th>$<?= number_format($order['total'], 2) ?></th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Helper function to get appropriate badge class based on order status
function getStatusBadgeClass($status) {
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'completed':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>