<?php

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);


echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>';


function drawlines(){
    echo str_repeat("<br>",3);
}

function displayError($error_message){
    echo '<div class="alert alert-danger" role="alert">';
    echo "<h2 class='mb-0'>❌ { $error_message} </h2> </div>";
}

function displaySuccess($success_message){
    echo '<div class="alert alert-success" role="alert">';
    echo '<h2 class="mb-0">✅ ' . $success_message . '</h2>';
    echo '</div>';
}
