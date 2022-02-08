<?php

session_start(); 
unset($_SESSION["user"]); 
unset($_SESSION["id"]); 
header("Location: /assing/login.php");
?>