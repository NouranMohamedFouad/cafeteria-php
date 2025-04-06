<?php

function drawTable($header, $tableData, $delete='../controllers/delete_user_logic.php', $show="#", $edit='../app/edit_user.php') {

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


            <td>
                <form method='post' action='{$delete}' class='delete-form'>
                    <input type='hidden' name='id' value='{$row[0]}'>
                    <button type='button' class='btn btn-danger delete-btn' data-id='{$row[0]}'>Delete</button>
                </form>
            </td>
            ";
            
        echo "</tr>";
    }

    echo "</tbody></table></div> </div> ";

    echo "
     <!-- Delete Confirmation Modal -->
     <div class=\"modal fade\" id=\"deleteModal\" tabindex=\"-1\" aria-labelledby=\"deleteModalLabel\" aria-hidden=\"true\">
        <div class=\"modal-dialog\">
            <div class=\"modal-content\">
                <div class=\"modal-header\">
                    <h5 class=\"modal-title\" id=\"deleteModalLabel\">Confirm Deletion</h5>
                    <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                </div>
                <div class=\"modal-body\">
                    Are you sure you want to delete this User? This action cannot be undone.
                </div>
                <div class=\"modal-footer\">
                    <button type=\"button\" class=\"btn btn-secondary\" data-bs-dismiss=\"modal\">Cancel</button>
                    <a href=\"#\" id=\"confirmDelete\" class=\"btn btn-danger\">Delete</a>
                </div>
            </div>
        </div>
     </div>

    ";

    echo "
         <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteButtons = document.querySelectorAll('.delete-btn');
                const confirmDeleteBtn = document.getElementById('confirmDelete');
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

                let formToSubmit = null;

                deleteButtons.forEach(button => {
                    button.addEventListener('click', function () {
                        formToSubmit = this.closest('form');
                        deleteModal.show();
                    });
                });
                confirmDeleteBtn.addEventListener('click', function () {
                    if (formToSubmit) {
                        formToSubmit.submit(); 
                    }
                });
            });
        </script>


     </body>
    
    ";
    

}


?>