<?php

// command line script for superuser creation.
// usage:
// manage.php createuser

if($argc == 1) echo "Error: Argument expected.".PHP_EOL;
else if ($argc == 2){
    
    switch($argv[1]){
        case "createuser": 
            require "database.php";
            $stdin = fopen("php://stdin", "r");
            while(true){

                echo "Enter username: ";
                $username = trim(fgets($stdin));
                if(!ctype_alnum($username)) echo "Username must be alphanumeric.".PHP_EOL;
                else{
                    
                    $result = $connection->query("SELECT username FROM users WHERE username = '$username';");
                    if($result->num_rows == 0) break;
                    else echo "User ".$username." already exists.".PHP_EOL;
                }
            }
            echo "Enter password: ";
            $password = trim(fgets($stdin));
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $result = $connection->query("INSERT INTO users (username, password) VALUES ('$username', '$hashed_password');");
            if($result) echo "User ".$username." created.";
            else echo "Error: Something went wrong while creating database record. Please try again.";
            break;
        default:
            echo "Unknown argument ".$argv[1].PHP_EOL;
            break;
    }
}
