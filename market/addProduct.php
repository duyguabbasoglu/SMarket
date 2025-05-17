<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'market') {
    header("Location: ../login/login.php");
    exit;
}

$market_id = $_SESSION['user_id'];
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $price = floatval($_POST['price']);
    $discount_price = floatval($_POST['discount_price']);
    $stock = intval($_POST['stock']);
    $expiration_date = $_POST['expiration_date'];
    $image = $_FILES['image'] ?? null;

    $stmt = $db->prepare("SELECT city, district FROM market_user WHERE id = ?");
    $stmt->execute([$market_id]);
    $market = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$title || $price <= 0 || $discount_price <= 0 || $stock <= 0 || !$expiration_date || !$image) {
        $errors[] = "Please fill all fields correctly.";
    } else {
        $target_dir = "../assets/";
        $filename = basename($image["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $stmt = $db->prepare("INSERT INTO productlist (market_id, title, price, discount_price, stock, expiration_date, image, city, district) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$market_id, $title, $price, $discount_price, $stock, $expiration_date, $filename, $market['city'], $market['district']]);
            $success = true;
        } else {
            $errors[] = "Image upload failed.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
    <link rel="stylesheet" href="../css/market.css">
    <style>
body {
  background: linear-gradient(to right, #ffe0f0, #e0ccff);
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
}

h2 {
  text-align: center;
  color: #6b21a8;
  font-size: 28px;
  margin-bottom: 20px;
}

.form-container {
  max-width: 500px;
  margin: 50px auto;
  background: white;
  padding: 30px 40px;
  border-radius: 15px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
  border: 3px solid #d8b4fe;
}

form label {
  display: block;
  margin-top: 15px;
  margin-bottom: 5px;
  color: #4c1d95;
  font-weight: 500;
}

form input[type="text"],
form input[type="number"],
form input[type="date"],
form input[type="file"] {
  width: 100%;
  padding: 10px;
  border: 2px solid #e9d5ff;
  border-radius: 8px;
  font-size: 14px;
  box-sizing: border-box;
  background-color: #faf5ff;
}

form button[type="submit"] {
  margin-top: 20px;
  width: 100%;
  padding: 12px;
  background-color: #c084fc;
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

form button[type="submit"]:hover {
  background-color: #9333ea;
}

.go-back {
  text-align: center;
  margin-top: 20px;
}

.go-back a {
  color: #9333ea;
  text-decoration: none;
  font-weight: bold;
}

.go-back a:hover {
  text-decoration: underline;
}

.message {
  background-color: #d1fae5;
  border: 2px solid #10b981;
  color: #065f46;
  padding: 10px 15px;
  border-radius: 10px;
  font-weight: bold;
  margin-bottom: 15px;
  text-align: center;
}

.error {
  background-color: #fee2e2;
  border: 2px solid #dc2626;
  color: #7f1d1d;
  padding: 10px 15px;
  border-radius: 10px;
  font-weight: bold;
  margin-bottom: 10px;
  text-align: center;
}
</style>

</head>
<body>

<div class="form-container">
    <h2>Add New Product</h2>

    <?php if ($success): ?>
        <div class="message">✅ Product added successfully!</div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Title:</label>
        <input type="text" name="title" required>

        <label>Price (TL):</label>
        <input type="number" name="price" step="0.01" required>

        <label>Discount Price (TL):</label>
        <input type="number" name="discount_price" step="0.01" required>

        <label>Stock:</label>
        <input type="number" name="stock" required>

        <label>Expiration Date:</label>
        <input type="date" name="expiration_date" required>

        <label>Product Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit">➕ Add Product</button>
    </form>

    <div class="go-back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
