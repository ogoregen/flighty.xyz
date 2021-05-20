<?php

require __DIR__."/../database.php";
require __DIR__."/../flighty.php";
require __DIR__."/../vendor/parsedown-1.7.4/Parsedown.php";

session_start();

if(!$_SESSION["is_authenticated"]) header("Location: /login");

$autofill = [];

if($_SERVER["REQUEST_METHOD"] === "POST"){
    
    $failed = false;
    if($_POST["submit"] == "articles"){

        if($result = createOrUpdateArticle($_POST)){

            $message["level"] = "success";
            $message["body"] = "Article saved successfully.";
        } 
        else $failed = true;
    }
    else if($_POST["submit"] == "pages"){

        if(createOrUpdatePage($_POST)){
        
            $message["level"] = "success";
            $message["body"] = "Page saved successfully.";
        }
        else $failed = true;
    }
    else if($_POST["submit"] == "delete"){

        $connection->query("DELETE FROM ".$_GET["page"]." WHERE id = ".$_POST["id"].";");
        $message["level"] = "success";
        $message["body"] = "Article deleted.";
    }

    if($failed){
            
        $message["level"] = "error";
        $message = "Someting went wrong while saving.";
        $autofill = $_POST;
    }
}

switch($_GET["page"]){

    case "articles":
        $entries = query("SELECT id, creation_date, title FROM articles ORDER BY creation_date DESC;");
        $title = "articles";
        $entry = "article";
        break;

    case "pages":
        $entries = query("SELECT id, title FROM pages ORDER BY menu_index ASC;");
        $title = "pages";
        $entry = "page";
        break;

    default:
        header("Location: /admin?page=articles");
        break;
}

if(isset($_GET["id"])){

    $selected = $_GET["id"];
    $result = $connection->query("SELECT * FROM $title WHERE id = '$selected';");
    if($result->num_rows > 0) $autofill = $result->fetch_assoc();
}
else{
    
    $new = true;
}

include_component("page_head.php", [
    "title" => $autofill["title"] ?? "New $entry",
]);


?>

