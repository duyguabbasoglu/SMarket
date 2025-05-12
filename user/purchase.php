<?php
    session_start();
    require '../db.php';
    header('Content-Type: application/json');

    if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
        echo json_encode(['error' => 'unauthorized']);
        exit;
    }

    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo json_encode(['error' => 'empty cart']);
        exit;
    }

    $cart = $_SESSION['cart'];

    foreach ($cart as $productId => $item) {
        $quantity = $item['quantity'];

        $stmt = $db->prepare("SELECT stock FROM productlist WHERE id = ?");
        $stmt->execute([$productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product || $product['stock'] < $quantity)
            continue;

        $newStock = $product['stock'] - $quantity;
        $update = $db->prepare("UPDATE productlist SET stock = ? WHERE id = ?");
        $update->execute([$newStock, $productId]);
    }

    unset($_SESSION['cart']);
    echo json_encode(['success' => true, 'message' => 'Purchase completed and stock updated']);
?>