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
?>

<?php
    // létrehozza a kosarat, ha még nem létezik
    if(!isset($_SESSION["cart"])){
        $_SESSION["cart"] = [];
    }

    //hozzáadja a kosárhoz a terméket
    if (isset($_SESSION["product_number"])){
        for($i = 1; $i <= $_SESSION["product_number"]; $i++){
            if(isset($_POST[strval($i)]) && !in_array(strval($i), $_SESSION["cart"])){
                array_push($_SESSION["cart"], strval($i));
            }
        }
    }
    
    //reseteli a kosarat, ha kell
    if(isset($_POST['reset'])){
        $_SESSION['cart'] = [];
    } 

    // if(count($_SESSION["cart"]) != 0){
    //     foreach($_SESSION["cart"] as $item){
    //         echo "item: " . $item;
    //     }
    //     echo "len: " . count($_SESSION["cart"]);
    // }

    // elküld a checkoutra
    if(isset($_POST["checkout"])){
        header("Location: checkout.php");
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
    
<!-- <input type="text" style="width: 300px;"> -->

<?php

    include("database_connection.php");

    $sql = "SELECT * FROM product";
    $result = mysqli_query($conn, $sql);
    $_SESSION["product_number"] = mysqli_num_rows($result);

    if(mysqli_num_rows($result) > 0){
        
        echo "<form  action='webshop.php' method='post'>";
        while($row = mysqli_fetch_assoc($result)){
            $imagedata = $row['product_image'];
            $product_id = $row["product_id"];
            $product_description = $row["product_description"];
            
            echo $row["product_name"] . "<br>";
            echo $product_description;
            echo "<img src='data:image/png;base64," . base64_encode($imagedata) . "' style='width: 300px;'>";
            echo "<input type='submit' name='" . $product_id . "' value='kosárhoz ad'> <br><br>";
            
            
        }
        echo "</form>";
    }

    
?>


<form action="webshop.php" method="post">
    <input type="submit" name="reset" value="reset">
    <input type="submit" name="checkout" value="checkout">
</form>

<form action="webshop.php" method="post">
    <br>
    <input type="submit" name="logout" value="logout">
</form>
</body>
</html>

