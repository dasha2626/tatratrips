<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$tresc = trim($_POST['tresc']);
$id_wycieczki = intval($_POST['id_wycieczki']);
$data = date('Y-m-d H:i:s'); 
$zatwierdzony = 0;

if ($tresc && $id_wycieczki) {

   
    $stmt_check = $conn->prepare("SELECT id FROM $sqltables_komentarze WHERE id_uzytkownika = ? AND id_wycieczki = ? AND tresc = ? AND data_dodania = ?");
    $stmt_check->bind_param("iiss", $user_id, $id_wycieczki, $tresc, $data);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows === 0) {
        $stmt = $conn->prepare("INSERT INTO $sqltables_komentarze (id_uzytkownika, id_wycieczki, tresc, data_dodania, zatwierdzony) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $user_id, $id_wycieczki, $tresc, $data, $zatwierdzony);
        $stmt->execute();
        $_SESSION['komentarz_dodany'] = true;
    }
}

header("Location: ../mojerezerwacje.php");
exit();
