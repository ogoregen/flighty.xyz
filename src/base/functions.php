
<?php

function create_message($level, $body){ //creates cookie-based message that can be retrieved after redirecting 

    if(!isset($_COOKIE["messages"])) $messages = array();
    else $messages = json_decode($_COOKIE["messages"]);
    $messages[count($messages)] = array(
        "level" => $level,
        "body" => $body
    );
    setcookie("messages", json_encode($messages));
}

function get_messages(){

    if(isset($_COOKIE["messages"])){

        $messages = json_decode($_COOKIE["messages"], true);
        setcookie("messages", "", -1); //discarding read messages
    }
    else $messages = null;
    return $messages;
}

function get_articles($connection, $admin = false){

    $articles = array();
    if($admin) $sql = "SELECT id, creation_date, is_draft, title FROM articles ORDER BY creation_date DESC;";
    else $sql ="SELECT id, creation_date, title, description FROM articles WHERE is_draft = false ORDER BY creation_date DESC;";
    $result = $connection->query($sql);
    while($article = $result->fetch_assoc()) $articles[count($articles)] = $article;
    return $articles;
}

function get_pages($connection){

    $pages = array();
    $sql = "SELECT id, title FROM pages ORDER BY menu_index;";
    $result = $connection->query($sql);
    while($page = $result->fetch_assoc()) $pages[count($pages)] = $page;
    return $pages;
}

?>
