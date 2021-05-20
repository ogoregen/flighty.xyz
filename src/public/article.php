<?php

// displays an article created on /admin

require __DIR__."/../database.php";
require __DIR__."/../flighty.php";
require __DIR__."/../vendor/parsedown-1.7.4/Parsedown.php";

session_start();

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $preview = true;
    $Parsedown = new Parsedown();
    $article = [
        "title" => addslashes($_POST["title"]),
        "creation_date" => date("Y-m-d"),
        "content" => $Parsedown->text(addslashes($_POST['content_raw'])),
    ];
}
else{

    $id = $_GET["id"];
    $article = query("SELECT title, creation_date, description, content FROM articles WHERE id = $id;")[0] ?? false;
    if(!$article) throw_404();
    $previous_article = query("SELECT id, title FROM articles WHERE id < $id ORDER BY id DESC LIMIT 1;")[0] ?? [];
    $next_article = query("SELECT id, title FROM articles WHERE id > $id LIMIT 1;")[0] ?? [];
}

include_component("page_head.php", [
    "title" => $article["title"],
    "meta_description" => $article["description"] ?? "",
]);

?>

<div class="fly-content-wrapper">
    <div>
        <?php include_component("nav.php") ?>
            <article>
                <h1 class="fly-margin-small-bottom"><?= $article["title"] ?></h1>
                <div class="fly-margin-horizontal">
                    <?php
                    echo date("M d, Y", strtotime($article["creation_date"]));
                    if(isset($preview)) echo " | preview";
                    else if($_SESSION["is_authenticated"] ?? false) echo " | <a href='/admin?page=articles&id=$id'>edit</a>";
                    ?>
                </div>
                <div class="fly-width-2-3 fly-dropcap fly-margin-auto-horizontal fly-article" style="margin-top: 32px">
                    <?= $article["content"] ?>
                </div>
            </article>
        </div>
        <div>
            <?php if(!isset($preview)): ?>
            <div class="fly-margin fly-border-top fly-padding-top fly-flex fly-flex-space-between fly-flex-wrap">
                <div>
                    <?php if($previous_article): ?>
                        <a class="fly-article-link fly-flex fly-flex-center" href="/article?id=<?= $previous_article["id"] ?>">
                            <div>
                                <i class="gg-chevron-left"></i>
                            </div>
                            <div>
                                <div style="font-weight: 400;">
                                Previous Article
                                </div>
                                <div>
                                    <?= $previous_article["title"] ?>
                                </div>
                            </div>
                        </a>
                    <?php endif ?>
                </div>

                <div class="fly-width-expand-small">
                    <div class="fly-visible-small fly-margin-small"></div>
                    <?php if($next_article): ?>
                        <a class="fly-article-link fly-flex fly-flex-center fly-text-right" style="justify-content: flex-end;" href="/article?id=<?= $next_article["id"]?>">
                            <div>
                                <div style="font-weight: 400;" >
                                Next Article
                                </div>
                                <div>
                                    <?= $next_article["title"] ?>
                                </div>
                            </div>
                            <div>
                                <i class="gg-chevron-right"></i>
                            </div>
                        </a>
                    <?php endif ?>
                </div>
            </div>
        <?php
        endif;
        include_component("footer.php");
        ?>
    </div>
</div>

<?php include_component("page_end.php") ?>