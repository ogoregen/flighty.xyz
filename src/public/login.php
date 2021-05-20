<?php

require __DIR__."/../database.php";
require __DIR__."/../flighty.php";

session_set_cookie_params(2147483647); //maximum cookie lifespan
session_start();

if(isset($_SESSION["is_authenticated"])) header("Location: /admin");
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $result = query("SELECT username, password FROM users WHERE username = '{$_POST["username"]}';");
    if($result){

        $user = $result[0];
        if(password_verify($_POST["password"], $user["password"])){
    
            $_SESSION["is_authenticated"] = true;
            header("Location: admin");
        }
        else $failed = true;
    }
    else $failed = true;
    if($failed) $messages[] = "These credentials do not match the records. Please try again.";
}

include_component("page_head.php", [
    "title" => "Log In",
]);

?>

<div class="fly-min-height-viewport fly-flex fly-flex-center fly-flex-middle">
    <div>
        <?php
        if(isset($messages)){
            
            foreach($messages as $message) echo "<p class='fly-form-controls fly-text-error' style='width: 274px'>$message</p>";
        }
        ?>
        <form action="" method="POST">
            <div style="margin-bottom: 16px; display: flex;">
                <label class="fly-form-label-inline" for="username">username</label>
                <input class="fly-input" name="username" type="text" id="username" required>
            </div>
            <div style="margin-bottom: 16px; display: flex;">
                <label class="fly-form-label-inline" for="password">password</label>
                <input class="fly-input" name="password" type="password" style="height: max-content;" id="password" required>
            </div>
            <div style="width: 100%; margin-bottom: 16px;">
                <div class="fly-form-controls">                            
                    <button class="fly-button" type="submit">log in</button>
                    <a href="/" style="margin-left: 8px;">back</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include_component("page_end.php") ?>