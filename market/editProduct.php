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

$stmt = $db->prepare("SELECT * FROM productlist WHERE id = ? AND market_id = ?");
$stmt->execute([$product_id, $market_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    echo "Product not found or unauthorized.";
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $price = floatval($_POST['price']);
    $discount_price = floatval($_POST['discount_price']);
    $stock = intval($_POST['stock']);
    $expiration_date = $_POST['expiration_date'];

    if (!$title || $price <= 0 || $discount_price <= 0 || $stock <= 0 || !$expiration_date) {
        $errors[] = "Please fill all fields correctly.";
    } else {
        $image = $product['image'];
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $target_dir = "../assets/";
            $filename = basename($_FILES["image"]["name"]);
            $target_file = $target_dir . $filename;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image = $filename;
            } else {
                $errors[] = "Image upload failed.";
            }
        }

        if (empty($errors)) {
            $stmt = $db->prepare("UPDATE productlist 
                SET title = ?, price = ?, discount_price = ?, stock = ?, expiration_date = ?, image = ? 
                WHERE id = ? AND market_id = ?");
            $stmt->execute([$title, $price, $discount_price, $stock, $expiration_date, $image, $product_id, $market_id]);
            $success = true;

            $stmt = $db->prepare("SELECT * FROM productlist WHERE id = ? AND market_id = ?");
            $stmt->execute([$product_id, $market_id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link rel="stylesheet" href="../css/market.css">
    <style>
body {
  background: linear-gradient(to right, #ffe4f0, #e0d0ff);
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
}

.form-container {
  max-width: 500px;
  margin: 50px auto;
  background-color: #fff0fb;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
  border: 2px solid #e9d5ff;
}

.form-container h2 {
  text-align: center;
  color: #7e22ce;
  margin-bottom: 25px;
}

label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #6b21a8;
}

input[type="text"],
input[type="number"],
input[type="date"],
input[type="file"] {
  width: 100%;
  padding: 8px 12px;
  margin-bottom: 18px;
  border: 1px solid #d8b4fe;
  border-radius: 8px;
  font-size: 14px;
  background-color: #fef6ff;
}

button[type="submit"] {
  width: 100%;
  background-color: #d8b4fe;
  color: white;
  padding: 10px;
  font-size: 16px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}

button[type="submit"]:hover {
  background-color: #a855f7;
}

.message {
  background-color: #dcfce7;
  color: #166534;
  padding: 12px;
  border-radius: 8px;
  text-align: center;
  margin-bottom: 20px;
  font-weight: bold;
}

.error {
  background-color: #fee2e2;
  color: #b91c1c;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 10px;
  font-weight: 500;
}

.go-back {
  text-align: center;
  margin-top: 15px;
}

.go-back a {
  text-decoration: none;
  color: #7e22ce;
  font-weight: 600;
}
</style>

</head>
<body>

<div class="form-container">
    <h2>Edit Product</h2>

    <?php if ($success): ?>
        <div class="message">✅ Product updated successfully!</div>
    <?php endif; ?>

    <?php foreach ($errors as $error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Product Title:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>

        <label>Price (TL):</label>
        <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" required>

        <label>Discount Price (TL):</label>
        <input type="number" name="discount_price" value="<?= $product['discount_price'] ?>" step="0.01" required>

        <label>Stock:</label>
        <input type="number" name="stock" value="<?= $product['stock'] ?>" required>

        <label>Expiration Date:</label>
        <input type="date" name="expiration_date" value="<?= $product['expiration_date'] ?>" required>

        <label>Change Image (optional):</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit">✏️ Update Product</button>
    </form>

    <div class="go-back">
        <a href="dashboard.php">← Back to Dashboard</a>
    </div>
</div>

</body>
</html>
