<?php
    require_once "./uitils/config.php";
    require_once "./uitils/commamn.php";
    // session_start();
    // $name = $_SESSION['name'];
    // $email = $_SESSION['email'];
    // $mobile = $_SESSION['phone'];
    $tid = $_GET['tid'];
    $amount = $_GET['amount']/100;

    echo 'Transaction Id: - '. $tid . '<br>Amount : '.$amount;
?>