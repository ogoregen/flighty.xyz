<?php

// utility for database connection

global $connection;
$connection = mysqli_connect("localhost", "flighty", "[PASSWORD HERE]", "flighty");

function query($query){

    global $connection;
    $result = $connection->query($query);
    $rows = $result->fetch_all(MYSQLI_ASSOC);
    return $rows ?? [];
}

function createOrUpdateArticle($post){

    $Parsedown = new Parsedown();
    $title = addslashes($_POST['title']);
    $description = addslashes($_POST['description']);
    $content_escaped = addslashes($_POST['content_raw']);
    $content = $Parsedown->text($content_escaped);
    $content_raw = $content_escaped;
    if(isset($_POST["id"])){ //update

        $query = <<<query
            UPDATE articles
            SET
            title ='{$title}',
            description ='{$description}',
            content ='{$content}',
            content_raw ='{$content_raw}'
            WHERE id = '{$_POST['id']}';
        query;
    }
    else{ //create

        $query = <<<query
            INSERT INTO articles
            (title, description, content, content_raw)
            VALUES (
                '{$title}',
                '{$description}',
                '{$content}',
                '{$content_raw}'
            );
        query;
    }
    global $connection;
    return $connection->query($query);
}

function createOrUpdatePage($post){

    $Parsedown = new Parsedown();
    $title = addslashes($_POST['title']);
    $content_escaped = addslashes($_POST['content_raw']);
    $content = $Parsedown->text($content_escaped);
    $content_raw = $content_escaped;
    if(isset($_POST["id"])){ //update

        $query = <<<query
            UPDATE pages
            SET
            title = '{$title}',
            menu_index = '{$_POST['menu_index']}',
            content = '{$content}',
            content_raw = '{$content_raw}'
            WHERE id = '{$_POST['id']}';
        query;
    }
    else{ //create

        $query = <<<query
            INSERT INTO pages
            (title, menu_index, content, content_raw)
            VALUES (
                '{$title}',
                {$_POST['menu_index']},
                '{$content}',
                '{$content_raw}'
            );
        query;
    }
    global $connection;
    return $connection->query($query);
}
