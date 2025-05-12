<?php


// dbname = test : değiştirin.
try {
       $db = new PDO("mysql:host=localhost;dbname=test;charset=utf8mb4", "std", " ");
       $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION) ;
    } 
catch( PDOException $ex) {
        $errorMsg = "Cannot connect to database.";
        header("Location: ./error/error.php?error=$errorMsg");
}

// owner kismi bittikten sonra productlist.sql deki rowlarin silinip 
//yerine yenilerinin konmasi gerekiyor ben onlari deneme amacli koydum