<?php

require_once "../db.php";
require_once "../Mail/mail.php";

$userType = $_GET["type"] ?? "user";

if($userType != "user" && $userType != "market"){
    header("Location: ../index.php");
    return;
}

$errors = [];

if(!empty($_POST)){
    extract($_POST); //fullName, emailAddress, password, city, district

    $requiredFields = ['fullName', 'emailAddress', 'password', 'city', 'district'];
    
    // Check for required fields
    foreach($requiredFields as $field) {
        if(empty($_POST[$field])) {
            $errors[$field] = ucfirst($field) . " is required";
        }
    }
    
    $fullName = filter_input(INPUT_POST, 'fullName', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $emailAddress = filter_input(INPUT_POST, 'emailAddress', FILTER_SANITIZE_EMAIL) ?? '';
    $password = $_POST['password'] ?? '';
    $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    $district = filter_input(INPUT_POST, 'district', FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
    

    //EMAIL

    if(!empty($emailAddress) && !filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
        $errors['emailAddress'] = "Please enter a valid email address";
    }
    
    if($userType == "user"){
        $a = $db->prepare("select count(*) email from consumer_user where email = ?");
        $a->execute([$emailAddress]);
        $count = $a->fetch(PDO::FETCH_ASSOC);
    }else if($userType == "market"){
        $a = $db->prepare("select count(*) email from market_user where email = ?");
        $a->execute([$emailAddress]);
        $count = $a->fetch(PDO::FETCH_ASSOC);
    }

    if($count["email"] > 0){
        var_dump($count["email"]);
        $errors['emailAddress'] = "There is already an user with that email address.";
    }

    //Password
    if(!empty($password)) {
        if(strlen($password) < 8) {
            $errors['password'] = "Password must be at least 8 characters long";
        } elseif (!preg_match('/[A-Z]/', $password)) {
            $errors['password'] = "Password must contain at least one uppercase letter";
        } elseif (!preg_match('/[a-z]/', $password)) {
            $errors['password'] = "Password must contain at least one lowercase letter";
        } elseif (!preg_match('/[0-9]/', $password)) {
            $errors['password'] = "Password must contain at least one number";
        }
    }

    if(empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        function generateVerificationCode() {
            return rand(10000, 99999); 
        }
        
        // Verification Data
        session_start();
        $verificationCode = generateVerificationCode();
        $_SESSION['verification_data'] = [
            'userType' => $userType,
            'fullName' => $fullName,
            'emailAddress' => $emailAddress,
            'password' => $hashedPassword,
            'city' => $city,
            'district' => $district,
            'code' => $verificationCode,
            'expires' => time() + 600 // code will be expired in 10 minutes
        ];
        

        $message = "Verification Code (SMarket)<br><br>5 Digit Code: " . $verificationCode;
        $verificationCode = Mail::send($emailAddress, "Verification Code (SMarket)", $message);
        

        header("Location: verify.php");
        exit;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMarket</title>
    <link rel="stylesheet" href="../css/register.css">
    
</head>
<body>

    <div class="container">

        <div class="header">
            <h1>SMarket</h1>
            <h4>Sustainable E-Commerce Website</h4>
        </div>

        <div class="content-area">
            <div class="register">
                <h1>Register as <?=ucwords($userType)?></h1>

                <form action="" method="post">
                    <div class="registerPart">
                        <p>
                            <span>Full name:</span>
                            <input type="text" name="fullName" value="<?= htmlspecialchars($fullName ?? '') ?>" autocomplete="off">
                            <?php if(isset($errors['fullName'])): ?>
                                <span class="error"><?= $errors['fullName'] ?></span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <span>Email address:</span>
                            <input type="text" name="emailAddress" value="<?=htmlspecialchars($emailAddress ?? '') ?>" autocomplete="off">
                            <?php if(isset($errors['emailAddress'])): ?>
                                <span class="error"><?= $errors['emailAddress'] ?></span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <span>Password:</span>
                            <input type="password" name="password" autocomplete="off">
                            <?php if(isset($errors['password'])): ?>
                                <span class="error"><?= $errors['password'] ?></span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <span>City:</span>
                            <input type="text" name="city" value="<?= htmlspecialchars($city ?? '') ?>" autocomplete="off">
                            <?php if(isset($errors['city'])): ?>
                                <span class="error"><?= $errors['city'] ?></span>
                            <?php endif; ?>
                        </p>
                        <p>
                            <span>District:</span>
                            <input type="text" name="district" value="<?= htmlspecialchars($district ?? '') ?>" autocomplete="off">
                            <?php if(isset($errors['district'])): ?>
                                <span class="error"><?= $errors['district'] ?></span>
                            <?php endif; ?>
                        </p>
                        <button type="submit">Register</button>
                    </div>
                </form>
                <div class="goBack">
                    <p><a href="../index.php">Go back</a></p>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>