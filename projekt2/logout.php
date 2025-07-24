<?php
session_start();


$_SESSION = [];


session_destroy();


if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/");
}
if (isset($_COOKIE['email'])) {
    setcookie('email', '', time() - 3600, "/");
}


header("Location: login.html");
exit;
?>
