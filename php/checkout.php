<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

use function PHPSTORM_META\type;

    require "phpmailer/src/PHPMailer.php";
    require "phpmailer/src/Exception.php";
    require "phpmailer/src//SMTP.php";

    //hibakódok kiírása, alap checkek a loginnel-logouttal kapcsolatban, database connection includeolása
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if(!isset($_SESSION["username"])){
        header("Location: login.php");
    }

    if(isset($_POST["logout"])){
        echo "destroyed";
        session_destroy();
        header("Location: login.php");
    }
   
    if(isset($_POST["vissza"])){
        header("Location: webshop.php");
    }
    include("database_connection.php");

    //termék törlése ------------------------------------------------------------------------------------------------------------
    foreach($_SESSION["cart"] as $item){
        if(isset($_POST[$item])){
            $index = array_search($item, $_SESSION["cart"]);
            unset($_SESSION["cart"][$index]);
            echo "siker";
            break;
            // !!! elem sikeresen törölve
        }
    }
  
    if(isset($_POST["order"]) && count($_SESSION["cart"]) != 0){

        //rendelés felvevése az adatbázisba ------------------------------------------------------------------------------------------------------------
        $username = $_SESSION["username"];
        $user_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username = '$username';"))["user_id"];
        
        $insert_into_orders = "INSERT INTO orders(user_id) VALUES ($user_id)";
        mysqli_query($conn, $insert_into_orders);
        
        $select_order = "SELECT * FROM orders ORDER BY order_id DESC LIMIT 1";
        $order_id = mysqli_fetch_assoc(mysqli_query($conn, $select_order))["order_id"];

        foreach($_SESSION["cart"] as $item){
            $current_push_query = "INSERT INTO product_combination(order_id, product_id) VALUES($order_id, $item);";
            mysqli_query($conn, $current_push_query);
        }





        //email küldése ------------------------------------------------------------------------------------------------------------
        
        $email_body_php = "";

        
        foreach($_SESSION["cart"] as $current_item_id){
            $sql = "SELECT * FROM product WHERE product_id = $current_item_id;";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);

            $product_name = $row["product_name"];
            $product_price = $row["price"];

            $email_body_php .= "<h3>" . $product_name . "</h3>";
            $email_body_php .= "<h4>" . $product_price . "</h4 style='float: right;'><br>";
        }

        $email_body_php .= "Összesen: " . $_SESSION["total"] . "Ft -ba fáj(nak) ez(ek) a szar(ok) neked :)";

        $email_body = '
        
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>
        <body>
        
                <h1>köszönjük megrendelését</h1>
                <p>rendelései:</p>' . $email_body_php . '  
        </body>
        </html>
        
        ';


        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = 'chilicsoda@gmail.com';
        $mail->Password = 'cbaglwapftfgpgmn';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('chilicsoda@gmail.com');
        $mail->addAddress ($_SESSION["email"]);
        $mail->isHTML (true);
        $mail->Subject = "Rendelese";
        $mail->Body = $email_body;
        $mail->send();

        header("Location: order_received.php");     


    }
    elseif(count($_SESSION["cart"]) == 0){
        // !!! üres a kosár tartalma, és így nem adhatsz le rendelést!
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    termékek(ek): <br><br>
    <form action="checkout.php" method="post">
    <?php
        $_SESSION["total"] = 0;
        foreach($_SESSION["cart"] as $current_item_id){
            $sql = "SELECT * FROM product WHERE product_id = $current_item_id;";
            $product = mysqli_fetch_assoc(mysqli_query($conn, $sql));
            echo "<img src='data:image/png;base64," . base64_encode($product["product_image"]) . "' style='width: 50px;'>";
            echo "<input type='submit' name='" . $current_item_id . "' value='törlés'>";
            echo "<br>" . $product["product_name"];
            echo " " . $product["price"] . "Ft";
            echo "<br><br><br><br>";

            $_SESSION["total"] += $product["price"];
        }

        echo "összesen: " . $_SESSION["total"] . " Ft";


    ?>  
</form>

    
    <form action="checkout.php" method="post">
        <br>
        <input type="submit" name="order" value="order">
        <br>
        <input type="submit" name="logout" value="logout">
        <br>
        <input type="submit" name="vissza" value="vissza">
    </form>
</body>
</html>