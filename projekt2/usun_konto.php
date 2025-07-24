<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$potwierdz_haslo = $_POST['potwierdz_haslo'] ?? '';

if (empty($potwierdz_haslo)) {
    header("Location: profil.php?error=puste_haslo");
    exit();
}


$stmt = $conn->prepare("SELECT password FROM $sqltables_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($potwierdz_haslo, $user['password'])) {
    header("Location: profil.php?error=bledne_haslo_usuniecie");
    exit();
}


$stmt = $conn->prepare("DELETE FROM $sqltables_wiadomosci_systemowe WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();


$stmt = $conn->prepare("DELETE FROM $sqltables_rezerwacje WHERE id_uzytkownika = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();


$stmt = $conn->prepare("DELETE FROM $sqltables_klienci WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();


$stmt = $conn->prepare("DELETE FROM $sqltables_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();





session_unset();
session_destroy();
header("Location: login.html?success=konto_usuniete");
exit;

exit();
