<?php


function validatePostData($postData){
    $errors = [];
    $valid_data = [];
    foreach ($postData as $key => $value) {
        if(! isset($value) or empty($value)){
            $errors[$key] = ucfirst("{$key} is required");
        }
        else if($key == "email" && !validateEmail($value)){
            $errors["email"] = "Invalid email";
        }
        else if($key == "password" && !validatePassword($value)){
            $errors["password"] = "Invalid Password";
        }
        else{
            if (is_array($value)) {
                $valid_data[$key] = array_map('trim', $value);
            } else {
                $valid_data[$key] = trim($value);
            } 
        }
    }

    return ["errors" => $errors, "valid_data" => $valid_data];
}

function validateEmail($email){
    return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
}

function validatePassword($password){
    return preg_match("/^[a-z0-9_]{8,15}$/", $password);
}

function isPasswordMatch($pass,$confirmPass){
    if($pass == $confirmPass){
        return true;
    }
    return false;
}

function validateUploadedFile($files, $extensions){

    $errors = [];
    $valid_data = [];

    foreach ($files as $file) {
        if (empty($file['tmp_name'])) {
            $errors["image"] = ucfirst("Image is required");
            return ["errors" => $errors, "valid_data" => $valid_data];
        }else{
            $tmp_name = explode("/", $file['tmp_name']);
            $valid_data['tmp_name'] = end($tmp_name);
        }
        $extention = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extention, $extensions)) {
            $errors["image"] = ucfirst("Invalid file extension");
        }else{
            $valid_data['extension'] = $extention;
        }
    }
    return ["errors" => $errors, "valid_data" => $valid_data];
}

function isEmailUnique($email, $userInstance,$id=null) {

    $existingUser = $userInstance->emailExists($email);
 
    if ($existingUser) {
        if ($id && $existingUser['id'] == $id) {
            return true;
        }
        return false;
    }

    return true;
}




?>
