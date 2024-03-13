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
<html lang="hu">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body{
    cursor: pointer;
    overflow-x: hidden;
    overflow-y:scroll;
}
.headbar{
    background-color: whitesmoke;
    position: sticky;
}

.logo{
    width: 70px;
    margin-left: 45%;
}

.login{
    width: 40px;
    padding-top: 15px;
    cursor: pointer;
}
    </style>
    <link rel="stylesheet" href="./sidenav.css">
    <script src="./sidenav.js"></script>
    <title>Chilicsoda Webshop</title>
</head>
<body>
<div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="index.php">Főoldal</a>
        <a href="about.php">Rólunk</a>
        <a href="forum.php">Fórum</a>
        <a id="sidenav-selected" href="webshop.php">Webshop</a>
        <?php
    if(isset($_SESSION["username"]) && $_SESSION["username"] == "admin"){
        echo "<a href='admin_page.php'>Admin</a>";
    }
    ?>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="" diabled><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <a href="checkout.php"><img class="float-end login" src="Login.png" alt="Login"></a>
    </div>


    <h2>A rendelésed elküldtük!</h2>
</body>
</html>