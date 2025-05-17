<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'market') {
    header("Location: ../login/login.php");
    exit;
}

$market_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM productlist WHERE market_id = ?");
$stmt->execute([$market_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function isExpired($date) {
    return strtotime($date) < strtotime(date('Y-m-d'));
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Market Dashboard</title>
    <link rel="stylesheet" href="../css/market.css">
</head>
<body>

<div class="header">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['email']) ?> </h1>
    <a href="addProduct.php">➕ Add Product</a> |
    <a href="editInfo.php">⚙️ Edit Info</a> |
    <a href="../logout.php">🚪 Logout</a>
</div>

<div class="container">
    <h2>Your Products</h2>

    <?php foreach ($products as $product): ?>
        <div class="card <?= isExpired($product['expiration_date']) ? 'expired' : '' ?>">
            <img src="../assets/<?= htmlspecialchars($product['image']) ?>" alt="Product">
            <div class="card-body">
                <h3><?= htmlspecialchars($product['title']) ?></h3>
                <p><del><?= $product['price'] ?> TL</del> <strong><?= $product['discount_price'] ?> TL</strong></p>
                <p>Stock: <?= $product['stock'] ?></p>
                <p>Expires: <?= $product['expiration_date'] ?> <?= isExpired($product['expiration_date']) ? '⚠️' : '' ?></p>
            </div>
            <div class="card-footer">
                <a href="editProduct.php?id=<?= $product['id'] ?>">✏️ Edit</a> |
                <a href="deleteProduct.php?id=<?= $product['id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')">🗑 Delete</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
