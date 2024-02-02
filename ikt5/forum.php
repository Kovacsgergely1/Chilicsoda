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

    include("database_connection.php");

    if(isset($_POST["new_theme"])){
        $theme_name = filter_input(INPUT_POST, "new_theme_name", FILTER_SANITIZE_SPECIAL_CHARS);

        //check hogy létezik e már azzal a névvel
        $sql_check_existence = "SELECT * FROM forum_themes WHERE theme_name = '" . $theme_name . "';";
        $result = mysqli_query($conn, $sql_check_existence);
        if(mysqli_num_rows($result) == 0){

            $sql_get_userid = "SELECT * FROM users WHERE username = '" . $_SESSION["username"] . "';";
            $user_id = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_userid))["user_id"];
    
    
            $sql_create_new_theme = "INSERT INTO forum_themes (theme_name, user_id) VALUES ('$theme_name', '$user_id');";
            try{
                mysqli_query($conn, $sql_create_new_theme);
                // !!! ki kellene írni, hogy téma sikeresen létrehozva
            }
            catch(mysqli_sql_exception){
                // !!! ki kellene írni, hogy téma sikeresen létrehozva
            }
        
        }
        else{
            echo "már létezik ezzel a névvel egy téma!";
            // !!!
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="forum.php" method="post">
    <br>
    Új téma neve:
    <input type="text" name="new_theme_name">
    <input type="submit" name="new_theme" value="új téma létrehozása"><br>
    <input type="submit" name="logout" value="logout"><br><br>

    Témák:
    <table>
        <tr>
            <th>Téma neve</th>
            <th>Téma létrehozója</th>
            <th>Téma törlése</th>
        </tr>
        <?php
        $_SESSION["delete_button_ids"] = [];
        $sql_select_all_theme = "SELECT * FROM forum_themes;";
        $result = mysqli_query($conn, $sql_select_all_theme);
        while ($theme = mysqli_fetch_assoc($result)){
            echo "<tr>";
            
            echo "<td> <input type='submit' name='" . $theme["theme_id"] . "' value='" . $theme["theme_name"] . "'></td>";
            $sql_get_username = "SELECT * FROM users WHERE user_id = '" . $theme["user_id"] . "';";
            $username = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_username))["username"];

            echo "<td>" . $username . "</td>";

            if($_SESSION["username"] == $username){
                echo "<td>" . "<input type='submit' name='d-" . $theme["theme_id"] . "' value='törlés'>" . "</td>";
                $_SESSION["delete_button_ids"][] = $theme["theme_id"];
            }
            else{
                echo "<td></td>";
            }

            echo "</tr>";
        }
        ?>
    </table>


</form>
</body>
</html>

<style>
table{
    width: 90vw;
    margin: auto;
}

th, td{
    border: 1px solid black;
    text-align: center;
}
</style>