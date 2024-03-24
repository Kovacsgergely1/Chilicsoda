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
<html lang="hu">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="admin_page.css">
    <link rel="stylesheet" href="./sidenav.css">
    <script src="./sidenav.js"></script>
    <title>Chilicsoda - Admin</title>
</head>
<body>
<div id="Sidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="index.php">Főoldal</a>
        <a href="about.php">Rólunk</a>
        <a href="forum.php">Fórum</a>
        <a href="webshop.php">Webshop</a>
        <a id="sidenav-selected" disabled>Admin</a>
        <?php
            if(isset($_SESSION['username'])){
				echo "<h5 class='username'>Üdvözlünk {$_SESSION['username']}!</h5>";
			}
        ?>
        <?php
            if(isset($_SESSION["username"])){
                echo '<input type="submit" name="logout" value="logout" class="logout">';
        }
        ?>
    </div>
    </div>

    <div class="headbar">
        <span style="font-size:40px;cursor:pointer;padding-left:10px;" onclick="openNav()">&#9776;</span>
        <a href="" diabled><img class="logo" src="./chilixd.png" alt="Logo_helye"></a>
        <img class="float-end login" src="./Login.png" alt="Logout">
    </div>

    <form action="admin_page.php" method="post">
    <table style="width: 90vw; margin-top: 50px;">
        <tr>
            <th>Név</th>
            <th>Rendelt termékek</th>
            <th>Mennyiség</th>
            <th>Ára</th>
            <th>Rendelés dátuma</th>
            <th>Rendelés törlése</th>
        </tr>
        <?php
            $sql_orders = "SELECT username, product_id, order_date, product_combination.order_id, product_combination.piece, orders.order_price FROM ((users INNER JOIN orders ON users.user_id = orders.user_id) INNER JOIN product_combination ON orders.order_id = product_combination.order_id) ORDER BY orders.order_id;";
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

            //elkészíti a táblázatot ------------------------------------------------------------------------------------
            $previous_order_id = 0;
            $new_row = false;
            $orders = mysqli_query($conn, $sql_orders);
            while($row = mysqli_fetch_assoc($orders)){
                //megnézi, hogy új rendelés-e -------------------------------------
                if($row["order_id"] != $previous_order_id){
                    $new_row = true;
                    $previous_order_id = $row["order_id"];
                }
                else{
                    $new_row = false;
                }

                $current_rowspan = $order_id_piece_assoc[$row["order_id"]];


                //táblázat --------------------------------------------------------
                echo "<tr>";

                // név
                if($new_row){
                    echo "<td rowspan='" . $current_rowspan . "'>" . $row["username"] . "</td>";
                }

                // rendelt termék neve
                $sql_productid_productname = "SELECT * FROM product WHERE product_id =" . $row["product_id"];
                $product_name = mysqli_fetch_assoc(mysqli_query($conn, $sql_productid_productname))["product_name"];
                echo "<td>" . $product_name . "</td>";

                // mennyiség
                echo "<td>" . $row["piece"] . " db" . "</td>";
                
                if($new_row){
                    // ára
                    echo "<td rowspan='" . $current_rowspan . "'>" . $row["order_price"] . " Ft" . "</td>";
    
                    //rendelés dátuma
                    echo "<td rowspan='" . $current_rowspan . "'>" . $row["order_date"] . "</td>";
    
                    //törlés gomb
                    echo "<td rowspan='" . $current_rowspan . "'>" . "<input type='submit' name='" . $row["order_id"] . "' value='törlés' id='torles'>" . "</td>";
                    
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