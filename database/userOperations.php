<?php

require_once  "databaseConnection.php";

class User{
    
    function createTable(){
        try{
            $conn = connect_to_db();

            $create_query = "create table if not exists `users` 
                (`id` int  auto_increment primary key, 
                `name` varchar(100) not null, 
                `email` varchar(100) unique ,
                `password` varchar(10), 
                `room` varchar(30) ,
                `ext` varchar(30) ,
                `image` varchar(255) );";

            $stmt = $conn->prepare($create_query);
            $res=$stmt->execute();

            $conn = null;


        }catch (Exception $e){
            echo $e->getMessage();

        }
    }
    
    function insert($name,$email, $pass,$room,$ext,$image){
        try{
            $conn = connect_to_db();
            if($conn){
                $inst_query = "insert into `users`(`name`, `email`, `password`, `room`, `ext`, `image`)
                values(:username, :useremail, :userpass, :userroom, :userext , :userimage); ";

                $stmt = $conn->prepare($inst_query);

                $stmt->bindParam(':username', $name);
                $stmt->bindParam(':useremail', $email);
                $stmt->bindParam(':userpass', $pass);
                $stmt->bindParam(':userroom', $room);
                $stmt->bindParam(':userext', $ext);
                $stmt->bindParam(':userimage', $image);

                $res=$stmt->execute();
                if($res){
                    $inserted_id   = $conn->lastInsertId();
                    return $inserted_id;
                }

                $conn = null;
                return false;
            }

        }catch (Exception $e){
            echo $e->getMessage();

        }
    }
};