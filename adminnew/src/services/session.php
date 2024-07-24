<?php
// Initialize the session
// require_once('services/database.php');
require_once('services/config.php');

session_start();

$page = basename($_SERVER['PHP_SELF']);
if (!in_array(strtolower($page), array("auth-login.php", "auth-forgotpw.php", "auth-logout.php")) && !strlen(trim($_SESSION['userId']))) {
	header("Location:auth-login");
}
// // function checkAuth($email)
// // {
// //     if (DatabaseService::getInstance()->checkUser($email)) {
// //         setSesstion();
// //         return true;
// //     } else {
// //         return "Invalid Details";
// //     }
// // }

// function setSesstion()
// {
//     $_SESSION["user"] = true;
// }

// function logoutSesstion()
// {
//     $_SESSION["user"] = false;
// }

// if (!isset($_SESSION["user"]) || $_SESSION["user"] !== true) {
//     header("location:auth-login.php");
//     exit;
// }
