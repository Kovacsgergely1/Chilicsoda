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
    foreach($_SESSION["cart"] as $item => $quantity){
        if(isset($_POST["d-" . $item])){
            unset($_SESSION["cart"][$item]);
            echo "<p class='alert alert-success'>Elem sikeresen törölve!</p>";
            break;
            // !!! elem sikeresen törölve
        }
    }
  

    //termék mennyiségének megváltoztatása
    if (isset($_SESSION["cart"])){
        foreach($_SESSION["cart"] as $id => $quantity){
            if(isset($_POST["r-" . $id])){                                // megnyomták e a gombot
                $sql_get_in_stock = "SELECT * FROM product WHERE product_id = $id;";
                $in_stock = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_in_stock))["in_stock"];

                if(!empty($_POST["q-" . $id]) && $_POST["q-" . $id] > 0 && $_POST["q-" . $id] <= $in_stock){       //érvényes-e, és nem-e negatív a megadott érték
                    $_SESSION["cart"][$id] = $_POST["q-" . $id];
                }
                elseif($_POST["q-" . $id] > $in_stock){
                    echo "<p class='alert alert-danger'>Nincs ennyi a raktáron!</p>";
                    //!!! alert?
                }
                else{
                    echo "<p class='alert alert-warning'>Üres a megadott érték, vagy negatív!</p>";
                    // !!! alert?
                }
            }
        }
    }


    //rendelés gomb
    if(isset($_POST["order"]) && count($_SESSION["cart"]) != 0){

        //rendelés felvevése az adatbázisba ------------------------------------------------------------------------------------------------------------
        $username = $_SESSION["username"];
        $user_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE username = '$username';"))["user_id"];
        
        $insert_into_orders = "INSERT INTO orders(user_id, order_price) VALUES ($user_id, " . $_SESSION["total"] . ")";
        mysqli_query($conn, $insert_into_orders);
        
        $select_order = "SELECT * FROM orders ORDER BY order_id DESC LIMIT 1";
        $order_id = mysqli_fetch_assoc(mysqli_query($conn, $select_order))["order_id"];

        foreach($_SESSION["cart"] as $item_id => $quantity){
            $current_push_query = "INSERT INTO product_combination(order_id, product_id, piece) VALUES($order_id, $item_id, $quantity);";
            mysqli_query($conn, $current_push_query);
        }

        //in_stock csökkentése
        foreach($_SESSION["cart"] as $item_id => $quantity){
            $sql_get_in_stock = "SELECT * FROM product WHERE product_id = $item_id;";
            $remaining_in_stock = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_in_stock))["in_stock"] - $quantity;        

            $sql_update_quantity = "UPDATE product SET in_stock = $remaining_in_stock WHERE product_id = $item_id;";
            mysqli_query($conn, $sql_update_quantity);
        }





        //email küldése ------------------------------------------------------------------------------------------------------------
        
        $email_body_php = "";

        $i = 1;
        foreach($_SESSION["cart"] as $current_item_id => $quantity){
            $sql = "SELECT * FROM product WHERE product_id = $current_item_id;";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);

            $product_name = $row["product_name"];
            $product_price = $row["price"];

            $email_body_php .= "<h3>$i, " . $product_name . " - " .  $quantity . "db</h3>";

            if(isset($row["sale"]) && $row["sale"] > 0){
                $email_body_php .= "<s>" . $row["price"] * $quantity . "Ft</s>";
                $email_body_php .= "<h4> helyett: " . ($row["price"] - ($row["price"] * $row["sale"])) * $quantity . " Ft</h4>";
            }
            else{
                $email_body_php .= "<h4>" . $product_price * $quantity. "</h4><br>";
            }
            $i++;
        }
        $email_body_php = "<br>PackX szállítás: 0Ft"
        $email_body_php = "<br>Összesen: " . $_SESSION["total"] . "Ft"
        $email_body_php = "<br>Köszönjük a rendelést és a belénk fektetett bizalmat!"
        $email_body_php .= "<br>Chilicsoda<br>1234 Kiskutya<br>Kismacska út 1.<br>Ha kérdése van írjon: info@chilicsoda.hu";

        $email_body = '
        
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Document</title>
        </head>
        <body>
        
                <h1>Köszönjük megrendelését!</h1>
                <p>Név: ' . $_SESSION["full_name"] . ' </p>
                <p>Szállítási cím: ' . $_SESSION["location"] . ' </p>
                <p>telefonszám: ' . $_SESSION["phone_number"] . ' </p>
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
        $mail->Subject = "Rendeles visszaigazolas";
        $mail->Body = $email_body;
        $mail->send();

        header("Location: order_received.php");     


    }

    elseif(count($_SESSION["cart"]) == 0){
        echo "<p class='alert alert-danger'>Üres a kosár tartalma!</p>"
    }
    
