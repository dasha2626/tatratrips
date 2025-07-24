<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$imie = trim($_POST['imie']);
$nazwisko = trim($_POST['nazwisko']);
$telefon = trim($_POST['telefon']);
$adres = trim($_POST['adres']);
$opis = trim($_POST['opis']);


if (empty($imie) || empty($nazwisko) || empty($telefon)) {
    header("Location: profil.php?error=puste_pola");
    exit();
}


if (!preg_match('/^[0-9+\-\s]{7,20}$/', $telefon)) {
    header("Location: profil.php?error=niepoprawny_telefon");
    exit();
}


$zdjecie_sql = "";
$zdjecie_nazwa = null;

if (!empty($_FILES['zdjecie']['name']) && $_FILES['zdjecie']['error'] === UPLOAD_ERR_OK) {
    $folder = 'uploads/';
    if (!is_dir($folder)) mkdir($folder);

    $ext = strtolower(pathinfo($_FILES['zdjecie']['name'], PATHINFO_EXTENSION));
    $dozwolone = ['jpg', 'jpeg', 'png', 'gif'];

    
    if (!in_array($ext, $dozwolone)) {
        header("Location: profil.php?error=niepoprawny_typ_pliku");
        exit();
    }

    if ($_FILES['zdjecie']['size'] > 2 * 1024 * 1024) {
        header("Location: profil.php?error=plik_za_duzy");
        exit();
    }

    
    $zdjecie_nazwa = uniqid('zdj_', true) . '.' . $ext;
    move_uploaded_file($_FILES['zdjecie']['tmp_name'], $folder . $zdjecie_nazwa);
    $zdjecie_sql = ", zdjecie = ?";
}


$query = "UPDATE $sqltables_klienci SET imie = ?, nazwisko = ?, telefon = ?, adres = ?, opis = ?" . $zdjecie_sql . " WHERE user_id = ?";

if ($zdjecie_sql) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssi", $imie, $nazwisko, $telefon, $adres, $opis, $zdjecie_nazwa, $user_id);
} else {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $imie, $nazwisko, $telefon, $adres, $opis, $user_id);
}

if ($stmt->execute()) {
    header("Location: profil.php?success=profil_zaktualizowany");
    exit();
} else {
    header("Location: profil.php?error=blad_zapisu");
    exit();
}
