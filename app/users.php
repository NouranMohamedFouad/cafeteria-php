
<?php
    require_once "../includes/utils.php";
    require_once "../helpers/printData.php";
    require_once "../database/user.php";


    $table  =[];
    $db = User::getInstance();
    $table=$db->selectData();
    // var_dump($table);
    // exit;
    if ($table) {
        foreach ($table as &$row) {
            $image = $row[6];        
            unset($row[6]);     
            array_unshift($row, $image);
        }

        $headers = ["Profile","Name","Room no."];

        drawTable($headers, $table);
    }
    else{
        echo '<body style="background-color: rgb(32, 30, 30);"><h1 class="text-center mt-5 fw-bold text-primary">No Records Found !</h1> </body>';
    }
    
?>
