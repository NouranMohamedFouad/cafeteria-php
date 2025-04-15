<?php

function drawTable($header, $tableData, $delete='/controllers/delete_user_logic', $edit='/edit_user') {

    global $userHeader;
    $userHeader = $header;
    echo '    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>User Management - Cafeteria</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="../assets/usersStylesheet.css" rel="stylesheet">
    </head>
    <body>
        <div class="background-overlay"></div>

        <div class="container">
            <div class="header">
                <h1>User Management</h1>
                <a href="add_user">
                <button class="btn btn-add">+ ADD NEW USER</button>
                </a>
            </div>

            <div class="content-box">
                <div class="category-header">
                    All Users
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>';
    foreach ($header as $value) {
        echo "<th>$value</th>";
    }
    echo "<th>Actions</th></tr></thead><tbody>";

    foreach ($tableData as $row) {
        echo "<tr>";
        foreach ($row as $index => $field) {
            if ($index ==0) {
                echo "<td><img src='{$field}' class='user-img' alt='User profile'></td>";
            } elseif ($index == 1 || $index == 3 || $index == 4 || $index == 6 || $index == 8 || $index==7) {
                continue;
            } else {
                echo "<td>{$field}</td>";
            }
        }
        
        echo "<td>
                <div class='action-buttons'>
                    <form method='post' action='{$edit}'>
                        <input type='hidden' name='id' value='{$row[1]}'>
                        <input type='submit' class='btn btn-edit' value='Edit'>
                    </form>

                    <form method='post' action='{$delete}' class='delete-form' id='delete-form-{$row[1]}'>
                        <input type='hidden' name='id' value='{$row[1]}'>
                        <button type='button' class='btn btn-delete delete-btn' data-id='{$row[1]}'>Delete</button>
                    </form>
                </div>
              </td>";
        echo "</tr>";
    }

    echo '</tbody></table>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDelete" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const deleteButtons = document.querySelectorAll(".delete-btn");
            const confirmDeleteBtn = document.getElementById("confirmDelete");
            const deleteModal = new bootstrap.Modal(document.getElementById("deleteModal"));
            let formToSubmit = null;

            deleteButtons.forEach(button => {
                button.addEventListener("click", function() {
                    const userId = this.getAttribute("data-id");
                    formToSubmit = document.getElementById("delete-form-" + userId);
                    deleteModal.show();
                });
            });

            confirmDeleteBtn.addEventListener("click", function() {
                if (formToSubmit) {
                    formToSubmit.submit();
                    deleteModal.hide();
                }
            });
        });
    </script>
    </body>
    </html>';
}

?>
