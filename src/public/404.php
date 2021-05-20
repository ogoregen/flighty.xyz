<?php

require_once __DIR__."/../database.php";
require_once __DIR__."/../flighty.php";

include_component("page_head.php", [
    "title" => "Not found.",
]);

?>

<div class="fly-content-wrapper">
    <?php include_component("nav.php") ?>
    <div class="fly-text-center">
        <h1>Error 404.</h1>
        <p class="fly-heading">This way does not lead anywhere. Go back <a href="/">home</a>?</p>
    </div>
    <?php include_component("footer.php") ?>
</div>

<?php include_component("page_end.php") ?>