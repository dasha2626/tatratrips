<?php
session_start();
require 'db.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (isset($_GET['success']) && $_GET['success'] === 'konto_usuniete') {
       
        exit;
    }

    header("Location: login.html?error=brak_danych");
    exit;
}

if (isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $haslo = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM $sqltables_users WHERE email = ? AND aktywny = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($haslo, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if (!empty($_POST['remember'])) {
                setcookie('email', $user['email'], time() + (86400 * 30), "/");
                setcookie('user_id', $user['id'], time() + (86400 * 30), "/");
            }

            header("Location: index.php");
            exit;
        } else {
            header("Location: login.html?error=zle_haslo");
            exit;
        }
    } else {
        header("Location: login.html?error=nie_istnieje");
        exit;
    }
} else {
    header("Location: login.html?error=brak_danych");
    exit;
}
