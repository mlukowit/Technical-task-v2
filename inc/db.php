<?php
$db_host       = "localhost";
$db_name       = "tshirtpa_tasktest";
$db_user       = "tshirtpa_root";
$db_pass       = "Airblow1234";
try{
    $db = new PDO(
        "mysql:host={$db_host};dbname={$db_name}",
        $db_user,
        $db_pass
    );
    $db->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo $e->getMessage();
    echo 'Sorry, Could not connect to the database this moment. Try again later';
    exit;
}

session_start();
