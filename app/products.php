<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/utils.php';
require_once '../database/databaseConnection.php';
require_once '../database/product.php';
require_once "../config/cloudinary_config.php";

// session_start();
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login');
//     exit();
// }

$productDB = ProductDB::getInstance();

if (isset($_GET['delete_id'])) {
    $productId = $_GET['delete_id'];
    
    $product = $productDB->getProductById($productId);
    
    if ($product) {
        if ($productDB->deleteProduct($productId)) {
            if (!empty($product['image_path']) && strpos($product['image_path'], 'cloudinary.com') !== false) {
                try {
                    $urlParts = parse_url($product['image_path']);
                    $path = explode('/', $urlParts['path']);
                    
                    $filenameWithExt = end($path);
                    $publicId = 'product_images/' . pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    
                    $cloudinary->uploadApi()->destroy($publicId);
                    
                } catch (Exception $e) {
                    error_log("Error deleting image from Cloudinary: " . $e->getMessage());
                }
            }
            
            $_SESSION['message'] = 'Product deleted successfully';
            header('Location: products');
            exit();
        } else {
            $_SESSION['error'] = 'Failed to delete product';
        }
    } else {
        $_SESSION['error'] = 'Product not found';
    }
    
    header('Location: products');
    exit();
}

$products = $productDB->getAllProductsWithCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Products | Bean & Crust</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link href="../assets/stylesheet.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="background-overlay"></div>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Product Management</h1>
            <a href="add_product" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Product
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">All Products</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($products)): ?>
                                <tr>
                                    <td colspan="7" class="text-center">No products found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['id']) ?></td>
                                        <td>
                                            <?php if (!empty($product['image_path'])): ?>
                                                <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                                     class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover;">
                                            <?php else: ?>
                                                <div class="bg-light text-center p-2" style="width: 80px; height: 80px;">
                                                    <i class="fas fa-image text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= htmlspecialchars($product['price']) ?> EGP</td>
                                        <td><?= htmlspecialchars($product['category_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $product['availability'] === 'available' ? 'success' : 'danger' ?>">
                                                <?= ucfirst(htmlspecialchars($product['availability'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="edit_product?id=<?= $product['id'] ?>" class="btn btn-sm btn-warning mb-2 w-75 btn-secondary">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <button class="btn btn-sm btn-danger delete-btn w-75 btn-primary" data-id="<?= $product['id'] ?>">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this product? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteButtons = document.querySelectorAll('.delete-btn');
            const confirmDelete = document.getElementById('confirmDelete');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    confirmDelete.href = `products?delete_id=${productId}`;
                    deleteModal.show();
                });
            });
        });
    </script>
</body>
</html>