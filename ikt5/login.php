<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);


    if(isset($_POST["register"])){
        header("Location: register.php");
    }
    include("database_connection.php");
    if(isset($_POST["login"])){
        

        if(!empty($_POST["username"] && !empty($_POST["password"]))){
            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);

            $sql = "SELECT * FROM users WHERE username = '" . $username . "';";
            $result = mysqli_query($conn, $sql);
            $user = mysqli_fetch_assoc($result);
        
            if(mysqli_num_rows($result) > 0){
                $filterd_pw = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
                if(password_verify($filterd_pw, $user["password"])){
                    session_start();
                    $_SESSION["username"] = $username;
                    $_SESSION["email"] = $user["email"];
                    $_SESSION["location"] = $user["location"];
                    $_SESSION["phone_number"] = $user["phone_number"];
                    $_SESSION["cart"] = [];
                    $_SESSION["choosen_theme_id"] = [];
                    header("Location: index.php");
                }
                else{
                    echo "Hibás jelszó! Kérem, próbálja újra!";
                }
            }
            else{
                echo "Felhasználó nem található!";
            }



            // $_SESSION["username"] = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);
            // $_SESSION["password"] = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
            // echo $_SESSION["username"];
            // echo $_SESSION["password"];
            // header("location: index.php");
        }
        else{
            echo "<h1>Kérem tötse ki mindkettő mezőt!<h1>";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>login</h1>
    <form action="login.php" method="post">
        <label>username:</label><br>
        <input type="text" name="username"><br>
        
        <label>password:</label><br>
        <input type="password" name="password"><br>
        
        <input type="submit" name="login" value="Log in">
        <input type="submit" name="register" value="Register">
    </form>
</body>
</html>