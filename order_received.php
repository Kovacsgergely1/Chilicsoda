<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if(!isset($_SESSION["username"])){
        header("Location: login.php");
    }

    if(isset($_POST["logout"])){
        session_destroy();
        header(
        "Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    Kösz tesa, megkaptuk a rendelésed!
    <form action="checkout.php" method="post">
        <br>
        <input type="submit" name="logout" value="logout">
    </form>
</body>
</html>