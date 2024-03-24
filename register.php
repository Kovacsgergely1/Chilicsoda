<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./register.css">
    <title>Chilicsoda - Regisztráció</title>
</head>
<body>
    <h1>register</h1>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <label>Felhasználónév:</label><br>
        <input type="text" name="username"><br>
        
        <label>Jelszó:</label><br>
        <input type="password" name="password"><br>
        
        <label>Teljes név:</label><br>
        <input type="text" name="full_name"><br>
        
        <label>Email:</label><br>
        <input type="email" name="email"><br>
      
        <label>Szállítási és számlázási cím:</label><br>
        <input type="text" name="location"><br>
       
        <label>Telefon:</label><br>
        <input type="tel" name="telephone"><br>

        <input type="submit"  name="register" value="register" id="register">
        <input type="submit" name="login" value="Log in" id="login">
    </form>
</body>
</html>

<?php
    if(isset($_POST["login"])){
        header("Location: login.php");
    }

    include("database_connection.php");
    if(isset($_POST["register"])){

        if(!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["full_name"]) && !empty($_POST["email"]) && !empty($_POST["location"]) && !empty($_POST["telephone"])){

            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);

            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $full_name = filter_input(INPUT_POST, "full_name", FILTER_SANITIZE_SPECIAL_CHARS);


            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $location = filter_input(INPUT_POST, "location", FILTER_SANITIZE_SPECIAL_CHARS);
            $phone_number = filter_input(INPUT_POST, "telephone", FILTER_SANITIZE_NUMBER_INT);
            // echo $phone_number;

            
            $sql = "INSERT INTO users (username, password, location, phone_number, email, full_name)
                    VALUES ('$username', '$hashed_password', '$location', '$phone_number', '$email', '$full_name')";
        
            try{
                mysqli_query($conn, $sql);
                session_start();
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $hashed_password;
                $_SESSION["full_name"] = $full_name;
                $_SESSION["email"] = $email;
                $_SESSION["location"] = $location;
                $_SESSION["phone_number"] = $phone_number;
                $_SESSION["cart"] = [];
                header("Location: index.php");
            }
            catch(mysqli_sql_exception){
                echo "Ezzel a felhasználónév/email címmel már rendelkezik fiók az oldalon";
            }
        }
        else{
            echo "<h1>Kérem tötse ki az összes mezőt!<h1>";
        }
    }
?>