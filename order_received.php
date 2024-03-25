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
.kozep{
    margin: auto;
	text-align: center;
}

a{
	text-decoration:none;
	color: black;
}
.webshop{
    border: none;
    border-radius: 5px;
    height: auto;
    width: auto;
    font-size: 20pt;
    background: none;
}
.webshop:hover{
    background-color: rgba(245, 245, 245, 0.295);
    transition: 0.3s ease;
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
    <?php
            if(isset($_SESSION['username'])){
				echo "<h5 class='username'>Üdvözlünk {$_SESSION['username']}!</h5>";
			}
        ?>
       <?php
            if(isset($_SESSION["username"])){
                echo '<form action="logout.php" method="post"><input type="submit" name="logout" value="logout" class="logout"></form>';
        }
        ?>
    </div>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="index.php"><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <a href="checkout.php"><img class="float-end login" src="Login.png" alt="Login"></a>
    </div>


    <div class="kozep">
        <h2>A rendelésed sikeresen elküldtük!</h2>
        <p>A szállítás sikerességéről a <a href="https://github.com/Project-PackX/" target="_blank">PackX</a> gondoskodik.</p>

        <button class="webshop"><a href="webshop.php">Vásárolj tovább!</a></button>
    </div>
</body>
</html>