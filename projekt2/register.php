<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'], $_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        header("Location: login.html?error=brak_danych");
        exit();
    }

    if (strlen($password) < 6) {
        header("Location: login.html?error=haslo_za_krotkie");
        exit();
    }

    if (!preg_match('/[a-z]/', $password)) {
        header("Location: login.html?error=brak_malej_litery");
        exit();
    }

    if (!preg_match('/[0-9]/', $password)) {
        header("Location: login.html?error=brak_cyfry");
        exit();
    }

    $check = $conn->prepare("SELECT id FROM $sqltables_users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: login.html?error=uzytkownik_istnieje");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'user';


    $stmt = $conn->prepare("INSERT INTO $sqltables_users (email, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $password_hash, $role);

    if ($stmt->execute()) {
        header("Location: login.html?success=rejestracja_ok");
        exit();
    } else {
        header("Location: login.html?error=blad_rejestracji");
        exit();
    }
} else {
    header("Location: login.html?error=nieprawidlowe_dane");
    exit();
}
?>
