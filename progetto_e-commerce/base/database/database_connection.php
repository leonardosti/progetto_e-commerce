<?php

$dns = "mysql:host=localhost;dbname=luxeshades";
$username = "root";
$password = "";
try{
    return new PDO($dns, $username, $password, [PDO::ATTR_ERRMODE =>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_OBJ]);
}catch(PDOException $e){
    // error message
    echo "<script>alert('" . addslashes($e->getMessage()) . "');</script>";
}

?>