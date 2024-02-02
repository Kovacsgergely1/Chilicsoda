<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>register</h1>
    <form action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <label>username:</label><br>
        <input type="text" name="username"><br>
        
        <label>password:</label><br>
        <input type="password" name="password"><br>
        
        <label>email:</label><br>
        <input type="email" name="email"><br>
      
        <label>location:</label><br>
        <input type="text" name="location"><br>
       
        <label>telephone:</label><br>
        <input type="tel" name="telephone"><br>

        <input type="submit" name="register" value="register">
        <input type="submit" name="login" value="Log in">
    </form>
</body>
</html>

<?php
    if(isset($_POST["login"])){
        header("Location: login.php");
    }

    include("database_connection.php");
    if(isset($_POST["register"])){

        if(!empty($_POST["username"]) && !empty($_POST["password"]) && !empty($_POST["email"]) && !empty($_POST["location"]) && !empty($_POST["telephone"])){

            $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_SPECIAL_CHARS);

            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
            $location = filter_input(INPUT_POST, "location", FILTER_SANITIZE_SPECIAL_CHARS);
            $phone_number = filter_input(INPUT_POST, "telephone", FILTER_SANITIZE_NUMBER_INT);
            // echo $phone_number;

            
            $sql = "INSERT INTO users (username, password, location, phone_number, email)
                    VALUES ('$username', '$hashed_password', '$location', '$phone_number', '$email')";
        
            try{
                mysqli_query($conn, $sql);
                session_start();
                $_SESSION["username"] = $username;
                $_SESSION["password"] = $hashed_password;
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