<?php

session_start();

require __DIR__."/../database.php";
require __DIR__."/../flighty.php";

include_component("page_head.php", [
    "meta_description" => "Hey there. This is flighty. Here you can find articles on computer graphics and such.",
]);

$articles = query("SELECT id, creation_date, title, description FROM articles ORDER BY creation_date DESC;"); 

?>

<div class="fly-content-wrapper">
    <div>
        <?php include_component("nav.php") ?>
        <div class="fly-margin fly-grid">
            <img class="fly-hidden-small fly-width-1-3" src="static/images/portrait.jpg">
            <img class="fly-visible-small fly-margin-small-bottom" style="width: 200px; margin-left: auto; margin-right: auto;" src="static/images/portrait.jpg">
            <div class="fly-border fly-width-2-3" style="display: flex; align-items: center; border-color: inherit;">
                <div>
                    <p class="fly-heading">Hey there.</p>
                    <p class="fly-text-lead">This is <span class="fly-text-primary">flighty</span>. I've rarely written and even more rarely written well. This page is my attempt to change that by sharing what I learn and create.</p>
                </div>
            </div>
        </div>
        <div class="fly-width-2-3" style="margin: auto;">
            <h2>Here are the latest of my writings.</h2>
            <ul class="fly-margin" style="list-style: none; padding: 0;">
                <?php foreach($articles as $article): ?>
                    <li style="margin-bottom: 16px;">
                        <div style="display: flex; justify-content: space-between;">
                            <a class="fly-article-link" href="/article?id=<?= $article["id"] ?>" style="font-size: 1.1em; font-weight: 400;"><?= $article["title"] ?></a>
                            <div><?= date("M. d, Y", strtotime($article["creation_date"])) ?></div>
                        </div>
                        <div style="margin-top: 10px;"><?= $article["description"] ?></div>
                    </li>
                <?php endforeach ?>
            </ul>
        </div>
    </div>
    <?php include_component("footer.php") ?>
</div>

<?php include_component("page_end.php") ?>