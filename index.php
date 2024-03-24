<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

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
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./index.css">
    <link rel="stylesheet" href="./sidenav.css">
    <script src="./sidenav.js"></script>
    <script src="./bgchange.js"></script>
    <title>Chilicsoda</title>
</head>
<body>

<video autoplay muted loop id="bgVideo">
        <source src="./Slow Motion Fire Blaze From the Bottom Stock Video Footage.mp4" type="video/mp4">
        Your browser does not support HTML5 video.
      </video>

    <div id="Sidenav" class="sidenav">
        
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a id="sidenav-selected" disabled>Főoldal</a>
        <a href="about.php">Rólunk</a>
        <a href="forum.php">Fórum</a>
        <a href="webshop.php">Webshop</a>
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
                echo '<input type="submit" name="logout" value="logout" class="logout">';
        }
        ?>
    </div>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="" diabled><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <a href="./login.php"><img class="float-end login" src="./Login.png" alt="Login"></a>
    </div>
    
    <div class="content">
        <section id="section-1">
        <div class="welcome" id="welcome">
            <h1>Üdvözlünk a ChiliCsoda weboldalán! Ahol minden falat egy csípős csoda!</h1>
        </div>
        </section>
    </div>

    <section  id="section-2">
        <h2 class="sect2-title">Miért válaszd a Chilicsodát?</h2>
    <div class="row">
        <div class="column3">
            <h2>Élmény</h2>
            <p>Termékeink nem csupán ételek, hanem CSODÁK, amivel garantáljuk a felejthetetlen ízélményeket.</p>
        </div>
        <div class="column3">
            <h2>Minőség</h2>
            <p>Minőségi alapanyagok, egyedi receptúrák, szívvel-lélekkel és egy kis pokoli élménnyel készített termékek jellemzik választékunkat.</p>
        </div>
        <div class="column3">
            <h2>Megbízhatóság</h2>
            <p>A legfinomabb chili termékeket kínáljuk, amelyeket gondosan válogatunk a világ minden tájáról.</p>
        </div>
        
    </div>

    <div class="row">
        <div class="column3">
            <h2>Széles választék</h2>
            <p>Legyen szó pikáns szószokról, csípős kiegészítőkről vagy akár nassolni valókról, mi garantáljuk, hogy termékeinkkel CSODÁSSÁ tesszük az összes étkezésed.</p>
        </div>
        <div class="column3">
            <h2>Csípősségre szabva</h2>
            <p>Kínálunk enyhe chiliket, közepesen erős ízeket és egészen szuper erős, tűzforró termékeket is.</p>
        </div>
        <div class="column3">
            <h2>WOW!</h2>
            <p>Ha azt keresed, ami kirobbant az unalmas hétköznapi ízekből és kihívást jelent a számodra, akkor nálunk a helyed!</p>
        </div>
    </section>
    <footer class="footer">
        <div class="col-sm-6 float-start">
            <a href="#"><img src="./fb-ico.png" alt="Facebook" class="ico"></a>
            <a href="#"><img src="./inst-ico.png" alt="Instagram" class="ico"></a>
            <a href="#"><img src="./yt-ico.png" alt="Youtube" class="ico"></a>
        </div>
        <div class="col-sm-6 float-start">
            <img src="./info-ico.png" alt="Információk" class="ico" id="info">
            <p id="info-text">1234 Kiskutya, Kismacska út 1.</p>
        </div>
    </footer>

<!-- <form action="index.php" method="post">
    <br>
    <input type="submit" name="webshop" value="webshop"><br>
    <input type="submit" name="logout" value="logout">
    <a href="forum.php">fórum</a>
</form> -->
</body>
</html>