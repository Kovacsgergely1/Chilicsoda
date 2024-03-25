<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // if(!isset($_SESSION["username"])){
    //     header("Location: login.php");
    // }

    if(isset($_POST["logout"])){
        session_destroy();
        header("Location: login.php");
    }

    include("database_connection.php");

    if(isset($_POST["new_theme"])){
        $theme_name = filter_input(INPUT_POST, "new_theme_name", FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($theme_name)){

            //check hogy létezik e már azzal a névvel
            $sql_check_existence = "SELECT * FROM forum_themes WHERE theme_name = '" . $theme_name . "';";
            $result = mysqli_query($conn, $sql_check_existence);
            if(mysqli_num_rows($result) == 0){

                $sql_get_userid = "SELECT * FROM users WHERE username = '" . $_SESSION["username"] . "';";
                $user_id = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_userid))["user_id"];
    
    
                $sql_create_new_theme = "INSERT INTO forum_themes (theme_name, user_id) VALUES ('$theme_name', '$user_id');";
                try{
                    mysqli_query($conn, $sql_create_new_theme);
                    echo "<p class='alert alert-success'>Téma sikeresen létrehozva.</p>";
                }
                catch(mysqli_sql_exception){
                    echo "<p class='alert alert-success'>Téma sikeresen létrehozva.</p>";
                }
            }
            else{
                echo "<p class='alert alert-warning'>Már létezik ezzel a névvel téma!</p>";
            }
        }
        
        else{
            echo "<p class='alert alert-warning'>Nem lehet üres a témanév!</p>";
        }
        
    }

    //témára való rákattintás 
    $sql_get_theme_ids = "SELECT theme_id FROM forum_themes;";
    $result = mysqli_query($conn, $sql_get_theme_ids);
    while($row = mysqli_fetch_assoc($result)){
        if(isset($_POST[$row["theme_id"]])){
            $_SESSION["choosen_theme_id"] = $row["theme_id"];
            header("Location: forum_theme.php");
        }
    }

    //téma törlése
    if(isset($_SESSION["delete_button_ids"])){
        foreach($_SESSION["delete_button_ids"] as $id){
            if(isset($_POST["d-" . $id])){
                $sql_delete_messages = "DELETE FROM forum_message WHERE theme_id = $id";
                $sql_delete_theme = "DELETE FROM forum_themes WHERE theme_id = $id";
                mysqli_query($conn, $sql_delete_messages); 
                mysqli_query($conn, $sql_delete_theme); 
            }
        }
    }


?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="./forum.css">
    <link rel="stylesheet" href="./sidenav.css">
    <script src="./sidenav.js"></script>
    <title>Chilifórum</title>
</head>
<body>
<div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="index.php">Főoldal</a>
        <a href="about.php">Rólunk</a>
        <a id="sidenav-selected" disabled>Fórum</a>
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
                echo '<form action="logout.php" method="post"><input type="submit" name="logout" value="logout" class="logout"></form>';
        }
        ?>
    </div>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="index.php"><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <a href="login.php"><img class="float-end login" src="./Login.png" alt="Login"></a>
    </div>




<form action="forum.php" method="post">
    <br>
    <?php
    if(isset($_SESSION["username"])){

        echo 'Új téma neve:';
        echo '<input type="text" name="new_theme_name">';
        echo '<input type="submit" name="new_theme" value="új téma létrehozása"><br>';
    }
    ?>


    <div class="column">
        <table>
            <tr>
                <th>Téma neve</th>
                <th>Létrehozó</th>
                <th></th>
            </tr>
            <?php
            $_SESSION["delete_button_ids"] = [];
            $sql_select_all_theme = "SELECT * FROM forum_themes;";
            $result = mysqli_query($conn, $sql_select_all_theme);
            while ($theme = mysqli_fetch_assoc($result)){
                echo "<tr>";
        
                echo "<td id='sender'> <input type='submit' name='" . $theme["theme_id"] . "' value='" . $theme["theme_name"] . "'></td>";
                $sql_get_username = "SELECT * FROM users WHERE user_id = '" . $theme["user_id"] . "';";
                $username = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_username))["username"];
                echo "<td>" . $username . "</td>";
                if(isset($_SESSION["username"]) && $_SESSION["username"] == $username){
                    echo "<td>" . "<input type='submit' name='d-" . $theme["theme_id"] . "' value='törlés' id='torles'>" . "</td>";
                    $_SESSION["delete_button_ids"][] = $theme["theme_id"];
                }
                else{
                    echo "<td></td>";
                }
                echo "</tr>";
            }
            ?>
        </table>
    </div>
    <div style="float: left; width:160px; margin-left: 5px;" class="advertisement">
        <script>if(!Ctv){var e=document.createElement("script");e.src="//ads.projectagoraservices.com/?id=8072",e.async=!0,document.write(e.outerHTML)}else{document.write('<div data-gz-block="e9a76335-4201-46a5-ac0b-d71602f2d3b9"></div>')}</script>
        <script src="//ads.projectagoraservices.com/?id=8072" async=""></script>
        <a href="https://rosszlanyok.hu/videkilanyok?utm_source=mozinet.me&amp;utm_medium=160x600_videk&amp;utm_campaign=mozinet.me" title="Rosszlanyok.hu - Vidéki szexpartner lányok" target="_blank"><img src="https://filmvilag.me/style/rl2.gif" width="160" height="600" alt="Rosszlanyok.hu - Vidéki szexpartner kereső. Vidéki lányok. Csak valódi képek!" title="Rosszlanyok.hu - Vidéki szexpartner lányok" border="0"></a>
    </div>

</form>
</body>
</html>