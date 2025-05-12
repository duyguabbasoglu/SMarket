<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: ../login/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$keyword = $_GET['q'] ?? '';

$stmt = $db->prepare("SELECT city, district FROM consumer_user WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$city = $user['city'];
$district = $user['district'];

if ($keyword) {
    $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $limit = 4;
    $start = ($page - 1) * $limit;

    $stmt = $db->prepare("
        SELECT * FROM productlist
        WHERE expiration_date >= CURDATE()
          AND city = ?
          AND title LIKE ?
        ORDER BY (district = ?) DESC
        LIMIT $start, $limit
    ");
    $stmt->execute([$city, "%$keyword%", $district]);

    $count_stmt = $db->prepare("
        SELECT COUNT(*) FROM productlist
        WHERE expiration_date >= CURDATE()
          AND city = ?
          AND title LIKE ?
    ");
    $count_stmt->execute([$city, "%$keyword%"]);

    $total_rows = $count_stmt->fetchColumn();
    $total_pages = ceil($total_rows / $limit);
} else {
    $stmt = $db->prepare("
        SELECT * FROM productlist
        WHERE expiration_date >= CURDATE()
          AND city = ?
        ORDER BY (district = ?) DESC
    ");
    $stmt->execute([$city, $district]);
    $total_pages = 0;
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

function isExpired($date) {
    return strtotime($date) < strtotime(date('Y-m-d'));
}

function calculateDiscountPrice($price, $discount_price, $expiration_date) {
    $today = strtotime(date('Y-m-d'));
    $exp = strtotime($expiration_date);
    $days_left = ($exp - $today) / (60*60*24);

    if ($days_left <= 3) {
        return round($price * 0.5, 2);
    } elseif ($days_left <= 7) {
        return round($price * 0.8, 2);
    } else {
        return $discount_price;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../css/user.css">
    <style>
        .quantity-input {
            width: 60px;
            padding: 4px;
            font-size: 14px;
        }
        .purchase-area {
            text-align: center;
            margin-top: 20px;
        }
        .purchase-area button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        .purchase-area button:hover {
            background-color: #218838;
        }
        .message-box {
            margin-top: 20px;
            padding: 12px 20px;
            border-radius: 8px;
            background-color: #d1e7dd;
            color: #0f5132;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            width: 80%;
            margin-left: auto;
            margin-right: auto;
        }
        .search-form {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .search-form input[type="text"] {
            padding: 6px;
            width: 250px;
            border-radius: 5px;
            border: 1px solid #aaa;
        }

        .search-form button {
            background-color: darkslategrey;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
<div class="header">
    <div class="left-title">SMarket</div>

    <div class="search-form">
    <form method="GET">
        <input type="text" name="q" placeholder="Search products..." value="<?= htmlspecialchars($keyword) ?>" required>
        <button type="submit">Search</button>
    </form>
    </div>

    <div class="right-user">
        <i class="fa-solid fa-user"></i> 
        <a href="edit.php" class="profile-link"><?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['email']) ?></a>
        <span id="uwu">|</span>
        <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart (<?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0 ?>)</a>
    </div>
</div>

<div class="container">
<?php foreach ($products as $product):
    if (isExpired($product['expiration_date'])) continue;
    $discount = calculateDiscountPrice($product['price'], $product['discount_price'], $product['expiration_date']);
?>
    <div class="card <?= isExpired($product['expiration_date']) ? 'expired' : '' ?>">
        <img src="../assets/<?= htmlspecialchars($product['image']) ?>" alt="Product">
        <div class="card-body">
            <h3><i class="fa-solid fa-basket-shopping"></i> <?= htmlspecialchars($product['title']) ?></h3>
            <p><del><?= htmlspecialchars($product['price']) ?> TL</del> <span><?= $discount ?> TL</span></p>
            <p><i class="fa-solid fa-box"></i> <?= htmlspecialchars($product['stock']) ?></p>
            <p>&#9201; Expires: <?= htmlspecialchars($product['expiration_date']) ?></p>
        </div>
        <div class="card-footer">
            <a href="cartUpdate.php?id=<?= $product['id'] ?>">&#128722; Add</a>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php if ($keyword && $total_pages > 1): ?>
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?q=<?= urlencode($keyword) ?>&page=<?= $page - 1 ?>"><img src="../assets/prev.png"></a>
    <?php endif; ?>
    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <a href="?q=<?= urlencode($keyword) ?>&page=<?= $i ?>" class="<?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
    <?php if ($page < $total_pages): ?>
        <a href="?q=<?= urlencode($keyword) ?>&page=<?= $page + 1 ?>"><img src="../assets/next.png"></a>
    <?php endif; ?>
</div>
<?php endif; ?>
</body>
</html>