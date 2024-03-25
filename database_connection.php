<?php
    $db_server = "sql113.infinityfree.com";
    $db_user = "if0_35969498";
    $db_pass = "q3aovQx4EQiZt";
    $db_name = "if0_35969498_chilicsoda";


    try{
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
        mysqli_set_charset($conn,"utf8");
        // echo "fasza";
    }
    catch(mysqli_sql_exception){
        echo "sikertelen csatlakozás az adatbázishoz";
    }
?>