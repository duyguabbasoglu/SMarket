<?php

require_once "../db.php";
session_start();

$error = "";

if(!empty($_POST)){
    extract($_POST);
    
    $consumerStmt = $db->prepare("select * from consumer_user where email = ?");
    $marketStmt = $db->prepare("select * from market_user where email = ?");
    $consumerStmt->execute([$emailAddress]);
    $marketStmt->execute([$emailAddress]);

    $consumerExists = $consumerStmt->rowCount() > 0;
    $marketExists = $marketStmt->rowCount() > 0;

    if($consumerExists){
        $userSameMail = $consumerStmt->fetch(PDO::FETCH_ASSOC);
        $userType = 'consumer';
    }else if($marketExists){
        $userSameMail = $marketStmt->fetch(PDO::FETCH_ASSOC);
        $userType = 'market';
    }else{
        // No user with that email exists
        $error = "Invalid email address or password";
        $_SESSION['login_error'] = $error;
        header("Location: ../index.php");
        exit;
    }
    
    // Check password
    if(password_verify($password, $userSameMail["pass"])){
        // Success - Set up user session
        $_SESSION['user_id'] = $userSameMail['id'];
        $_SESSION['email'] = $userSameMail['email'];
        $_SESSION['user_type'] = $userType;
        
        // Redirect to appropriate dashboard
        if($userType == 'consumer') {
            header("Location: ../user/dashboard.php");
        } else {
            header("Location: ../market/dashboard.php");
        }
        exit;
    } else {
        // Wrong password
        $error = "Invalid email address or password";
        $_SESSION['login_error'] = $error;
        header("Location: ../index.php");
        exit;
    }
}
?>