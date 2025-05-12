<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: ../login/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
}

header("Location: cart.php");
exit;
?>
