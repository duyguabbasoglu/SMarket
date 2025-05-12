<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'consumer') {
    header("Location: ../login/login.php");
    exit;
}

$id = $_SESSION['user_id'];
$message = "";
$query = $db->prepare("SELECT * FROM consumer_user WHERE id = ?");
$query->execute([$id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $city = trim($_POST['city']);
    $district = trim($_POST['district']);

    if ($fullname && $city && $district) {
        $stmt = $db->prepare("UPDATE consumer_user SET fullName = ?, city = ?, district = ? WHERE id = ?");
        $stmt->execute([$fullname, $city, $district, $id]);
        $_SESSION['user_name'] = $fullname;
        $message = "Your information is updated!";
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
    <title>Edit Info</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/user.css">
    <style>
        body {
            background-color: beige;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .register h1 {
            font-size: 28px; color: white; margin-bottom: 30px;
        }
        .registerPart p {
            color: white;
            font-size: 18px;
        }
        .added-message {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            color:lightcyan;
            margin-top: 20px;
        }
        .button-area {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        .go-back {
            text-align: center;
            margin-top: 30px;
            font-size: 18px;
        }

        .go-back a {
            text-decoration: none;
            color: lightblue;
            font-weight: bold;
        }

        .go-back a:hover {
            color:darkblue
        }
    </style>
</head>
<body>
<div class="register">
    <h1>Update Your Info</h1>
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
        <br><div class="go-back">
            <a href="dashboard.php">← Back to Main Page</a>
        </div>

    </form>
</div>
</body>
</html>
