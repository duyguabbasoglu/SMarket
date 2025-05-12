<?php

require_once "./db.php";

?>

<?php
session_start();
$loginError = $_SESSION['login_error'] ?? '';

if(isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMarket</title>
    <link rel="stylesheet" href="./css/index.css">
</head>
<body>

    <div class="container">

        <div class="header">
            <h1>SMarket</h1>
            <h4>Sustainable E-Commerce Website</h4>
        </div>

        <div class="content-area">
            <div class="login">
                <h1>Login</h1>
                <form action="./login/login.php" method="post">
                    <div class="loginPart">
                        <?php if(!empty($loginError)): ?>
                            <p><?php echo $loginError; ?></p>
                        <?php endif; ?>
                        <p><span>Email address:</span><input type="text" name="emailAddress" autocomplete="off"></p>
                        <p><span>Password:</span><input type="password" name="password" autocomplete="off"></p>
                        <button type="submit">Login</button>
                    </div>
                </form>
                <div class="registerPart">
                    <h4>Don't you have an account?</h4>
                    <p>Register as a <a href="./register/register.php?type=user">User</a> or <a href="./register/register.php?type=market">Market</a></p>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>