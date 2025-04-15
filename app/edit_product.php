<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../includes/utils.php';
require_once '../database/databaseConnection.php';
require_once '../database/product.php';
require_once '../validations/validate.php';
require_once "../config/cloudinary_config.php";

// session_start();
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login.php');
//     exit();
// }

$productDB = ProductDB::getInstance();

$productId = $_GET['id'] ?? 0;

if (!$productId) {
    header("location:notfound.php"); 
    exit;
}

$product = $productDB->getProductById($productId);

if (!$product) {
    $_SESSION['error'] = 'Product not found';
    header('Location: products.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $availability = $_POST['availability'] ?? 'available';
    $errors = [];

    // validate inputs
    if (empty($name) || !validateAlphaOnly($name)) $errors['name'] = 'Valid product name is required';
    if (empty($price)) $errors['price'] = 'Price is required';
    if (empty($category)) $errors['category'] = 'Category is required';

    // handle file upload
    $imagePath = $product['image_path']; // Keep current image by default
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_errors = validateUploadedFile($_FILES, ['png', 'jpg', 'jpeg']);
        $image_errors = $file_errors["errors"];
        $validImageData = $file_errors["valid_data"];
        $image_name = "{$validImageData['tmp_name']}.{$validImageData['extension']}";
        $image_tmp = $_FILES['image']['tmp_name'];

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

        try {
            $uploadResponse = $cloudinary->uploadApi()->upload($image_tmp, [
                'folder' => 'product_images',
                'public_id' => pathinfo($image_name, PATHINFO_FILENAME),
                'overwrite' => true,
                'resource_type' => 'image'
            ]);

            $imagePath = $uploadResponse['secure_url'];
        } catch (Exception $e) {
            $errors['image'] = 'Error uploading image to Cloudinary: ' . $e->getMessage();
        }
    }

    if (empty($errors)) {
        if ($productDB->updateProduct($productId, $name, $price, $category, $availability, $imagePath)) {
            $_SESSION['message'] = 'Product updated successfully';
            header('Location: products.php');
            exit();
        } else {
            $errors['general'] = 'Failed to update product';
        }
    }
}

$categories = $productDB->getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | Bean & Crust</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/stylesheet.css" rel="stylesheet">
</head>
<body>
<div class="background-overlay"></div>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Product</h1>
            <a href="products.php" class="btn btn-primary">
                <i class="fa-solid fa-eye mx-2"></i>Show All Products
            </a>
        </div>
        
        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Product Details</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name</label>
                        <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                               id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                       id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>">
                                <span class="input-group-text">EGP</span>
                                <?php if (isset($errors['price'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['price']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <div class="input-group">
                                <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" 
                                        id="category" name="category">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['id']) ?>" 
                                            <?= ($product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <a href="add_category.php" class="btn btn-outline-secondary">Add Category</a>
                                <?php if (isset($errors['category'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['category']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="availability" class="form-label">Availability</label>
                        <select class="form-select" id="availability" name="availability">
                            <option value="available" <?= $product['availability'] === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="unavailable" <?= $product['availability'] === 'unavailable' ? 'selected' : '' ?>>Unavailable</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Product Image</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Current Image</label>
                        <div>
                            <img src="<?= htmlspecialchars($product['image_path']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Change Image (leave blank to keep current)</label>
                        <input class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" 
                               type="file" id="image" name="image">
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="products.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Product</button>
            </div>
        </form>
    </div>
</body>
</html>