<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if(!isset($_SESSION["username"])){
        header("Location: login.php");
    }

    if(isset($_POST["logout"])){
        session_destroy();
        header("Location: login.php");
    }
 
    if(isset($_POST["webshop"])){
        header("Location: webshop.php");
    }

    if(isset($_POST["admin_page"])){
        header("Location: admin_page.php");
    }
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="index.php" method="post">
    <br>
    <input type="submit" name="webshop" value="webshop"><br>
    <input type="submit" name="logout" value="logout">
    <?php
    if($_SESSION["username"] == "admin"){
        echo "<input type='submit' name='admin_page' value='admin page'>";
    }
    ?>
    <a href="forum.php">fórum</a>
</form>
</body>
</html>