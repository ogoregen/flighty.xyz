
<?php
session_start();
$connection = mysqli_connect("localhost", "libero", "nX6TVJfRkHKqNm", "libero");
$result = $connection->query("SELECT * FROM articles WHERE id = '".$_GET['id']."';");
$article = $result->fetch_assoc();
if(!$article || ($article['is_draft'] && !$_SESSION['is_authenticated'])) header("Location: /");

$context["title"] = $article["title"];
$context["meta_description"] = $article["description"];
require_once "components/page_head.php";
?>

<div style="display: flex; flex-direction: column; justify-content: space-between; height: 100vh;">

    <div>
        <?php require_once "components/nav.php" ?>

        <section>
            <h1 class="fly-heading" style="margin-bottom: 8px;"><?php echo $article["title"] ?></h1>
            <div class="fly-margin" style="margin-top: 0; margin-bottom: 0;"><?php echo date("M d, Y", strtotime($article["creation_date"])) ?></div>
            <div class="fly-grid">
                <div class="fly-column-2-3 article" style="margin: auto;">
                    <?php echo $article["body"] ?>
                </div>
            </div>
        </section>
    </div>
    <div>
    <div style="border-top: 1px solid black; display:flex; justify-content: space-between; padding-top: 16px;" class="fly-margin">
            <div class="fly-grid" style="display: flex; align-items: center;">
                <div><-</div>
                <div>
                    <div>
                        <a style="font-weight: 400;" href="">Previous Article</a>
                    </div>
                    <div>
                        <a href="">dapibus sollicitudin elit</a>
                    </div>
                </div>
            </div>
            <div class="fly-grid" style="display: flex; align-items: center; text-align: right;">
                <div>
                    <div>
                        <a style="font-weight: 400;" href="">Next Article</a>
                    </div>
                    <div>
                        <a href="">dapibus sollicitudin elit</a>
                    </div>
                </div>
                <div>-></div>
            </div>
        </div>
        <?php require_once "components/footer.php" ?>
    </div>
</div>
<?php require_once "components/page_end.php" ?>
