<?php
require '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$wycieczka_id = intval($_POST['wycieczka_id']);
$imie = trim($_POST['imie']);
$nazwisko = trim($_POST['nazwisko']);
$telefon = trim($_POST['telefon']);


$stmt = $conn->prepare("SELECT dostepne_miejsca FROM $sqltables_wycieczki WHERE id = ?");
$stmt->bind_param("i", $wycieczka_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data || $data['dostepne_miejsca'] <= 0) {
    die("Brak dostępnych miejsc.");
}

$stmt = $conn->prepare("SELECT id FROM $sqltables_klienci WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $stmt = $conn->prepare("UPDATE $sqltables_klienci SET imie = ?, nazwisko = ?, telefon = ? WHERE user_id = ?");
    $stmt->bind_param("sssi", $imie, $nazwisko, $telefon, $user_id);
} else {
    $stmt = $conn->prepare("INSERT INTO $sqltables_klienci (user_id, imie, nazwisko, telefon) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $imie, $nazwisko, $telefon);
}
$stmt->execute();


$stmt = $conn->prepare("INSERT INTO $sqltables_rezerwacje (id_uzytkownika, id_wycieczki) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $wycieczka_id);
$stmt->execute();


$conn->query("UPDATE $sqltables_wycieczki SET dostepne_miejsca = dostepne_miejsca - 1 WHERE id = $wycieczka_id");

$_SESSION['rezerwacja_udana'] = true;
header("Location: ../index.php");
exit;
