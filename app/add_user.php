<?php

    $errors=[];
    $old_data=[];

    if(isset($_GET["errors"])){
        $errors = $_GET["errors"];
        echo "<br>";
        $errors = json_decode($errors, true);
    }

    if(isset($_GET["old"])){
        $old_data=$_GET["old"];
        $old_data = json_decode($old_data, true);
    }

    

?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link href="../assets/stylesheet.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Raleway:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            width: 100%;
            background-color: #f0e6c3;
            color: #5d4037;
            border-radius: 30px;
            padding: 5%;
        }
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--dark-color);
        }

        .btn-secondary:hover {
            background-color: #c0a080;
            border-color: #c0a080;
        }

        .btn-outline-secondary {
            border-color: var(--secondary-color);
            color: var(--dark-color);
        }

        .btn-outline-secondary:hover {
            background-color: var(--secondary-color);
        }
    </style>
</head>
<body class="container mt-5">
<div class="background-overlay"></div>
    
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Add New User</h1>
            <a href="users.php" class="btn btn-primary">
            <i class="fa-solid fa-eye mx-2"></i>Show All Users
            </a>
    </div>
    <form action="../controllers/add_user_logic" method="post" class="shadow p-4 rounded form" enctype="multipart/form-data">

        <!-- User Details Section -->
        <div class="p-4 mb-4" style="background-color: #6d4c41; color: white;">
            <h5 class="fw-bold">User Details</h5>
        </div>

        <div class="p-4 mb-4" style="background-color: white;">
            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control"
                value='<?php echo isset($old_data["name"]) ? $old_data["name"] : ""; ?>'>
                <div class="text-danger font-weight-bold">
                    <?php echo isset($errors["name"]) ? $errors["name"] : ""; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email"
                value='<?php echo isset($old_data["email"]) ? $old_data["email"] : ""; ?>' required>
                <div class="text-danger font-weight-bold">
                    <?php echo isset($errors["email"]) ? $errors["email"] : ""; ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control"
                value='<?php echo isset($old_data["password"]) ? $old_data["password"] : ""; ?>'>
                <div class="text-danger font-weight-bold">
                    <?php echo isset($errors["password"]) ? $errors["password"] : ""; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                value='<?php echo isset($old_data["confirm_password"]) ? $old_data["confirm_password"] : ""; ?>' required>
                <div class="text-danger font-weight-bold">
                    <?php echo isset($errors["confirm_password"]) ? $errors["confirm_password"] : ""; ?>
                </div>
            </div>

            <div class="mb-3">
                <label for="room" class="form-label">Room No.:</label>
                <select class="form-select" id="room" name="room">
                    <option value="Application1">Application1</option>
                    <option value="Application2">Application2</option>
                    <option value="Cloud">Cloud</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="ext" class="form-label">Extension:</label>
                <input type="text" class="form-control" id="ext" name="ext"
                value='<?php echo isset($old_data["ext"]) ? $old_data["ext"] : ""; ?>'>
                <div class="text-danger font-weight-bold">
                    <?php echo isset($errors["ext"]) ? $errors["ext"] : ""; ?>
                </div>
            </div>
        </div>

        <!-- Profile Image Section -->
        <div class="p-4 mb-4" style="background-color: #6d4c41; color: white;">
            <h5 class="fw-bold">Profile Image</h5>
        </div>

        <div class="p-4 mb-4" style="background-color: white;">
            <div class="mb-3">
                <label for="image" class="form-label">Profile Picture:</label>
                <input type="file" class="form-control" id="file" name="image" required>
                <div class="text-danger font-weight-bold">
                    <?php echo isset($errors["image"]) ? $errors["image"] : ""; ?>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <button type="reset" class="btn btn-secondary  px-5">Reset</button>
            <button type="submit" class="btn px-5 btn-primary">Add</button>
        </div>

        </form>

    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>















