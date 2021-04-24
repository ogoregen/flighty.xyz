
<?php
session_start();
unset($_SESSION["is_authenticated"]);
session_destroy();
header('Location: /libero');
?>
