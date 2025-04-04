<?php

//require_once "../includes/utils.php";
require_once "connectionCred.php";


function connect_to_db(){
    $pdo=false;
    try{
        $dns = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";port=".DB_PORT.";";
        $pdo = new PDO($dns, DB_USER, DB_PASSWORD);

    }catch (PDOException $e){
        echo $e->getMessage();
    }
    return $pdo;
}

