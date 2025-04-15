<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/utils.php';
require_once '../database/databaseConnection.php';
require_once '../database/product.php';
require_once '../database/user.php';

// Session check for admin (uncomment when ready)
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$productDB = ProductDB::getInstance();
$userDB = User::getInstance();

// Get all products and users
$products = $productDB->getAllProductsWithCategories();
$users = $userDB->selectData();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | Cafeteria System</title>
    <link href="../assets/stylesheet.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/stylesheet.css" rel="stylesheet">
</head>
<body>
    <div class="background-overlay"></div>
    <?php include '../includes/header.php'; ?>
    <div class="container-fluid">
        <div class="row">
            <!-- Main Content Area (75%) -->
            <div class="col-md-9 p-4">
                <h1 class="mb-4">Create New Order</h1>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h3>Order Details</h3>
                    </div>
                    <div class="card-body">
                        <!-- User Selection -->
                        <div class="mb-4">
                            <label for="userSelect" class="form-label">Select Customer</label>
                            <select id="userSelect" class="form-select">
                                <option value="">-- Select a customer --</option>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user[0] ?>" data-room="<?= $user[4] ?>"><?= htmlspecialchars($user[1]) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Room Selection -->
                        <div class="mb-4">
                            <label for="roomSelect" class="form-label">Room</label>
                            <select id="roomSelect" class="form-select">
                                <option value="">-- Select room --</option>
                                <!-- Will be populated based on selected user -->
                            </select>
                        </div>
                        
                        <!-- Product Selection -->
                        <!-- Replace the product selection dropdown with a product table -->
                        <div class="mb-4">
                            <h4 class="mb-3">Select Products</h4>
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
                        
                        <!-- Order Notes -->
                        <div class="mb-4">
                            <label for="orderNotes" class="form-label">Order Notes</label>
                            <textarea id="orderNotes" class="form-control" rows="3" placeholder="Add any special instructions or notes for this order"></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Order Button -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button id="submitOrder" class="btn btn-primary" disabled>
                        <i class="fas fa-check-circle me-2"></i>Submit Order
                    </button>
                </div>
            </div>
            
            <!-- Order Cart Sidebar (25%) -->
            <div class="col-md-3 bg-light p-4" style="min-height: 100vh;">
                <div class="sticky-top pt-3">
                    <h3 class="mb-4">Order Summary</h3>
                    
                    <div id="cartItems" class="mb-4">
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                            <p>Your cart is empty</p>
                            <p class="small">Select products to add to this order</p>
                        </div>
                    </div>
                    
                    <div id="orderSummary" class="d-none">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal">0.00 EGP</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3 fw-bold">
                            <span>Total:</span>
                            <span id="total">0.00 EGP</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Item Template (Hidden) -->
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
            const userSelect = document.getElementById('userSelect');
            const roomSelect = document.getElementById('roomSelect');
            const cartItems = document.getElementById('cartItems');
            const orderSummary = document.getElementById('orderSummary');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');
            const submitOrderBtn = document.getElementById('submitOrder');
            const orderNotes = document.getElementById('orderNotes');
            
            const cart = [];
            
            // Handle user selection
            userSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const room = selectedOption.getAttribute('data-room');
                
                // Clear and populate room select
                roomSelect.innerHTML = '';
                
                if (room) {
                    const option = document.createElement('option');
                    option.value = room;
                    option.textContent = room;
                    roomSelect.appendChild(option);
                    roomSelect.value = room;
                } else {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = '-- Select room --';
                    roomSelect.appendChild(defaultOption);
                }
                
                updateSubmitButtonState();
            });
            
            // Handle product selection from table
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
                            <p class="small">Select products to add to this order</p>
                        </div>
                    `;
                    orderSummary.classList.add('d-none');
                    return;
                }
                
                // Clear cart display
                cartItems.innerHTML = '';
                
                // Get template
                const template = document.getElementById('cartItemTemplate');
                
                // Add each item to cart display
                let subtotal = 0;
                
                cart.forEach((item, index) => {
                    const itemTotal = item.price * item.quantity;
                    subtotal += itemTotal;
                    
                    const cartItem = document.importNode(template.content, true);
                    
                    // Set item details
                    cartItem.querySelector('img').src = item.image;
                    cartItem.querySelector('img').alt = item.name;
                    cartItem.querySelector('.product-name').textContent = item.name;
                    cartItem.querySelector('.product-price').textContent = `${item.price.toFixed(2)} EGP`;
                    cartItem.querySelector('.product-qty').value = item.quantity;
                    cartItem.querySelector('.item-total').textContent = `${itemTotal.toFixed(2)} EGP`;
                    
                    // Set data attribute for identification
                    const cartItemElement = cartItem.querySelector('.cart-item');
                    cartItemElement.dataset.index = index;
                    
                    // Add event listeners
                    cartItem.querySelector('.increase-qty').addEventListener('click', () => {
                        cart[index].quantity++;
                        updateCartDisplay();
                    });
                    
                    cartItem.querySelector('.decrease-qty').addEventListener('click', () => {
                        if (cart[index].quantity > 1) {
                            cart[index].quantity--;
                            updateCartDisplay();
                        }
                    });
                    
                    cartItem.querySelector('.product-qty').addEventListener('change', (e) => {
                        const newQty = parseInt(e.target.value);
                        if (newQty >= 1) {
                            cart[index].quantity = newQty;
                            updateCartDisplay();
                        } else {
                            e.target.value = cart[index].quantity;
                        }
                    });
                    
                    cartItem.querySelector('.remove-item').addEventListener('click', () => {
                        cart.splice(index, 1);
                        updateCartDisplay();
                        updateSubmitButtonState();
                    });
                    
                    cartItems.appendChild(cartItem);
                });
                
                // Update summary
                subtotalElement.textContent = `${subtotal.toFixed(2)} EGP`;
                totalElement.textContent = `${subtotal.toFixed(2)} EGP`;
                orderSummary.classList.remove('d-none');
            }
            
            // Update submit button state
            function updateSubmitButtonState() {
                const hasUser = userSelect.value !== '';
                const hasRoom = roomSelect.value !== '';
                const hasItems = cart.length > 0;
                
                submitOrderBtn.disabled = !(hasUser && hasRoom && hasItems);
            }
            
            // Handle order submission
            submitOrderBtn.addEventListener('click', function() {
                // Disable button to prevent double submission
                submitOrderBtn.disabled = true;
                submitOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
                
                const orderData = {
                    userId: userSelect.value,
                    room: roomSelect.value,
                    notes: orderNotes.value,
                    items: cart.map(item => ({
                        productId: item.id,
                        quantity: item.quantity,
                        price: item.price
                    }))
                };
                
                // Send order data to server
                fetch('../controllers/order_controller.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert('Order created successfully!');
                        
                        // Reset form
                        userSelect.value = '';
                        roomSelect.innerHTML = '<option value="">-- Select room --</option>';
                        orderNotes.value = '';
                        cart.length = 0;
                        updateCartDisplay();
                        updateSubmitButtonState();
                    } else {
                        // Show error message
                        alert('Error: ' + data.message);
                        
                        // Re-enable submit button
                        submitOrderBtn.disabled = false;
                        submitOrderBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Submit Order';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while processing your order. Please try again.');
                    
                    // Re-enable submit button
                    submitOrderBtn.disabled = false;
                    submitOrderBtn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Submit Order';
                });
            });
        });
    </script>
</body>
</html>