<?php
require '../db.php';
session_start();

$email = trim($_POST['email']);
$temat = trim($_POST['temat']);
$tresc = trim($_POST['tresc']);
$user_id = !empty($_POST['user_id']) ? intval($_POST['user_id']) : null;

if (!$email || !$tresc) {
    die("Brakuje wymaganych danych.");
}

$stmt = $conn->prepare("INSERT INTO $sqltables_zgloszenia (user_id, email, temat, tresc) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $email, $temat, $tresc);
$stmt->execute();


$_SESSION['zgloszenie_wyslane'] = true;


header("Location: ../index.php");
exit;
