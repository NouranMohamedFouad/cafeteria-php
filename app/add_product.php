<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require_once '../includes/utils.php';
require_once '../database/databaseConnection.php';
require_once '../database/product.php';
require_once '../validations/validate.php';
require_once "../config/cloudinary_config.php";

// check if user is admin  ----> may be changed later
// session_start();
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login');
//     exit();
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $price = $_POST['price'] ?? '';
    $category = $_POST['category'] ?? '';
    $errors = [];

    // validate inputs
    if (empty($name)|| !validateAlphaOnly($name)) $errors['name'] = 'Valid product name is required';
    if (empty($price)) $errors['price'] = 'Price is required';
    if (empty($category)) $errors['category'] = 'Category is required';

    // Handle file upload
    // $imagePath = '';
    // if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    //     $uploadDir = '../uploads/';
    //     $imageName = uniqid() . '_' . basename($_FILES['image']['name']);
    //     $imagePath = $uploadDir . $imageName;
        
    //     if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
    //         $errors['image'] = 'Failed to upload image';
    //     }
    // } else {
    //     $errors['image'] = 'Product image is required';
    // }

    // $image_name = $_FILES['image']['name'];
    // $imagePath = "../uploads/" . $image_name;
    // $image_tmp = $_FILES['image']['tmp_name'];

    // $valid_extensions = array("jpeg", "jpg", "png");
    // $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);

    // if(empty($image_name) and empty($image_tmp)  or ! in_array($ext, $valid_extensions)) {
    //     $errors['image'] = "Failed to upload image.";
    // }else{
    //     $image_name = explode("/", $image_tmp);
    //     $image_name = end($image_name).".".$ext;

    //     $uploaded=move_uploaded_file($image_tmp, "../uploads/" . $image_name);
    // }

    $file_errors = validateUploadedFile($_FILES, ['png', 'jpg', 'jpeg']);
    $image_errors = $file_errors["errors"];
    $validImageData = $file_errors["valid_data"];
    $image_name = "{$validImageData['tmp_name']}.{$validImageData['extension']}";
    $image_tmp = $_FILES['image']['tmp_name'];

    try {
        $uploadResponse = $cloudinary->uploadApi()->upload($image_tmp, [
            'folder' => 'product_images',
            'public_id' => pathinfo($image_name, PATHINFO_FILENAME),
            'overwrite' => true,
            'resource_type' => 'image'
        ]);

        $imagePath = $uploadResponse['secure_url'];
        if (empty($errors)) {
            $productDB = ProductDB::getInstance();
            if ($productDB->addProduct($name, $price, $category, $imagePath)) {
                $_SESSION['message'] = 'Product added successfully';
                header('Location: products');
                exit();
            } else {
                var_dump($productDB);
                $errors['general'] = 'Failed to add product';
            }
        }
    
        echo "<h1> Image uploaded successfully </h1>";

    } catch (Exception $e) {
        echo "<h1> Error uploading image to Cloudinary: {$e->getMessage()} </h1>";
        exit;
    }


}


$productDB = ProductDB::getInstance();
$categories = $productDB->getCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product | Bean & Crust</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/stylesheet.css" rel="stylesheet">

</head>
<body>
<div class="background-overlay"></div>
    
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Add New Product</h1>
            <a href="products" class="btn btn-primary">
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
                               id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">Price</label>
                            <div class="input-group">
                                <input type="number" step="0.01" class="form-control <?= isset($errors['price']) ? 'is-invalid' : '' ?>" 
                                       id="price" name="price" value="<?= htmlspecialchars($_POST['price'] ?? '') ?>">
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
                                            <?= (isset($_POST['category']) && $_POST['category'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <a href="add_category" class="btn btn-outline-secondary">Add Category</a>
                                <?php if (isset($errors['category'])): ?>
                                    <div class="invalid-feedback"><?= htmlspecialchars($errors['category']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-4">
                <div class="card-header">
                    <h2>Product Image</h2>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Choose Image</label>
                        <input class="form-control <?= isset($errors['image']) ? 'is-invalid' : '' ?>" 
                               type="file" id="image" name="image">
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback"><?= htmlspecialchars($errors['image']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-between">
                <button type="reset" class="btn btn-secondary">Reset</button>
                <button type="submit" class="btn btn-primary">Add Product</button>
            </div>
        </form>
    </div>

    <?php include '../includes/footer.php'; ?>
</body>
</html>