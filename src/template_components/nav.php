<?php

$pages = query("SELECT id, title FROM pages ORDER BY menu_index;");
$path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

?>

<div class="fly-width-1-1 fly-flex fly-flex-space-between fly-flex-center">
    <div>
        <a href="/" class="fly-heading fly-link-text">flighty</a>
        <div class="fly-margin-horizontal">computer science and such</div>
    </div>
    <nav class="fly-hidden-small">
        <ul class="fly-margin fly-nav">
            <li class="<?php if($path == "/") echo "fly-nav-selected" ?>">
                <a href="/">Home</a>
            </li>
            <?php foreach($pages as $page): ?>
                <li class="<?php if($path == "/page" && isset($_GET["id"]) && $page["id"] == $_GET["id"]) echo "fly-nav-selected" ?>"><a href="page?id=<?php echo $page["id"] ?>"><?php echo $page["title"] ?></a></li>
            <?php endforeach ?>
            <?php if(isset($_SESSION["is_authenticated"])): ?>
                <li>
                    <a href="/admin">Admin</a>
                </li>
            <?php endif ?>
        </ul>
    </nav>
    <div class="fly-margin fly-visible-small">
        <a onclick="toggleVisibility(document.getElementById('mobile_menu'));" class="gg-chevron-down"></a>
    </div>
</div>

<nav class="fly-margin" id="mobile_menu" hidden>
    <ul class="fly-list">
        <li class="<?php if($path == "/") echo "fly-text-primary" ?>"><a href="/">Home</a></li>
        <?php foreach($pages as $page): ?>
            <li class="<?php if($path == "/page" && isset($_GET["id"]) && $page["id"] == $_GET["id"]) echo "fly-text-primary" ?>">
                <a href="page?id=<?php echo $page["id"] ?>"><?php echo $page["title"] ?></a>
            </li>
        <?php endforeach ?>
        <?php if(isset($_SESSION["is_authenticated"])): ?>
            <li>
                <a href="/admin">Admin</a>
            </li>
        <?php endif ?>
    </ul>
</nav>