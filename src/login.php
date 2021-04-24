
<?php
session_set_cookie_params(2147483647); //maximum cookie lifespan
session_start();
$messages = array();
if(isset($_SESSION["is_authenticated"])) header("Location: /libero/admin");
if($_SERVER["REQUEST_METHOD"] === "POST"){

    $connection = mysqli_connect("localhost", "libero", "nX6TVJfRkHKqNm", "libero");
    $result = $connection->query("SELECT username, password FROM users WHERE username = '".$_POST['username']."';");
    if($result->num_rows > 0){

        $user = $result->fetch_assoc();
        if(password_verify($_POST["password"], $user["password"])){
    
            $_SESSION["is_authenticated"] = true;
            header("Location: admin");
        }
        else $failed = true;
    }
    else $failed = true;
    if($failed) $messages[count($messages)] = "No match.";
}
$context["title"] = "login";
$context["meta_description"] = "";
require_once "components/page_head.php";
?>

<div style="min-height: 100vh; display: flex; justify-content: center; align-items: center;">
    <div>
        <?php
        if($messages){
            
            foreach($messages as $message) echo "<p>$message</p>";
        }
        ?>
        <form action="" method="POST">
            <div style="margin-bottom: 16px; display: flex;">
                <label for="username">username</label>
                <input name="username" type="text" id="username" required>
            </div>
            <div style="margin-bottom: 16px; display: flex;">
                <label for="password">password</label>
                <input name="password" type="password" style="height: max-content;" id="password" required>
            </div>
            <div style="width: 100%; margin-bottom: 16px;">
                <div class="fly-form-controls">                            
                    <button type="submit">log in</button>
                    <a href="/libero" style="margin-left: 8px;">back</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once "components/page_end.php" ?>
