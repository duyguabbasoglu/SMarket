<?php
require_once "../db.php";
require_once "../Mail/mail.php"; 
session_start();

if(!isset($_SESSION['verification_data'])) {
    header("Location: ../index.php");
    exit;
}

$data = $_SESSION['verification_data'];
$errors = [];
$success = false;
$resend = $_GET["resend"] ?? 0;



// Check if verification code has expired
if(time() > $data['expires']) {
    $errors['expired'] = "Verification code has expired. Please register again.";
    unset($_SESSION['verification_data']);
}

if(!empty($_POST)) {
    $enteredCode = $_POST['verification_code'] ?? '';
    
    if(empty($enteredCode)) {
        $errors['code'] = "Please enter the verification code";
    } elseif($enteredCode != $data['code']) {
        $errors['code'] = "Invalid verification code";
    } else {
        
        if($data['userType'] == "user") {
            $stmt = $db->prepare("insert into consumer_user (email, fullName, pass, city, district) values (?, ?, ?, ?, ?)");
            $stmt->execute([$data['emailAddress'], $data['fullName'], $data['password'], $data['city'], $data['district']]);
        } else {
            $stmt = $db->prepare("insert into market_user (email, fullName, pass, city, district) values (?, ?, ?, ?, ?)");
            $stmt->execute([$data['emailAddress'], $data['fullName'], $data['password'], $data['city'], $data['district']]);
        }
            
        unset($_SESSION['verification_data']);
            
        $success = true;
            
        header("refresh:2; url=../index.php");
    }

    $resend=0;
}

// For resending code
if($resend == 1) {
    
    $newCode = rand(10000, 99999);
    $data['code'] = $newCode;
    $data['expires'] = time() + 600;
    $_SESSION['verification_data'] = $data;
    
    $message = "Verification Code (SMarket)<br><br>5 Digit Code: " . $newCode;
    Mail::send($data['emailAddress'], "Verification Code (SMarket)", $message);
    
    $resendMessage = "Verification code has been resent to " . $data['emailAddress'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - SMarket</title>
    <link rel="stylesheet" href="../css/register.css">
    <link rel="stylesheet" href="../css/verify.css">
</head>
<body>

    <div class="container">
        <div class="header">
            <h1>Welcome to SMarket</h1>
            <h4>Sustainable E-Commerce Website</h4>
        </div>

        <div class="content-area">
            <div class="verification">
                <h1>Email Verification</h1>
                
                <?php if($success): ?>
                    <div class="success-message">
                        <h3>Registration Successful!</h3>
                        <p>Your account has been created successfully.</p>
                        <p>Redirecting to login page...</p>
                    </div>
                <?php else: ?>
                    <p>We've sent a verification code to <strong><?= $data['emailAddress'] ?></strong></p>
                    
                    <?php if(isset($resendMessage)): ?>
                        <div class="success-message"><?= $resendMessage ?></div>
                    <?php endif; ?>
                    
                    <?php if(isset($errors['expired'])): ?>
                        <div class="error-message"><?= $errors['expired'] ?></div>
                    <?php else: ?>
                        <form action="" method="post">
                            <div>
                                <label for="verification_code">Enter 5-digit code:</label>
                                <input type="text" name="verification_code" id="verification_code" maxlength="5" autocomplete="off">
                            </div>
                            
                            <?php if(isset($errors['code'])): ?>
                                <div class="error-message"><?= $errors['code'] ?></div>
                            <?php endif; ?>
                            
                            <?php if(isset($errors['db'])): ?>
                                <div class="error-message"><?= $errors['db'] ?></div>
                            <?php endif; ?>
                            
                            <button type="submit">Verify</button>
                        </form>
                        
                        <p>
                            <a href="?resend=1" class="resend-link">Resend verification code</a>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
                
            
            </div>
        </div>
    </div>
    
</body>
</html>