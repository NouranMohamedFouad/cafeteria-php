<?php

function drawTable($header, $tableData, $delete='#', $show="#", $edit='../app/edit_user.php') {

    echo '<body style="background-color: rgb(32, 30, 30);"> <div class="table-responsive mt-10">
        <table class="table table-hover table-bordered align-middle">
            <thead class="table-dark">
            <tr>';
    foreach ($header as $value) {
        echo "<th>$value</th>";
    }
    echo "<th>Show</th> <th>Edit</th> <th>Delete</th>";
    echo "</tr></thead><tbody>";

    foreach ($tableData as $row) {
        echo "<tr>";
        foreach ($row as $index=>$field) {
            if($index==6){
                echo "<td><img src='{$field}' width='100' height='100'> </td>";

            }
            else if($index==0 || $index==2 || $index==3 || $index==5){
                continue;

            }
            else {
                echo "<td>{$field}</td>";
            }
        }
        echo "<td> <form method='post' action='{$show}'> 
            <input type='hidden' name='id' value='{$row[0]}'>
            <input type='submit' class='btn btn-info' value='Show'>
            </form> </td>


            <td> <form method='post' action='{$edit}'> 
            <input type='hidden' name='id' value='{$row[0]}'>
            <input type='submit' class='btn btn-warning' value='edit'>
            </form> </td>


            <td> <form method='post' action='{$delete}'> 
            <input type='hidden' name='id' value='{$row[0]}'>
            <input type='submit' class='btn btn-danger' value='Delete'>
            </form> </td>";
            
        echo "</tr>";
    }

    echo "</tbody></table></div> </div> </body>";

}


?>