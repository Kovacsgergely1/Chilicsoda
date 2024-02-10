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

    //lenullázza az item-nél használt cuccokat
    $_SESSION["current_image_index"] = 0;
    $_SESSION["images"] = [];

    //elküldi az oldalra, ha megnyomta a gombot
    if (isset($_SESSION["avaliable_product_ids"])){
        foreach($_SESSION["avaliable_product_ids"] as $id){
            if(isset($_POST["button-$id"])){
                $_SESSION["choosed_item_id"] = $id;
                header("Location: item.php");
            }
        }
    }


    




    // elküld a checkoutra
    if(isset($_POST["checkout"])){
        header("Location: checkout.php");
    }


    //szűrő feltételek kiszedése ------------------------------------------------------------------------------------
    //szűrési paraméterek létrehozása/lenzullázása
    if(!isset($_SESSION["filter_parameters"]) || isset($_POST["filter_reset"]) || isset($_POST["filter_refresh"])){
        $_SESSION["filter_parameters"] = [];
    }

    //összes brand id-ának kilistázása
    $_SESSION["avaible_brand_ids"] = [];
    
    $sql_get_bradns = "SELECT * FROM brand;";
    $result = mysqli_query($conn, $sql_get_bradns);

    while($row = mysqli_fetch_assoc($result)){
        $_SESSION["avaible_brand_ids"][] = $row["brand_id"];
    }


    //ez azért kell, hogy bármilyen oldalfrissülésnél (pl kosárhoz adás) ne írja fellül az összes brand_id-val
    //megnézi, hogy létezik-e már a lista
    if(!isset($_SESSION["filter_parameters"]["brands"])){
        $_SESSION["filter_parameters"]["brands"] = $_SESSION["avaible_brand_ids"];      //alapból megkapja az összes értéket, hogy az első kiírásnál a gomboknál mindegyik be legyen pipálva
    }
    //megnézi, hogy létezik-e már a lista
    if(!isset($_SESSION["filter_parameters"]["spicy"])){
        $_SESSION["filter_parameters"]["spicy"] = [1, 2, 3, 4, 5];      //alapból megkapja az összes értéket, hogy az első kiírásnál a gomboknál mindegyik be legyen pipálva
    }

    if(isset($_POST["filter_refresh"])){
        //brands
        $_SESSION["filter_parameters"]["brands"] = [];                              //reseteli a brand idkat, mivel eleinte megadjuk neki az összeset

        foreach($_SESSION["avaible_brand_ids"] as $current_id){                 //végigmegy az összes lehetséges brand id-n
            if(isset($_POST["brand-" . $current_id])){                          //megnézi, hogy be van-e tick-elve a checkbox-a
                $_SESSION["filter_parameters"]["brands"][] = $current_id;       //hozzáadja a filterhez a mostani brand id-t
            }
        }

        //spiciness
        $_SESSION["filter_parameters"]["spicy"] = [];                              //reseteli a brand idkat, mivel eleinte megadjuk neki az összeset

        for($current_spiciness = 1; $current_spiciness <= 5; $current_spiciness++){                 //végigmegy az összes lehetséges brand id-n
            if(isset($_POST["spicy-" . $current_spiciness])){                          //megnézi, hogy be van-e tick-elve a checkbox-a
                $_SESSION["filter_parameters"]["spicy"][] = $current_spiciness;       //hozzáadja a filterhez a mostani brand id-t
            }
        }

    }   

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous"> -->
    <title>Document</title>
</head>
<body>
    
<!-- szűrő -->
<form action="webshop.php" method="post">
    <?php

        //brands
        $sql_get_bradns = "SELECT * FROM brand;";
        $result = mysqli_query($conn, $sql_get_bradns);

        while($row = mysqli_fetch_assoc($result)){
            if(in_array($row["brand_id"], $_SESSION["filter_parameters"]["brands"])){
                echo "<input type='checkbox' name='brand-" . $row["brand_id"] . "' checked>" . $row["brand_name"] . "<br>";
            }
            else{
                echo "<input type='checkbox' name='brand-" . $row["brand_id"] . "'>" . $row["brand_name"] . "<br>";
            }
        }
        
        //erősség
        echo "Erősség: ";
        for($i=1; $i <= 5; $i++){
            if(in_array($i, $_SESSION["filter_parameters"]["spicy"])){
                echo "<input type='checkbox' name='spicy-$i' checked>$i";
            }
            else{
                echo "<input type='checkbox' name='spicy-$i'>$i";
            }
        }
    ?>
        
    <input type="submit" name="filter_refresh" value="frissítés">
    <input type="submit" name="filter_reset" value="visszaállítás">
