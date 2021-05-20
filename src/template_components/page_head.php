<!DOCTYPE html>
<html>
    <head>
        <title><?= isset($context["title"]) ? $context["title"]." - " : "" ?>flighty</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?= $context["meta_description"] ?? "" ?>">
        <link rel="icon" href="static/images/favicon.png" type="image/png"> 
        <link rel="stylesheet" href="static/css/style.css">
        <link rel="preconnect" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@200;400;900&display=swap" rel="stylesheet">
        <link href="https://css.gg/css?=|chevron-left|chevron-right|chevron-down|close" rel="stylesheet">
    </head>
    <body>
        <button class="fly-button" onclick="toggleLights();" style="position: fixed; bottom: 10px; left: 10px;" id="lightButton">dark mode</button>
        <script src="static/js/main.js"></script>
        <main class="fly-container">