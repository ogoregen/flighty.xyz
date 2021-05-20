<?php

// utility functions for templating and http errors

// includes file with a controlled scope
function include_component($file_name, $context = []){

    include "../template_components/".$file_name;
}

function throw_404(){
    
    http_response_code(404);
    include "404.php";
    die();
}