</form>

<?php
    //szűrő feltételek kiszedése ------------------------------------------------------------------------------------


    
    $sql = "SELECT * FROM product WHERE available = TRUE";
    $result = mysqli_query($conn, $sql);

    // product id-k kiszedése
    $_SESSION["avaliable_product_ids"] = [];
    while($row = mysqli_fetch_assoc($result)){
        $_SESSION["avaliable_product_ids"][] = $row["product_id"];
    }
    $result = mysqli_query($conn, $sql);

    //termékek kiírása
    if(mysqli_num_rows($result) > 0){
        
        echo "<form  action='webshop.php' method='post'>";
        while($row = mysqli_fetch_assoc($result)){
            if(in_array($row["brand_id"], $_SESSION["filter_parameters"]["brands"]) && in_array($row["spiciness"], $_SESSION["filter_parameters"]["spicy"])){

                $imagedata = $row['product_image'];
                $product_id = $row["product_id"];
                $product_description = $row["product_description"];
                
                //alap kiírások ---------------------------------------------------------------------------
                
                echo "<button type='submit' name='button-$product_id'>";
                    //név
                    echo $row["product_name"] . "<br>";
                    
                    //kép
                    $sql_get_image = "SELECT * FROM product_image WHERE product_id = $product_id ORDER BY image_id LIMIT 1;";
                    $image = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_image))["image_data"];

                    echo "<img src='data:image/png;base64," . base64_encode($image) . "' style='width: 300px;'> <br>";

                    //ár
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

                    //raktáron
                    if($row["in_stock"] > 0){
                        echo "raktáron " . $row["in_stock"] . "db";
                        //!!!
                    }
                    else{
                        echo "nincs raktáron!";
                        //!!!
                    }
                echo "</button><br>";

                
                


                // //brand name
                // $sql_get_brand_name = "SELECT * FROM brand WHERE brand_id =" . $row["brand_id"];
                // $brand_name = mysqli_fetch_assoc(mysqli_query($conn, $sql_get_brand_name))["brand_name"];
                // echo $brand_name . ": ";

                // echo $product_description;

                // //képek "<img src='data:image/png;base64," . base64_encode($imagedata) . "' style='width: 300px;'> <br>";
                // // $sql_get_images = "SELECT * FROM product_image WHERE product_id = $product_id;";
                // // $result = mysqli_query($conn, $sql_get_images);

                // // while($image_data = mysqli_fetch_assoc($result)){
                // //     echo "<img src='data:image/png;base64," . base64_encode($imagedata) . "' style='width: 300px;'> <br>";
                // // }

                // echo "<img src='data:image/png;base64," . base64_encode($imagedata) . "' style='width: 300px;'> <br>";
                
                // // akció/ár
                // if(isset($row["sale"]) && $row["sale"] > 0){
                //     echo " " . $row["sale"]*100 .   "% leárazás: ";
                //     echo "<s>" . $row["price"] . " Ft/db</s>";
                //     echo " helyett: <br>" . $row["price"] - ($row["price"] * $row["sale"]) . " Ft/db <br>";
                // }
                // else{
                //     echo $row["price"] . " Ft/db <br>";
                // }

                // //erősség
                // echo " Erőssége: ";
                // for($i=1; $i <= $row["spiciness"]; $i++){
                //     echo "<img src='spicy.png' alt='erős' style='width: 30px'>";
                // }
                // for($i=1; $i <= 5 - $row["spiciness"]; $i++){
                //     echo "<img src='not_spicy.png' alt='nem erős' style='width: 30px'>";
                // }

                
                // //in stock + kosárhoz adás
                // if($row["in_stock"] > 0){
                //     echo "<input type='number' name='q-$product_id' value='1'>";
                //     echo "raktáron: " . $row["in_stock"] . "db";
                //     echo "<input type='submit' name='" . $product_id . "' value='kosárhoz ad'>";
                // }
                // else{
                //     echo " nincs raktáron!";
                //     //!!!
                // }

                // echo "<br><br><br><br>";
            }
            
        }
    }

    

    if(isset($_SESSION["username"])){
        echo '<input type="submit" name="reset" value="reset">';
        echo '<input type="submit" name="checkout" value="checkout">';
        echo '<br>';
        echo '<input type="submit" name="logout" value="logout">';
    }
?>
    
    
</form>
</body>
</html>

