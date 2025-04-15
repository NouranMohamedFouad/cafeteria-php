<?php
require_once '../includes/utils.php';
require_once '../database/databaseConnection.php';
require_once '../database/product.php';
require_once '../validations/validate.php';

// session_start();
// if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
//     header('Location: login');
//     exit();
// }

$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    
    if (empty($name) || !validateAlphaOnly($name)) {
        $errors['name'] = 'Valid category name is required';
    } else {
        $productDB = ProductDB::getInstance();
        if ($productDB->addCategory($name)) {
            $success = true;
            $_SESSION['message'] = 'Category added successfully';
        } else {
            $errors['general'] = 'Failed to add category';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="../assets/stylesheet.css" rel="stylesheet">
</head>
<body>
<div class="background-overlay"></div>
    
    <?php include '../includes/header.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Add Category</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">Category added successfully! You can now go back to <a href="add_product">Add Product</a>.</div>
        <?php else: ?>
            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($errors['general']) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                           id="name" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name']) ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="add_product" class="btn btn-secondary">Back</a>
                    <button type="submit" class="btn btn-primary">Add Category</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <?php include '../includes/footer.php'; ?>
</body>
</html>