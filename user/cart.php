<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: ../login/login.php");
    exit;
}

$cart = $_SESSION['cart'] ?? [];

function calculateTotal($cart) {
    $total = 0;
    foreach ($cart as $item) {
        $total += calculateDiscountPrice($item['price'], $item['discount_price'], $item['expiration_date']) * $item['quantity'];
    }
    return number_format($total, 2);
}

function calculateDiscountPrice($price, $discount_price, $expiration_date) {
    $today = strtotime(date('Y-m-d'));
    $exp = strtotime($expiration_date);
    $days_left = ($exp - $today) / (60 * 60 * 24);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cart</title>
    <link rel="stylesheet" href="../css/user.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
    <div class="left-title">Cart</div>

    <div class="search-form">
        <form method="GET" action="dashboard.php">
            <input type="text" name="q" placeholder="Search products..." required>
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="right-user">
        <i class="fa-solid fa-user"></i> 
        <a href="edit.php" class="profile-link">
            <?= htmlspecialchars($_SESSION['user_name'] ?? $_SESSION['email']) ?>
        </a>
        <span id="uwu">|</span>
        <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> Cart (<?= array_sum(array_column($cart, 'quantity')) ?>)</a>
    </div>
</div>

<div class="cart-container">
    <h2>🛒 Your Purchase Summary</h2>
    <?php if (empty($cart)): ?>
        <p id="add-warning">Please add items to your cart.</p>
    <?php else: ?>
        <?php foreach ($cart as $id => $item): 
            $discounted = calculateDiscountPrice($item['price'], $item['discount_price'], $item['expiration_date']);
        ?>
            <div class="cart-item" data-id="<?= $id ?>">
                <img src="../assets/<?= htmlspecialchars($item['image']) ?>" alt="Product">
                <div class="cart-item-details">
                    <h4><?= htmlspecialchars($item['title']) ?></h4>
                    <p>Price: <?= $discounted ?> TL</p>
                    <p>Amount: 
                        <input type="number" class="quantity-input" value="<?= htmlspecialchars($item['quantity']) ?>" min="1" onchange="updateQuantity(<?= $id ?>, this.value)">
                    </p>
                    <p class="item-total">Total: <?= number_format($discounted * $item['quantity'], 2) ?> TL</p>
                </div>
                <a href="remove.php?id=<?= htmlspecialchars($id) ?>" class="remove-btn"><i class="fa-solid fa-trash"></i> Remove</a>
            </div>
        <?php endforeach; ?>
        <div class="total-section">
            Total: <span id="grand-total"><?= calculateTotal($cart) ?> TL</span><br>
            <div class="purchase-area">
                <button onclick="purchaseCart()">Complete Purchase 🧾</button>
            </div>
            <div id="purchase-message" class="message-box" style="display:none;"></div>
        </div>
    <?php endif; ?>
</div>

<div class="go-back">
    <a href="dashboard.php">← Back to Main Page</a>
</div>

<script>
function updateQuantity(id, quantity) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "cartUpdate.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if (this.status === 200) {
            const res = JSON.parse(this.responseText);
            document.querySelector(`[data-id='${id}'] .item-total`).innerText = "Total: " + res.itemTotal + " TL";
            document.getElementById("grand-total").innerText = res.grandTotal + " TL";
        }
    };
    xhr.send("id=" + id + "&quantity=" + quantity);
}

function purchaseCart() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "purchase.php", true);
    xhr.onload = function() {
        if (this.status === 200) {
            const res = JSON.parse(this.responseText);
            const msgBox = document.getElementById("purchase-message");
            msgBox.style.display = "block";
            msgBox.innerText = res.message;

            setTimeout(() => location.reload(), 2000);
        }
    };
    xhr.send();
}
</script>
</body>
</html>