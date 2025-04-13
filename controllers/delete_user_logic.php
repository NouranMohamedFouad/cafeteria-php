<?php
    require_once "../includes/utils.php";
    require_once "../database/user.php";

    $db = User::getInstance();
    $idToDelete = $_POST['id'];
    $isDeleted=$db->deleteUser($idToDelete);
    header("Location:../app/users.php");
?>
