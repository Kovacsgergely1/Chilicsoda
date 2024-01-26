<?php
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "chilicsoda";


    try{
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
        // echo "fasza";
    }
    catch(mysqli_sql_exception){
        echo "sikertelen csatlakozás az adatbázishoz";
    }
?>