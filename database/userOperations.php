<?php

require_once  "databaseConnection.php";

class User{
    private static $instance = null;
    private $db;
    
    private function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new User();
        }
        return self::$instance;
    }
    
    public function createTable(){
        try{
            $create_query = "create table if not exists `users` 
                (`id` int  auto_increment primary key, 
                `name` varchar(100) not null, 
                `email` varchar(100) unique ,
                `password` varchar(10), 
                `room` varchar(30) ,
                `ext` varchar(30) ,
                `image` varchar(255) );";

            $stmt = $this->db->prepare($create_query);
            $res=$stmt->execute();

            $conn = null;


        }catch (Exception $e){
            echo $e->getMessage();

        }
    }
    
    public function insert($name,$email,$pass,$room,$ext,$image){
        try{
                $inst_query = "insert into `users`(`name`, `email`, `password`, `room`, `ext`, `image`)
                values(:username, :useremail, :userpass, :userroom, :userext , :userimage); ";

                $stmt = $this->db->prepare($inst_query);

                $stmt->bindParam(':username', $name);
                $stmt->bindParam(':useremail', $email);
                $stmt->bindParam(':userpass', $pass);
                $stmt->bindParam(':userroom', $room);
                $stmt->bindParam(':userext', $ext);
                $stmt->bindParam(':userimage', $image);

                $res=$stmt->execute();
                if($res){
                    $inserted_id =$this->db->lastInsertId();
                    return $inserted_id;
                }

                $conn = null;
                return false;

        }catch (Exception $e){
            echo $e->getMessage();

        }
    }

    public function selectData(){
        $data = [];
        try{
           
            $select_query="select * from `users`";
            $stmt=$this->db->prepare($select_query);
            $res=$stmt->execute();
            $data=$stmt->fetchAll(PDO::FETCH_NUM);

        }catch (Exception $e){
            echo $e->getMessage();
        }
        return $data;

    }

    public function selectUserById($id){
        $data = [];
        try{
                $select_query = "select * from `users` where `id` = :userid";
                $stmt =$this->db->prepare($select_query); 
                $stmt->bindParam(':userid', $id, PDO::PARAM_INT);
                $res=$stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

        }catch (Exception $e){
            displayError($e->getMessage());
        }
        return $data;
    }

    public function updateUser($id,$name,$email,$pass,$room,$ext,$image){

        try{
                $fieldsToUpdate['name'] = $name;
                $fieldsToUpdate['email'] = $email;
                $fieldsToUpdate['password'] = $pass;
                $fieldsToUpdate['room'] = $room;
                $fieldsToUpdate['ext'] = $ext;
                $fieldsToUpdate['image'] = $image;
                
                $setClause = [];
                foreach ($fieldsToUpdate as $column => $value) {
                    $setClause[] = "`$column` = :$column";
                }

                $update_query = "update `users` set " . implode(", ", $setClause) . " where `id` = :id";


                $stmt=$this->db->prepare($update_query);
                
                foreach ($fieldsToUpdate as $column => $value) {
                    $stmt->bindValue(":$column", $value);
                }
                $stmt->bindValue(":id", $id, PDO::PARAM_INT);

                $res = $stmt->execute();

                if($res){
                    $affected_rows = $stmt->rowCount();
                }

                if($affected_rows){
                    return $affected_rows;
                }
            return false;
        }catch (Exception $e){
            echo $e->getMessage();

        }
    }

};