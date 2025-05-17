<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'market') {
    header("Location: ../login/login.php");
    exit;
}

$market_id = $_SESSION['user_id'];
$product_id = $_GET['id'] ?? null;

if (!$product_id || !is_numeric($product_id)) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $db->prepare("DELETE FROM productlist WHERE id = ? AND market_id = ?");
$stmt->execute([$product_id, $market_id]);

header("Location: dashboard.php");
exit;
