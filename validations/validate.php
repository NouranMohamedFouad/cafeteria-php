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
            $valid_data[$key] = trim($value);
        }
    }
    return ["errors" => $errors, "valid_data" => $valid_data];
}

function validateEmail($email){
    return preg_match("/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/", $email);
}

function validatePassword($password){
    return preg_match("/^[a-z0-9_]{8}$/", $password);
}

function validateAlphaOnly($string){
    return preg_match("/^[a-zA-Z]+$/", $string);
}

?>