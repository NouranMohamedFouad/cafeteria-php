<?php

    require_once "../includes/utils.php";
    require_once "../validations/validateData.php";
    require_once "../database/user.php";
    require_once "../config/cloudinary_config.php";

    session_start();
    $_SESSION['edit_user_id'] = $_POST['id'];

    $db = User::getInstance();
    $db->createTable();

    $formDataIssues = validatePostData($_POST);
    $formErrors = $formDataIssues["errors"];
    $oldData= $formDataIssues["valid_data"];


    $password='';
    // $password = $_POST['password'];
    // $confirm_password = $_POST['confirm_password'];
    // $matched=isPasswordMatch($password,$confirm_password);
    // if(!$matched){
    //     $formErrors["confirm_password"]="confirm password didn't match";
    // }


    $file_errors = validateUploadedFile($_FILES, ['png', 'jpg', 'jpeg']);
    $image_errors = $file_errors["errors"];
    $validImageData = $file_errors["valid_data"];


    if(count($image_errors)) {
        $formErrors=array_merge($formErrors, $image_errors);

    }
    $name = $_POST['name'];
    $email = $_POST['email'];
    $room = $_POST['room'];
    $ext = $_POST['ext'];
    $id = $_POST['id'];

    if (!isEmailUnique($email,$db)){
        
        $emailError = ["email" => "Email is already taken"];
        $formErrors = array_merge($formErrors, $emailError);
    }


    if(count($formErrors)) {
        $errors = json_encode($formErrors);
        $queryString ="errors={$errors}";
        $old_data = json_encode($oldData);
        if($old_data){
            $queryString .= "&old={$old_data}";
        }
        header("location:../app/edit_user.php?{$queryString}");
    }
    else {

            if ($_FILES['image']['tmp_name']) {

                $image_name = "{$validImageData['tmp_name']}.{$validImageData['extension']}";
                $image_tmp = $_FILES['image']['tmp_name'];

                try {
                    $uploadResponse = $cloudinary->uploadApi()->upload($image_tmp, [
                        'folder' => 'user_profiles',
                        'public_id' => pathinfo($image_name, PATHINFO_FILENAME),
                        'overwrite' => true,
                        'resource_type' => 'image'
                    ]);

                    $imagePath = $uploadResponse['secure_url'];

                    echo "<h1> Image uploaded successfully </h1>";

                    if ($oldData['image']) {
                                                
                        $old_image_public_id = pathinfo($oldData['image'], PATHINFO_FILENAME);
                        \Cloudinary\Uploader::destroy('user_profiles/' . $old_image_public_id);
                    }

                } catch (Exception $e) {
                    echo "<h1> Error uploading image to Cloudinary: {$e->getMessage()} </h1>";
                    exit;
                }

                
            } else {
                $imagePath = $oldData['image'];
            }

            
            $updated=$db->updateUser($id,$name,$email,$password,$room,$ext,$imagePath,$oldData);
            
    
            if($updated) {
                header("location:../app/users.php");
            }
            else{
                echo '<h1 class="mt-5 fw-bold text-danger">Contact Support</h1>';
            }  
            
            

}

?>
