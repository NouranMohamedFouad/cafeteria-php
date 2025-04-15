<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once '../database/databaseConnection.php';
require_once '../database/product.php';
require_once '../database/order.php';

$productDB = ProductDB::getInstance();
$orderDB = Order::getInstance();
$products = $productDB->getAllProductsWithCategories();
$latestOrders = $orderDB->getLatestOrdersByUser($_SESSION['user_id'], 5);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | Cafeteria System</title>
    <link href="../assets/stylesheet.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="background-overlay"></div>
    <?php include '../includes/header.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content Area (75%) -->
            <div class="col-md-9 p-4">
                <!-- Latest Orders Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Latest Orders</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php if (empty($latestOrders)): ?>
                                <div class="col-12">
                                    <div class="text-center text-muted py-5">
                                        <i class="fas fa-receipt fa-3x mb-3"></i>
                                        <p>No orders yet</p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <?php foreach ($latestOrders as $order): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">Order #<?= $order['id'] ?></h6>
                                                <p class="text-muted small mb-2">
                                                    <?= date('M d, H:i', strtotime($order['created_at'])) ?>
                                                </p>
                                                <div class="mb-2">
                                                    <?php foreach ($order['items'] as $item): ?>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <i class="fas fa-mug-hot me-2"></i>
                                                            <span><?= htmlspecialchars($item['product_name']) ?> x <?= htmlspecialchars($item['quantity']) ?></span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 
                                                        ($order['status'] === 'processing' ? 'warning' : 'secondary') ?>">
                                                        <?= ucfirst($order['status']) ?>
                                                    </span>
                                                    <span class="fw-bold"><?= number_format($order['total'], 2) ?> EGP</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Available Products Section -->
                <div class="card">
                    <div class="card-header">
                        <h3>Available Products</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">No products available</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($products as $product): ?>
                                            <?php if ($product['availability'] === 'available'): ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?= $product['image_path'] ?>" 
                                                             alt="<?= htmlspecialchars($product['name']) ?>" 
                                                             class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                    </td>
                                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                                    <td><?= htmlspecialchars($product['price']) ?> EGP</td>
                                                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-primary add-to-cart"
                                                                data-id="<?= $product['id'] ?>"
                                                                data-name="<?= htmlspecialchars($product['name']) ?>"
                                                                data-price="<?= htmlspecialchars($product['price']) ?>"
                                                                data-image="<?= htmlspecialchars($product['image_path']) ?>">
                                                            <i class="fas fa-plus"></i> Add
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Cart Sidebar (25%) -->
            <div class="col-md-3 bg-light p-4" style="min-height: 100vh;">
                <div class="sticky-top pt-3">
                    <h3 class="mb-4">Your Order</h3>
                    
                    <div id="cartItems" class="mb-4">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Your cart is empty</p>
                            <p class="small">Select products to add to your order</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="orderNotes" class="form-label">Notes</label>
                        <textarea id="orderNotes" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="room" class="form-label">Room</label>
                        <select id="room" class="form-select">
                            <option value="Application1">Application1</option>
                            <option value="Application2">Application2</option>
                            <option value="Cloud">Cloud</option>
                        </select>
                    </div>
                    
                    <div id="orderSummary" class="d-none">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total:</span>
                            <span id="total">0.00 EGP</span>
                        </div>
                    </div>

                    <button id="submitOrder" class="btn btn-primary w-100" disabled>
                        <i class="fas fa-check-circle me-2"></i>Place Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Item Template -->
    <template id="cartItemTemplate">
        <div class="card mb-2 cart-item">
            <div class="card-body p-2">
                <div class="d-flex align-items-center">
                    <img src="" alt="" class="img-thumbnail me-2" style="width: 50px; height: 50px; object-fit: cover;">
                    <div class="flex-grow-1">
                        <h6 class="product-name mb-0"></h6>
                        <div class="text-muted product-price small"></div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-2">
                    <div class="input-group input-group-sm" style="max-width: 120px;">
                        <button class="btn btn-outline-secondary decrease-qty" type="button">-</button>
                        <input type="number" class="form-control text-center product-qty" value="1" min="1">
                        <button class="btn btn-outline-secondary increase-qty" type="button">+</button>
                    </div>
                    <button class="btn btn-sm btn-outline-danger remove-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="text-end mt-1">
                    <span class="fw-bold item-total"></span>
                </div>
            </div>
        </div>
    </template>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cartItems = document.getElementById('cartItems');
            const orderSummary = document.getElementById('orderSummary');
            const totalElement = document.getElementById('total');
            const submitOrderBtn = document.getElementById('submitOrder');
            const orderNotes = document.getElementById('orderNotes');
            const roomSelect = document.getElementById('room');
            const cartItemTemplate = document.getElementById('cartItemTemplate');
            
            const cart = [];

            // Handle adding products to cart
            document.querySelectorAll('.add-to-cart').forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    const productPrice = parseFloat(this.getAttribute('data-price'));
                    const productImage = this.getAttribute('data-image');
                    
                    // Check if product already in cart
                    const existingItem = cart.find(item => item.id === productId);
                    
                    if (existingItem) {
                        // Increment quantity if already in cart
                        existingItem.quantity++;
                        updateCartDisplay();
                    } else {
                        // Add new item to cart
                        cart.push({
                            id: productId,
                            name: productName,
                            price: productPrice,
                            image: productImage,
                            quantity: 1
                        });
                        updateCartDisplay();
                    }
                    
                    updateSubmitButtonState();
                });
            });

            // Update cart display
            function updateCartDisplay() {
                if (cart.length === 0) {
                    cartItems.innerHTML = `
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Your cart is empty</p>
                            <p class="small">Select products to add to your order</p>
                        </div>
                    `;
                    orderSummary.classList.add('d-none');
                    return;
                }

                cartItems.innerHTML = '';
                let total = 0;

                cart.forEach(item => {
                    const cartItem = cartItemTemplate.content.cloneNode(true);
                    const itemTotal = item.price * item.quantity;
                    total += itemTotal;

                    // Set item details
                    cartItem.querySelector('img').src = item.image;
                    cartItem.querySelector('img').alt = item.name;
                    cartItem.querySelector('.product-name').textContent = item.name;
                    cartItem.querySelector('.product-price').textContent = `${item.price} EGP`;
                    cartItem.querySelector('.product-qty').value = item.quantity;
                    cartItem.querySelector('.item-total').textContent = `${itemTotal.toFixed(2)} EGP`;

                    // Add event listeners for quantity controls
                    const itemElement = cartItem.querySelector('.cart-item');
                    itemElement.dataset.productId = item.id;

                    const qtyInput = cartItem.querySelector('.product-qty');
                    const decreaseBtn = cartItem.querySelector('.decrease-qty');
                    const increaseBtn = cartItem.querySelector('.increase-qty');
                    const removeBtn = cartItem.querySelector('.remove-item');

                    qtyInput.addEventListener('change', (e) => {
                        const newQty = parseInt(e.target.value) || 1;
                        updateItemQuantity(item.id, newQty);
                    });

                    decreaseBtn.addEventListener('click', () => {
                        const newQty = Math.max(1, item.quantity - 1);
                        updateItemQuantity(item.id, newQty);
                    });

                    increaseBtn.addEventListener('click', () => {
                        updateItemQuantity(item.id, item.quantity + 1);
                    });

                    removeBtn.addEventListener('click', () => {
                        removeFromCart(item.id);
                    });

                    cartItems.appendChild(cartItem);
                });

                orderSummary.classList.remove('d-none');
                totalElement.textContent = `${total.toFixed(2)} EGP`;
            }

            // Update item quantity
            function updateItemQuantity(productId, newQuantity) {
                const item = cart.find(item => item.id === productId);
                if (item) {
                    item.quantity = Math.max(1, newQuantity);
                    updateCartDisplay();
                    updateSubmitButtonState();
                }
            }

            // Remove item from cart
            function removeFromCart(productId) {
                const index = cart.findIndex(item => item.id === productId);
                if (index !== -1) {
                    cart.splice(index, 1);
                    updateCartDisplay();
                    updateSubmitButtonState();
                }
            }

            // Update submit button state
            function updateSubmitButtonState() {
                submitOrderBtn.disabled = cart.length === 0 || !roomSelect.value;
            }

            // Handle room selection
            roomSelect.addEventListener('change', updateSubmitButtonState);

            // Handle order submission
            submitOrderBtn.addEventListener('click', function() {
                if (cart.length === 0) return;

                const orderData = {
                    userId: <?= $_SESSION['user_id'] ?>,
                    items: cart.map(item => ({
                        productId: item.id,
                        quantity: item.quantity,
                        price: item.price
                    })),
                    notes: orderNotes.value,
                    room: roomSelect.value
                };

                // Send order to server
                fetch('/controllers/order_controller', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('Order placed successfully!');
                        // Clear cart
                        cart.length = 0;
                        orderNotes.value = '';
                        updateCartDisplay();
                        updateSubmitButtonState();
                        // Reload page to show new order in latest orders
                        location.reload();
                    } else {
                        alert('Failed to place order: ' + (result.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    alert('Error placing order: ' + error.message);
                });
            });
        });
    </script>
</body>
</html>