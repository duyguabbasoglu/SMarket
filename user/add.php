<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: ../login/login.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$product_id = intval($_GET['id']);

$stmt = $db->prepare("SELECT * FROM productlist WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product || strtotime($product['expiration_date']) < time()) {
    header("Location: dashboard.php?error=expired");
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += 1;
} else {
    $_SESSION['cart'][$product_id] = [
        'title' => $product['title'],
        'price' => $product['discount_price'],
        'image' => $product['image'],
        'quantity' => 1
    ];
}

header("Location: dashboard.php?success=added");
exit;
?>