?>

<!DOCTYPE html>
<html lang="hu">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="webshop.css">
    <link rel="stylesheet" href="./sidenav.css">
    <script src="./sidenav.js"></script>
    <title>Chilicsoda - Checkout</title>
</head>
<body>
<div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="index.php">Főoldal</a>
        <a href="about.php">Rólunk</a>
        <a href="forum.php">Fórum</a>
        <a id="sidenav-selected" disabled>Webshop</a>
        <?php
    if(isset($_SESSION["username"]) && $_SESSION["username"] == "admin"){
        echo "<a href='admin_page.php'>Admin</a>";
    }
    ?>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="" diabled><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <input type="submit" name="logout" value=""><img class="float-end logout" src="Login.png" alt="Logout"></input>
    </div>



    <h2>Termékek:</h2> <br><br>
    <form action="checkout.php" method="post">
    <?php

        $_SESSION["total"] = 0;
        foreach($_SESSION["cart"] as $current_item_id => $quantity){
            //leszedi az adatabázisból az itemet
            $sql = "SELECT * FROM product WHERE product_id = $current_item_id;";
            $product = mysqli_fetch_assoc(mysqli_query($conn, $sql));
            
            // kiírja az itemet


            $sql_get_image = "SELECT * FROM product_image WHERE product_id = $current_item_id ORDER BY image_id LIMIT 1;";
            $image = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_image))["image_data"];

            //kép + név
            echo "<img src='$image' style='width: 50px;'>";  //!!! itt beállítottam a képnek egy szélességet, vigyázz, nehogy szopj vele
            echo "<br>" . $product["product_name"];
            
            // darabszám
            echo "<br>darabszám: ";
            echo "<input type='number' name='q-$current_item_id' value='$quantity'>";
            echo "<input type='submit' name='r-$current_item_id' value='frissítés'>";
            echo " db.";
            
            //ára

            if(isset($product["sale"]) && $product["sale"] > 0){
                echo "<br> " . $product["sale"]*100 .   "% leárazás: ";
                echo "<s>" . $product["price"] . " Ft/db</s>";
                echo " helyett: " . $product["price"] - ($product["price"] * $product["sale"]) . " Ft/db";
                echo " * " . $quantity . "db = " . ($product["price"] - ($product["price"] * $product["sale"])) * $quantity . "Ft";
                
                $_SESSION["total"] += ($product["price"] - ($product["price"] * $product["sale"])) * $quantity;
            }
            else{
                echo "<br>" . $product["price"] . "Ft * " . $quantity . "db = " . $product["price"] * $quantity . "Ft";
                $_SESSION["total"] += $product["price"] * $quantity;
            }

            //törlés
            echo "<br><input type='submit' name='d-" . $current_item_id . "' value='törlés'>";
            echo "<br><br><br><br>";

        }

        echo "összesen: " . $_SESSION["total"] . " Ft <br><br>";

        // !!!
        echo "Szállítási adatok: <br>";
        echo "név: " . $_SESSION["full_name"] . "<br>";
        echo "cím: " . $_SESSION["location"] . "<br>";
        echo "telefonszám: " . $_SESSION["phone_number"] . "<br>";
        echo "e-mail: " . $_SESSION["email"] . "<br>";


    ?>  
</form>

    
    <form action="checkout.php" method="post">
        <br>
        <input type="submit" name="order" value="order" id="order">
    </form>
</body>
</html>