<?php

require '../db.php';
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_POST['id'], $_POST['quantity']) || !is_numeric($_POST['id']) || !is_numeric($_POST['quantity'])) {
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$id = intval($_POST['id']);
$quantity = max(1, intval($_POST['quantity']));

$stmt = $db->prepare("SELECT * FROM productlist WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product || $quantity > $product['stock']) {
    echo json_encode(['error' => 'Stock limit exceeded']);
    exit;
}

if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]['quantity'] = $quantity;
}

function calculateDiscountPrice($price, $discount_price, $expiration_date) {
    $today = strtotime(date('Y-m-d'));
    $exp = strtotime($expiration_date);
    $days_left = ($exp - $today) / (60 * 60 * 24);

    if ($days_left <= 3) return round($price * 0.5, 2);
    elseif ($days_left <= 7) return round($price * 0.8, 2);
    else return $discount_price;
}

function calculateTotal($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $discounted = calculateDiscountPrice($item['price'], $item['discount_price'], $item['expiration_date']);
        $total += $discounted * $item['quantity'];
    }
    return number_format($total, 2);
}

$itemTotal = number_format(calculateDiscountPrice($product['price'], $product['discount_price'], $product['expiration_date']) * $quantity, 2);
$grandTotal = calculateTotal($_SESSION['cart']);

echo json_encode([
    'success' => true,
    'itemTotal' => $itemTotal,
    'grandTotal' => $grandTotal
]);
