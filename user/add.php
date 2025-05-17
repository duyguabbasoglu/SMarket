<?php
session_start();
require '../db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

$id = intval($_POST['id']);
$stmt = $db->prepare("SELECT * FROM productlist WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo json_encode(['success' => false, 'error' => 'Product not found']);
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$current_quantity = $cart[$id]['quantity'] ?? 0;
$new_quantity = $current_quantity + 1;

if ($new_quantity > $product['stock']) {
    echo json_encode(['success' => false, 'error' => 'Stock limit reached']);
    exit;
}

$product['quantity'] = $new_quantity;
$cart[$id] = $product;
$_SESSION['cart'] = $cart;

$totalItems = array_sum(array_column($cart, 'quantity'));

echo json_encode(['success' => true, 'totalItems' => $totalItems]);
