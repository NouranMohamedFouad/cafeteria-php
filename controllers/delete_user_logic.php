<?php
    require_once "../includes/utils.php";
    require_once "../database/user.php";
    require_once "../config/cloudinary_config.php";


    $db = User::getInstance();
    $idToDelete = $_POST['id'];

    $user = $db->selectUserById($idToDelete);
    if (!empty($user['image']) && strpos($user['image'], 'cloudinary.com') !== false) {
        try {
            $urlParts = parse_url($user['image']);
            $path = explode('/', $urlParts['path']);
            
            $filenameWithExt = end($path);
            $publicId = 'user_profiles/' . pathinfo($filenameWithExt, PATHINFO_FILENAME);
            
            $cloudinary->uploadApi()->destroy($publicId);
            
        } catch (Exception $e) {
            error_log("Error deleting image from Cloudinary: " . $e->getMessage());
        }
    }

    $isDeleted=$db->deleteUser($idToDelete);
    header("Location:../app/users.php");
?>
