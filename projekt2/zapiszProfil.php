<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$imie = $_POST['imie'];
$nazwisko = $_POST['nazwisko'];
$telefon = $_POST['telefon'];
$adres = $_POST['adres'];
$opis = $_POST['opis'];

$zdjecie_nazwa = null;


if (!empty($_FILES['zdjecie']['name']) && $_FILES['zdjecie']['error'] === UPLOAD_ERR_OK) {
    $folder = 'uploads/';
    if (!is_dir($folder)) mkdir($folder);

    $ext = pathinfo($_FILES['zdjecie']['name'], PATHINFO_EXTENSION);
    $zdjecie_nazwa = uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['zdjecie']['tmp_name'], $folder . $zdjecie_nazwa);
}


$stmt = $conn->prepare("INSERT INTO $sqltables_klienci (user_id, imie, nazwisko, telefon, adres, opis, zdjecie)
                        VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssss", $user_id, $imie, $nazwisko, $telefon, $adres, $opis, $zdjecie_nazwa);
$stmt->execute();

header("Location: profil.php");
exit();
