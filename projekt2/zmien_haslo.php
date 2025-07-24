<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: profil.php?error=brak_sesji");
    exit();
}

$user_id = $_SESSION['user_id'];

$stare_haslo = $_POST['stare_haslo'] ?? '';
$nowe_haslo = $_POST['nowe_haslo'] ?? '';
$powtorz_haslo = $_POST['powtorz_haslo'] ?? '';


if (empty($stare_haslo) || empty($nowe_haslo) || empty($powtorz_haslo)) {
    header("Location: profil.php?error=puste_pola");
    exit();
}


$stmt = $conn->prepare("SELECT password FROM $sqltables_users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !password_verify($stare_haslo, $user['password'])) {
    header("Location: profil.php?error=zle_stare_haslo");
    exit();
}


if ($nowe_haslo !== $powtorz_haslo) {
    header("Location: profil.php?error=hasla_nie_zgadzaja");
    exit();
}


if (strlen($nowe_haslo) < 6) {
    header("Location: profil.php?error=haslo_za_krotkie");
    exit();
}


$haslo_hash = password_hash($nowe_haslo, PASSWORD_DEFAULT);
$stmt = $conn->prepare("UPDATE $sqltables_users SET password = ? WHERE id = ?");
$stmt->bind_param("si", $haslo_hash, $user_id);
$stmt->execute();

header("Location: profil.php?success=haslo_zmienione");
exit();
?>
