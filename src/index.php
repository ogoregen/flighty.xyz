
<?php
session_start();

require_once "functions.php";

$connection = mysqli_connect("localhost", "libero", "nX6TVJfRkHKqNm", "libero");
$articles = get_articles($connection);

$context["title"] = "libero";
$context["meta_description"] = "";
require_once "components/page_head.php";

$pages = get_pages($connection);

require_once "components/nav.php";
?>

<div class="fly-margin fly-grid">
    <img class="fly-column-1-3" src="static/images/portrait.jpg"></img>
    <div class="fly-border fly-column-2-3" style="display: flex; align-items: center;">
        <div>
            <h1>Nisl nibh egestas lectus!</h1>
            <p>Nunc facilisis cursus ligula in dapibus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.</p>
            <div class="fly-margin">
                <label for="test">test input</label>
                <input id="test" type="email"></input>
                <button>magna</button>
                <button>posuere</button>
            </div>
        </div>
    </div>
</div>
<div class="fly-column-2-3" style="margin: auto;">
<?php
    require_once "functions.php";
    $messages = get_messages();
    if($messages){
        foreach($messages as $message) echo $message["body"];
    }
    ?>
    <h1>Etiam sed est in magna imperdiet pulvinar.</h1>
    <ul class="lbr-list fly-margin" style="list-style: none; padding: 0;">
        <?php foreach($articles as $article): ?>
            <li style="margin-bottom: 16px;">
                <div style="display: flex; justify-content: space-between;">
                    <a href="/article?id=<?php echo $article["id"] ?>" style="font-size: 1.1em; font-weight: 400;"><?php echo $article["title"] ?></a>
                    <div><?php echo date("M. d, Y", strtotime($article["creation_date"])) ?></div>
                </div>
                <div style="margin-top: 10px;"><?php echo $article["description"] ?></div>
            </li>
        <?php endforeach ?>
    </ul>
</div>  

<?php
require_once "components/footer.php";
require_once "components/page_end.php";
?>