<div class="fly-content-wrapper">

    <div>
        <nav class="fly-flex fly-flex-space-between fly-flex-center fly-width-1-1">
            <div>
                <ul class="fly-list-inline fly-margin">
                    <li><a class="fly-link-text" href=""><b>flighty admin</b></a></li>
                    <li><a href="?page=articles" class="<?php if($title == "articles") echo "fly-text-primary" ?>">articles</a></li>
                    <li><a href="?page=pages" class="<?php if($title == "pages") echo "fly-text-primary" ?>">pages</a></li>
                </ul>
            </div>
            <ul class="fly-list-inline fly-margin">
                <li style="margin-right: 8px;"><a href="/">back to the site</a></li>
                <li><a href="/logout">log out</a></li>
            </ul>
        </nav>
        <section>
            <div class="fly-grid fly-margin">

                <div class="fly-width-1-3" id="sidebar" style="display: flex; flex-direction: column; padding-right: 24px;">
                    <div>
                        <h2 class="fly-margin-0"><?= ucfirst($title) ?></h2>
                        <ul style="list-style: none; padding-left: 0;">
                            <?php foreach($entries as $article): ?>
                                <li style="margin-bottom: 16px;">
                                    <div class="<?php if($article["id"] == $selected) echo "fly-text-primary" ?>" style="display: flex; justify-content: space-between;">
                                        <a href="?page=<?= $title ?>&id=<?= $article["id"] ?>" style="font-weight: 400; color: inherit;"><?= $article["title"]; ?></a>
                                        <div style="min-width: max-content;">
                                            <?php if($title == "articles") echo date("m/d/y", strtotime($article["creation_date"])) ?>
                                        </div>
                                    </div>
                                </li>
                            <?php endforeach ?>
                            <li style="margin-bottom: 16px;">
                                <a href="?page=<?= $title ?>&new" class="<?php if(isset($new)) echo "fly-text-primary" ?>" style="font-weight: 900;">New <?= ucfirst($entry) ?></a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="fly-width-2-3" style="margin-right: auto; margin-left: auto;">
                    <form action="/admin?page=<?= $title ?>" method="POST" style="display: inline">
                        <label class="fly-form-label" for="title">Title</label>
                        <textarea name="title" class="fly-textarea fly-input-title" style="padding-left: 0; padding-top: 0; margin-top: 0" rows="1" placeholder="..." oninput="removeLineBreaks(this); adjustTextarea(this);" id="titleInput" style="width: 100%; margin-bottom: 16px;" required><?php if($autofill) echo $autofill["title"] ?></textarea>
                        <?php if($title == "articles"): ?>
                            <label class="fly-form-label" for="description">Description (optional)</label>
                            <textarea name="description" class="fly-textarea" rows="2" style="width: 100%; margin-bottom: 16px;" id="description"><?php if($autofill) echo $autofill["description"] ?></textarea>
                        <?php else: ?>
                            <label class="fly-form-label" for="menu_index">Menu Index</label>
                            <input name="menu_index" type="number" class="fly-input" style="width: 100%; margin-bottom: 16px;" id="menu_index" value="<?php if($autofill) echo $autofill["menu_index"] ?>" required>
                        <?php endif ?>
                        <label class="fly-form-label" for="body">Content</label>
                        <textarea name="content_raw" class="fly-textarea" rows="20" id="body" required><?php if($autofill) echo $autofill["content_raw"] ?></textarea>
                        <div class="fly-text-right fly-text-small" style="margin-bottom: -20px;">
                            <a href="https://guides.github.com/features/mastering-markdown/" target="_blank">Markdown</a> text formatting supported
                        </div>
                        <?php if($autofill): ?>
                            <input name="id" value="<?= $_GET['id'] ?>" hidden>
                        <?php endif ?>
                        <button class="fly-button" name="submit" type="submit" style="margin-top: 16px;" value="<?= $title ?>"><?= isset($new) ? "publish" : "update" ?></button>
                        <button class="fly-button-text" formtarget="_blank" formaction="/<?= $entry ?>" style="margin-left: 8px" type="submit">preview</button>
                    </form>
                    <?php if($autofill): ?>
                        <a href="/<?= $entry ?>?id=<?= $_GET['id'] ?>" target="_blank" style="margin-left: 8px">view <?= $entry ?></a>
                        <a href="#" onclick="toggleBold(this); toggleVisibility(document.getElementById('delete_button'));" class="fly-text-error" style="margin-left: 8px">delete</a>
                        <form action="/admin?page=<?= $title ?>" method="POST" style="display: inline">
                            <input name="id" value="<?= $_GET['id'] ?>" hidden>
                            <button class="fly-button fly-button-default" id="delete_button" style="margin-left: 8px" type="submit" name="submit" value="delete" hidden>confirm</button>
                        </form>
                    <?php endif ?>
                </div>

            </div>
        </section>
    </div>

    <footer class="fly-margin fly-border-top fly-flex fly-flex-space-between" style="padding-top: 16px; margin-bottom: 16px;">
        <a class="fly-hidden-mobile" href="#" onclick="toggleSidebar();" id="sidebarDisplayButton">hide sidebar</a>
        <span>flighty admin</span>
    </footer>

</div>

<?php if(isset($message)): ?>
    <div class="fly-border fly-alert-<?= $message["level"] ?>" style="position: fixed; top: 10px; left: 50%; transform: translateX(-50%);" id="alert">
        <p class="fly-text-<?= $message["level"] ?>"><?= $message["body"] ?></p>
        <a style="position: absolute; top: 0; right: 0;" class="gg-close" onclick="document.getElementById('alert').hidden = true;"></a>
    </div>
<?php endif ?>

<script src="static/js/admin.js"></script>

<?php include_component("page_end.php") ?>