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

    //üzenet küldése
    if(isset($_POST["send_message"])){
	    $filetred_message = filter_input(INPUT_POST, "message", FILTER_SANITIZE_SPECIAL_CHARS);
	
        $sql_get_userid = "SELECT * FROM users WHERE username = '" . $_SESSION["username"] . "';";
        $sender_user_id = intval(mysqli_fetch_assoc(mysqli_query($conn, $sql_get_userid))["user_id"]);
        // echo $filetred_message . " " . $sender_user_id . " " . $_SESSION["choosen_theme_id"];

        //ellenőrzés, hogy ugyanaz az ember ugyanabba a témába írta e már ugyan azt (ha ujratölti az oldalát akkor megtörténne)
        $sql_check_message = "SELECT message, user_id, theme_id FROM forum_message WHERE message = '" . $filetred_message . "' AND user_id = $sender_user_id AND theme_id = " . $_SESSION["choosen_theme_id"] . ";";
        
        if(mysqli_num_rows(mysqli_query($conn, $sql_check_message)) > 0){
            echo "<p class='alert alert-warning'>Már létezik ez az üzenet!</p>";
            // !!!
        }
        
        elseif($filetred_message == ""){
            echo "<p class='alert alert-danger'>Üres az üzenet!</p>";
            // !!!
        }
        
        else{
            // üzenet feltöltése
            $sql_upload_message = "INSERT INTO forum_message (message, user_id, theme_id) VALUES ('" . $filetred_message . "'," .  $sender_user_id . "," . $_SESSION["choosen_theme_id"] . ");";
            try{
                mysqli_query($conn, $sql_upload_message);
                // !!! üzenet sikeresen elküldve
            }
            catch(mysqli_sql_exception){
                echo "<p class='alert alert-danger'>Üzenet elküldése sikertelen!</p>";
            }
        }
    }

    //üzenet törlése
    if(isset($_SESSION["delete_button_ids"])){
        foreach($_SESSION["delete_button_ids"] as $id){
            if(isset($_POST[$id])){
                $sql_delete_message = "DELETE FROM forum_message WHERE message_id = $id";
                mysqli_query($conn, $sql_delete_message); 
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
        <a href="forum.php"><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <a href="login.php"><img class="float-end login" src="./Login.png" alt="Login"></a>
    </div>



    <form action="forum_theme.php" method="post">
        <br>
        <button id="return"><a href="forum.php">Vissza</a></button>
        <br>
        <?php
        if(isset($_SESSION["username"])){
            echo 'üzenet:';
            echo '<input type="text" name="message">';
            echo '<input type="submit" name="send_message" value="üzenet küldése">';
            echo '<br>';
            echo '<br>';
        }
        ?>
    
    <div class="column">
        <table>
            <tr>
                <th>Üzenő</th>
                <th>Üzenet</th>
                <th>Üzenet dátuma</th>
                <th></th>
            </tr>
        <?php
            $_SESSION["delete_button_ids"] = [];
            $sql_all_messages = "SELECT * FROM forum_message WHERE theme_id = '" . $_SESSION["choosen_theme_id"] . "';";
            $result = mysqli_query($conn, $sql_all_messages);
            if(mysqli_num_rows($result) > 0) {
                while($message = mysqli_fetch_assoc($result)){
        
                        $sql_get_username = "SELECT * FROM users WHERE user_id = '" . $message["user_id"] . "';";
                        $message_sender_username = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_username));
        
                    if(isset($message_sender_username["username"])){
                        $message_sender_username = $message_sender_username["username"];
                        echo "<tr>";
                        echo "<td id='sender'>" . $message_sender_username . "</td>";
                        echo "<td>" . $message["message"] . "</td>";
                        echo "<td id='time'>" . $message["date"] . "</td>";
        
                        if(isset($_SESSION["username"]) && $_SESSION["username"] == $message_sender_username){
                            echo "<td>" . "<input type='submit' name='" . $message["message_id"] . "' value='törlés'>" . "</td>";
                            $_SESSION["delete_button_ids"][] = $message["message_id"];
                        }
                        else{
                            echo "<td></td>";
                        }
                        echo "</tr>";
                    }
                }
            }
            else{
                echo "<p class='alert alert-info'>Még nincs üzenet ebben a témában!</p>";
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