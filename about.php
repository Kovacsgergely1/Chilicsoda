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
    <link rel="stylesheet" href="./about.css">
    <link rel="stylesheet" href="./sidenav.css">
    <script src="./sidenav.js"></script>
    <title>Chilicsoda</title>
</head>
<body>
    <div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="index.php">Főoldal</a>
        <a id="sidenav-selected" disabled>Rólunk</a>
        <a href="forum.php">Fórum</a>
        <a href="webshop.php">Webshop</a>
        <?php
    if(isset($_SESSION["username"]) && $_SESSION["username"] == "admin"){
        echo "<a href='admin_page.php'>Admin</a>";
    }
    ?>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="index.php"><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <a href="login.php"><img class="float-end login" src="./Login.png" alt="Login"></a>
    </div>

    <div class="about-section">
        <h1>A Csodáról</h1>
        <p>Ha kíváncsi a történetünkre nézze meg a videót!</p>

        <iframe width="600" height="338" src="https://www.youtube.com/embed/rVkScnER848" allowfullscreen></iframe>      </div>
      
      <h2 style="text-align:center">Csapatunk</h2>
      <div class="row">
        <div class="column">
          <div class="card">
            <img src="https://i.imgur.com/n86HO2F.png" alt="Kocsis Viktor - Kép">
            <div class="container">
              <h2>Kocsis Viktor</h2>
              <p class="title">CEO & Founder</p>
              <p>kocsis.viktor@chilicsoda.hu</p>
              <p><a href="mailto:kocsis.viktor@chilicsoda.hu"><button class="button">Kapcsolat</button></a></p>
            </div>
          </div>
        </div>
      
        <div class="column">
          <div class="card">
            <img src="https://i.imgur.com/z5GKsA7.png" alt="Csopaki Emma - Kép">
            <div class="container">
              <h2>Csopaki Emma</h2>
              <p class="title">CEO & Founder</p>
              <p>csopaki.emma@chilicsoda.hu</p>
              <p><a href="mailto:csopaki.emma@chilicsoda.hu"><button class="button">Kapcsolat</button></a></p>
            </div>
          </div>
        </div>
        
<!--        <div class="column">
          <div class="card">
            <img src="./no-profile-picture-icon.png" alt="">
            <div class="container">
              <h2>Cicz Imre</h2>
              <p class="title">Designer</p>
              <p>leiras</p>
              <p>cicz.imre@chilicsoda.hu</p>
              <p><button class="button">Kapcsolat</button></p>
            </div>
          </div>
        </div>

        <div class="column">
            <div class="card">
              <img src="./no-profile-picture-icon.png" alt="">
              <div class="container">
                <h2>Péni Szecső</h2>
                <p class="title">Munkakor</p>
                <p>leiras</p>
                <p>peni.szecso@chilicsoda.hu</p>
                <p><button class="button">Kapcsolat</button></p>
              </div>
            </div>
          </div>

          <div class="column">
            <div class="card">
              <img src="./no-profile-picture-icon.png" alt="">
              <div class="container">
                <h2>Kiver Emma</h2>
                <p class="title">Munkakor</p>
                <p>leiras</p>
                <p>kiver.emma@chilicsoda.hu</p>
                <p><button class="button">Kapcsolat</button></p>
              </div>
            </div>
          </div>

          <div class="column">
            <div class="card">
              <img src="./no-profile-picture-icon.png" alt="" >
              <div class="container">
                <h2>Cigány Ferkó</h2>
                <p class="title">Munkakor</p>
                <p>leiras</p>
                <p>cigany.ferko@chilicsoda.hu</p>
                <p><button class="button">Kapcsolat</button></p>
              </div>
            </div>
          </div> -->

      </div>

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
</body>
</html>