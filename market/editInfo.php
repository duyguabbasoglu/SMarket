<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'market') {
    header("Location: ../login/login.php");
    exit;
}

$id = $_SESSION['user_id'];
$message = "";

$query = $db->prepare("SELECT * FROM market_user WHERE id = ?");
$query->execute([$id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);

    if ($fullname && $city && $district) {
        $stmt = $db->prepare("UPDATE market_user SET fullName = ?, city = ?, district = ? WHERE id = ?");
        $stmt->execute([$fullname, $city, $district, $id]);

        $_SESSION['user_name'] = $fullname;
        $message = "✅ Your information has been updated.";

        $user['fullName'] = $fullname;
        $user['city'] = $city;
        $user['district'] = $district;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Info (Market)</title>
    <link rel="stylesheet" href="../css/market.css">
    <style>
body {
  background: linear-gradient(to right, #ffe4f0, #e0d0ff);
  font-family: 'Poppins', sans-serif;
  margin: 0;
  padding: 0;
}

.register {
  max-width: 500px;
  margin: 50px auto;
  background-color: #fff0fb;
  border-radius: 16px;
  padding: 30px;
  box-shadow: 0 6px 12px rgba(0,0,0,0.1);
  border: 2px solid #e9d5ff;
}

.register h1 {
  text-align: center;
  color: #7e22ce;
  margin-bottom: 25px;
}

.registerPart {
  margin-bottom: 20px;
}

.registerPart span {
  display: inline-block;
  width: 100px;
  font-weight: 600;
  color: #6b21a8;
}

.registerPart input[type="text"] {
  padding: 8px 12px;
  width: 60%;
  border: 1px solid #d8b4fe;
  border-radius: 8px;
  font-size: 14px;
}

.button-area {
  text-align: center;
  margin-top: 25px;
}

.button-area button {
  background-color: #d8b4fe;
  color: white;
  border: none;
  padding: 10px 25px;
  font-size: 15px;
  border-radius: 8px;
  cursor: pointer;
  transition: 0.3s;
}

.button-area button:hover {
  background-color: #a855f7;
}

.added-message {
  text-align: center;
  background-color: #dcfce7;
  color: #166534;
  padding: 12px;
  border-radius: 8px;
  margin-bottom: 20px;
  font-weight: bold;
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
<div class="register">
    <h1>Update Market Info</h1>

    <?php if ($message): ?>
        <p class="added-message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="registerPart">
            <p><span>Full Name:</span> 
            <input type="text" name="fullname" value="<?= htmlspecialchars($user['fullName']) ?>" required></p>
        </div>
        <div class="registerPart">
            <p><span>City:</span> 
            <input type="text" name="city" value="<?= htmlspecialchars($user['city']) ?>" required></p>
        </div>
        <div class="registerPart">
            <p><span>District:</span> 
            <input type="text" name="district" value="<?= htmlspecialchars($user['district']) ?>" required></p>
        </div>
        <div class="button-area">
            <button type="submit">Update</button>
        </div>

        <div class="go-back">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>
    </form>
</div>
</body>
</html>
