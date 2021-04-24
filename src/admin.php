
<?php
session_start();
if(!$_SESSION["is_authenticated"]) header("Location: /login");
$message = null;
$autofill = false;
$connection = mysqli_connect("localhost", "libero", "nX6TVJfRkHKqNm", "libero");
if($_SERVER["REQUEST_METHOD"] === "POST"){
    
    if($_POST["submit"] == "delete"){


    }
    else{

        require_once "base/external/parsedown-1.7.4/Parsedown.php";
        $Parsedown = new Parsedown();
        $is_draft = false;
        if($_POST['submit'] == "draft"){

            $is_draft = true;
        }
        if(isset($_POST["id"])){

            $result = $connection->query("UPDATE articles SET title='".$_POST['title']."', description='".$_POST['description']."', body='".$Parsedown->text(htmlspecialchars($_POST['body']))."', raw_body='".$_POST['body']."' WHERE id = '".$_POST['id']."';");
        }
        else{

            $result = $connection->query("INSERT INTO articles (is_draft, title, description, body, raw_body) VALUES ('".$is_draft."', '".$_POST['title']."', '".$_POST['description']."', '".$Parsedown->text(htmlspecialchars($_POST['body']))."', '".$_POST['body']."');");
        }
        if($result) $message = "success!";
        else{ //failed
            
            $message = "Someting went wrong while saving.";
            $autofill = true;
            $autofill_article = array(
                //autofill
                "title" => $_POST['title'],
                "description" => $_POST['description'],
                "raw_body" => $_POST["body"]
            );
        } 
    }
}
else if(isset($_GET['id'])){

    $selected_article_id = $_GET['id'];
    $result = $connection->query("SELECT * FROM articles WHERE id = '".$selected_article_id."';");
    if($result->num_rows > 0){

        $autofill = true;
        $autofill_article = $result->fetch_assoc();
    }
}

require_once "functions.php";
$articles = get_articles($connection, true);

$context["title"] = "admin";
require_once "components/page_head.php";
?>

<div>
    <nav style="display: flex; width: 100%; justify-content: space-between; align-items: center;">
        <div>
        <a href="" class="fly-margin"><b>libero</b> admin</a>
        <ul class="lbr-list fly-margin" style="list-style: none; padding: 0; display: inline-block;">
            <li class="fly-active" style="display: inline-block; margin-right: 8px;"><a href="">articles</a></li>
            <li class="fly-active" style="display: inline-block; margin-right: 8px;"><a href="">pages</a></li>
            <li style="display: inline-block;"><a href="">newsletter</a></li>
        </ul>
        </div>
        <ul class="lbr-list fly-margin" style="list-style: none; padding: 0;">
            <li style="display: inline-block; margin-right: 8px;"><a href="/">back to the site</a></li>
            <li style="display: inline-block;"><a href="/logout">log out</a></li>
        </ul>
    </nav>
    <section>
        <div class="fly-grid fly-margin">
            <div class="fly-column-1-3" id="sidebar" style="display: flex; flex-direction: column; padding-right: 24px;">
                <div>
                    <h2 style="margin: 0;">articles</h2>
                    <ul class="lbr-list" style="list-style: none; padding-left: 0;">
                        <?php foreach($articles as $article): ?>
                            <li style="margin-bottom: 16px;">
                                <div class="<?php if($article["id"] == $selected_article_id) echo "fly-text-danger" ?>" style="display: flex; justify-content: space-between;">
                                    <a href="?id=<?php echo $article["id"] ?>" style="font-weight: 400; color: inherit;"><?php echo $article["title"]; if($article["is_draft"]) echo " (draft)" ?></a>
                                    <div style="min-width: max-content;"><?php echo date("m/d/y", strtotime($article["creation_date"])) ?></div>
                                </div>
                            </li>
                        <?php endforeach ?>
                        <li style="margin-bottom: 16px;">
                            <div class="" style="display: flex; justify-content: space-between;">
                                <a class="fly-button" href="">new article</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="fly-column-2-3" style="margin: auto;">
                <?php
                if($message) echo $message;
                ?>
                <form action="" method="POST">
                    <textarea name="title" class="fly-textarea fly-input-title" rows="1" placeholder="title" oninput="adjustTextarea(this);" id="titleInput" style="width: 100%; margin-bottom: 16px;" required><?php if($autofill) echo $autofill_article["title"] ?></textarea>
                    <textarea name="description" class="fly-textarea" placeholder="description (optional)" rows="2" style="width: 100%; margin-bottom: 16px;"><?php if($autofill) echo $autofill_article["title"] ?></textarea>
                    <textarea name="body" class="fly-textarea" placeholder="content" rows="20" required><?php if($autofill) echo $autofill_article["raw_body"] ?></textarea>
                    <button name="submit" type="submit" style="margin-top: 16px;">publish</button>
                    <a href="" name="submit" type="submit" value="draft" class="fly-button-link" style="margin-left: 8px">save as draft</a>
                    <a href="" style="margin-left: 8px">preview</a>
                    <a href="#" onclick="toggleVisibility(document.getElementById('delete_button'));" class="fly-text-danger" style="margin-left: 8px">delete</a>
                </form>
                <?php if($autofill): ?>
                    <form action="" method="POST">
                        <input name="id" value="<?php echo $autofill_article["id"] ?>" hidden>
                        <button class="fly-text-danger" name="delete" type="submit" style="margin-top: 16px; display: inline-block;" id="delete_button" hidden>confirm</button>
                    </form>
                <?php endif ?>
            </div>
        </div>
    </section>
</div>
<div style="border-top: 1px solid black; padding-top: 16px; margin-bottom: 16px; display: flex; justify-content: space-between;" class="fly-margin">
    <a class="fly-hidden-mobile" href="#" onclick="toggleSidebar();" id="sidebarDisplayButton">hide sidebar</a>
</div>

<script>
var sidebar = document.getElementById("sidebar");
var sidebarDisplayButton = document.getElementById("sidebarDisplayButton");

function toggleSidebar(){

    var hide = (sidebar.style.display != "none");
    sidebar.style.display = hide ? "none" : "flex";
    sidebarDisplayButton.innerHTML = hide ? "show sidebar" : "hide sidebar";
}

function adjustTextarea(textarea){

    textarea.value = textarea.value.replace(/\n/g, '');
    textarea.style.height = "";
    textarea.style.height = textarea.scrollHeight + "px";
}

adjustTextarea(document.getElementById("titleInput"));

function toggleVisibility(element){

    element.hidden = !element.hidden;
}
</script>

<?php require_once "components/page_end.php"; ?>
