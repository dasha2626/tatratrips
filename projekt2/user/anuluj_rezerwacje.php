<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_rezerwacji'])) {
    $id_rezerwacji = intval($_POST['id_rezerwacji']);
    $user_id = $_SESSION['user_id'];

  
    $stmt = $conn->prepare("SELECT id_wycieczki FROM $sqltables_rezerwacje WHERE id = ? AND id_uzytkownika = ? AND status != 'anulowana'");
    $stmt->bind_param("ii", $id_rezerwacji, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $id_wycieczki = $row['id_wycieczki'];

    
        $stmt = $conn->prepare("UPDATE $sqltables_rezerwacje SET status = 'anulowana' WHERE id = ?");
        $stmt->bind_param("i", $id_rezerwacji);
        $stmt->execute();

        $stmt = $conn->prepare("UPDATE $sqltables_wycieczki SET dostepne_miejsca = dostepne_miejsca + 1 WHERE id = ?");
        $stmt->bind_param("i", $id_wycieczki);
        $stmt->execute();
    }
}

header("Location: ../mojerezerwacje.php");
exit();
