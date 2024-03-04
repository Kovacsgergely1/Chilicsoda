<?php
    session_start();
    $product_id = $_SESSION["choosed_item_id"];
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // if(!isset($_SESSION["username"])){
    //     header("Location: login.php");
    // }

    if(isset($_POST["logout"])){
        session_destroy();
        header("Location: login.php");
    }
    
    if(isset($_POST["webshop"])){
        header("Location: webshop.php");
    }
    include("database_connection.php");



    // létrehozza a kosarat, ha még nem létezik
    if(!isset($_SESSION["cart"])){
        $_SESSION["cart"] = [];
    }


    //hozzáadja a kosárhoz a terméket

    if(isset($_POST[strval($product_id)])){                                     //megnyomták e a gombot

        $sql_get_in_stock = "SELECT * FROM product WHERE product_id = $product_id;";
        $in_stock = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_in_stock))["in_stock"];
        $in_cart = 0;
        if(isset($_SESSION["cart"][$product_id])){
            $in_cart = $_SESSION["cart"][$product_id];
        }

        if(!empty($_POST["q-" . $product_id]) && $_POST["q-" . $product_id] > 0 && $_POST["q-" . $product_id] + $in_cart <= $in_stock){       //van-e érték adva a darabszámnak, és nem-e negatív, és van-e annyi a raktáron
            if(!isset($_SESSION["cart"][$product_id])){                         //ha még nincs benne a cartban, akk beleteszi
                    $_SESSION["cart"][$product_id] = $_POST["q-" . $product_id];
                    // echo "új";
            }
            else{                                                       //ha benne van a cart-ba, akk megnöveli ennyivel a darabszámát
                $_SESSION["cart"][$product_id] += intval($_POST["q-" . $product_id]);
                // echo "megvan";
            }
        }
        elseif($_POST["q-" . $product_id] + $in_cart > $in_stock){
            echo "nincs ennyi a raktáron!";
            // !!! alert?
        }
        else{
            echo "üres a megadott érték, vagy negatív";
            // !!! alert?
        }
    }

    //képek lapozása --------------------------------------------------------------------------------
    // képek kilistázása
    if(empty($_SESSION["images"])){
        $sql_get_images = "SELECT * FROM product_image WHERE product_id = $product_id;";
        $result = mysqli_query($conn, $sql_get_images);
        // echo mysqli_num_rows($result);
        
        
        while($row = mysqli_fetch_assoc($result)){
            $_SESSION["images"][] = $row["image_data"];
        }
    }
        
    if(isset($_POST["right"]) && $_SESSION["current_image_index"] + 1 < count($_SESSION["images"])){
        $_SESSION["current_image_index"] += 1;
    }
    if(isset($_POST["left"]) && $_SESSION["current_image_index"] - 1 >= 0){
        $_SESSION["current_image_index"] -= 1;
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
<form action="item.php" method="post">

    <?php
        $sql = "SELECT * FROM product WHERE product_id = " . $product_id;
        $result = mysqli_query($conn, $sql);
    
        //termékek kiírása
        if(mysqli_num_rows($result) > 0){
            $row = mysqli_fetch_assoc($result);
            
            $product_name = $row["product_name"];
            $product_description = $row["product_description"];

            echo "<form  action='item.php' method='post'>";

            //brand name
            $sql_get_brand_name = "SELECT * FROM brand WHERE brand_id =" . $row["brand_id"];
            $brand_name = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_brand_name))["brand_name"];
            echo $brand_name . ": ";
            echo $product_name . "<br>";



            
            // képek "<img src='data:image/png;base64," . base64_encode($imagedata) . "' style='width: 300px;'> <br>";
            echo "<input type='submit' name='left' value='<-'>";
            
            // while($image = mysqli_fetch_assoc($result)){
            //         $image_data = $image["image_data"];
            //         echo "<img src='data:image/png;base64," . base64_encode($image_data) . "' style='width: 300px;'> <br>";
            //     }
                
            echo "<img src=" . $_SESSION["images"][$_SESSION["current_image_index"]] . " style='width: 300px;'> <br>";
            echo $product_description . "<br>";
            

            echo "<input type='submit' name='right' value='->'>";

            // akció/ár
            if(isset($row["sale"]) && $row["sale"] > 0){
                echo " " . $row["sale"]*100 .   "% leárazás: ";
                echo "<s>" . $row["price"] . " Ft/db</s>";
                echo " helyett: <br>" . $row["price"] - ($row["price"] * $row["sale"]) . " Ft/db <br>";
            }
            else{
                echo $row["price"] . " Ft/db <br>";
            }

            //erősség
            echo " Erőssége: ";
            for($i=1; $i <= $row["spiciness"]; $i++){
                echo "<img src='spicy.png' alt='erős' style='width: 30px'>";
            }
            for($i=1; $i <= 5 - $row["spiciness"]; $i++){
                echo "<img src='not_spicy.png' alt='nem erős' style='width: 30px'>";
            }

            
            //in stock + kosárhoz adás
            if(isset($_SESSION["username"])){
                if($row["in_stock"] > 0){
                    echo "<input type='number' name='q-$product_id' value='1'>";
                    echo "raktáron: " . $row["in_stock"] . "db";
                    echo "<input type='submit' name='" . $product_id . "' value='kosárhoz ad'>";
                }
                else{
                    echo " nincs raktáron!";
                    //!!!
                }
            }

            echo "<br><br><br><br>";
        }
    ?>

    <br>
    <input type="submit" name="webshop" value="webshop"><br>

    <?php
    if(isset($_SESSION["username"])){
        echo '<input type="submit" name="logout" value="logout">';
    }

    ?>
    
</form>
</body>
</html>