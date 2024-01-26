<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    if($_SESSION["username"] != "admin"){
        header("Location: login.php");
    }

    if(isset($_POST["logout"])){
        session_destroy();
        header("Location: login.php");
    }

    if(isset($_POST["webshop"])){
        header("Location: webshop.php");
    }    

    include("database_connection.php");


    //rendelés törlése
    if(isset($_SESSION["admin_page_order_number"])){
        $sql_get_order_ids = "SELECT DISTINCT order_id FROM orders ORDER BY order_id;";
        $order_ids = mysqli_query($conn, $sql_get_order_ids);
        $list_of_order_ids = [];
        while($row = mysqli_fetch_assoc($order_ids)){
            array_push($list_of_order_ids, $row["order_id"]);
        }

        foreach($list_of_order_ids as $id){
            if(isset($_POST[$id])){
                //! ide kellene majd egy biztos vagy benne? gomb....
                $sql_delete_product_combination = "DELETE FROM product_combination WHERE `order_id` = $id;";
                mysqli_query($conn, $sql_delete_product_combination);

                $sql_delete_orders = "DELETE FROM orders WHERE `order_id` = $id;";
                mysqli_query($conn, $sql_delete_orders);

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
    admin page
    <form action="admin_page.php" method="post">
    <table style="width: 90vw; margin-top: 50px;">
        <tr>
            <th>Név</th>
            <th>Rendelt termékek</th>
            <th>Rendelés dátuma</th>
            <th>Rendelés törlése</th>
        </tr>
        <?php
            $sql_orders = "SELECT username, product_id, order_date, product_combination.order_id FROM ((users INNER JOIN orders ON users.user_id = orders.user_id) INNER JOIN product_combination ON orders.order_id = product_combination.order_id) ORDER BY orders.order_id;";
            $orders = mysqli_query($conn, $sql_orders);
            //végigmegy az összes rendelésen
            $_SESSION["admin_page_order_number"] = mysqli_num_rows($orders);

            //dictionaryt csinál hogy melyik order_id ből hány darab van
            $order_id_piece_assoc = array();
            while($row = mysqli_fetch_assoc($orders)){
                if(in_array($row["order_id"], array_keys($order_id_piece_assoc))){
                    $order_id_piece_assoc[$row["order_id"]] += 1;
                }
                else{
                    $order_id_piece_assoc[$row["order_id"]] = 1;
                }
            }

            //elkészíti a táblázatot
            $previous_order_id = 0;
            $orders = mysqli_query($conn, $sql_orders);
            while($row = mysqli_fetch_assoc($orders)){
                echo "<tr>";

                echo "<td>" . $row["username"] . "</td>";

                $sql_productid_productname = "SELECT * FROM product WHERE product_id =" . $row["product_id"];
                $product_name = mysqli_fetch_assoc(mysqli_query($conn, $sql_productid_productname))["product_name"];
                echo "<td>" . $product_name . "</td>";

                echo "<td>" . $row["order_date"] . "</td>";

                if($row["order_id"] != $previous_order_id){
                    echo "<td rowspan='" . $order_id_piece_assoc[$row["order_id"]] . "'>" . "<input type='submit' name='" . $row["order_id"] . "' value='törlés'>" . "</td>";
                    $previous_order_id = $row["order_id"];
                }
                
                echo "</tr>";
            }

        ?>
    </table>
    </form>

<form action="admin_page.php" method="post">
    <br>
    <input type="submit" name="webshop" value="webshop"><br>
    <input type="submit" name="logout" value="logout">
    <!-- <input type='submit' name='1' value='törlés'> -->

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