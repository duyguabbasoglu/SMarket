<?php
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
$quantity = max(1, intval($_POST['quantity'])); // minimum 1

if (!isset($_SESSION['cart'][$id])) {
    echo json_encode(['error' => 'Product not in cart']);
    exit;
}

$_SESSION['cart'][$id]['quantity'] = $quantity;

$itemTotal = number_format($_SESSION['cart'][$id]['price'] * $quantity, 2);

function calculateTotal($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return number_format($total, 2);
}
$grandTotal = calculateTotal($_SESSION['cart']);

echo json_encode([
    'success' => true,
    'itemTotal' => $itemTotal,
    'grandTotal' => $grandTotal
]);
?>
