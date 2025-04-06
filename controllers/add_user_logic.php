<?php

    require_once "../includes/utils.php";
    require_once "../validations/validateData.php";
    require_once "../database/userOperations.php";

    $formDataIssues = validatePostData($_POST);
    $formErrors = $formDataIssues["errors"];
    $oldData= $formDataIssues["valid_data"];


    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $matched=isPasswordMatch($password,$confirm_password);
    if(!$matched){
        $formErrors["confirm_password"]="confirm password didn't match";
    }


    $file_errors = validateUploadedFile($_FILES, ['png', 'jpg', 'jpeg']);
    $image_errors = $file_errors["errors"];
    $validImageData = $file_errors["valid_data"];


    if(count($image_errors)) {
        $formErrors=array_merge($formErrors, $image_errors);

    }


    if(count($formErrors)) {
        $errors = json_encode($formErrors);
        $queryString ="errors={$errors}";
        $old_data = json_encode($oldData);
        if($old_data){
            $queryString .= "&old={$old_data}";
        }
        header("location:../app/add_user.php?{$queryString}");
    }
    else {

            $name = $_POST['name'];
            $email = $_POST['email'];
            $room = $_POST['room'];
            $ext = $_POST['ext'];

            $image_name = "{$validImageData['tmp_name']}.{$validImageData['extension']}";

            $image_tmp = $_FILES['image']['tmp_name'];
      
            $uploaded=move_uploaded_file($image_tmp, "../uploads/" . $image_name);
            $imagePath = "../uploads/" . $image_name;
            if($uploaded){
                echo "<h1> Image uploaded successfully </h1>";
            }else{
                echo "<h1> Error uploading image </h1>";
            }

            $db = User::getInstance();

            $db->createTable();
            
            $id =$db->insert($name,$email,$password,$room,$ext,$imagePath);

    
            if($id) {
                header("location:../app/users.php");
            }
            else{
                echo '<h1 class="mt-5 fw-bold text-danger">Contact Support</h1>';
            }  
            
            

}

?>
