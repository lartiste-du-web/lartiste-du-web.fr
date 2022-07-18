<?php
    
    $dbhost = "";
    $dbname = "";
    $dbuser = "";
    $dbpassword = "";
    
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpassword);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
?>