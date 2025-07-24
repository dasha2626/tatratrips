<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? null;
$profil_zdjecie = '../user.png';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['zatwierdz']) && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE $sqltables_rezerwacje SET status = 'zatwierdzona' WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        header("Location: admin_rezerwacje.php");
        exit();
    }

    if (isset($_POST['anuluj']) && isset($_POST['id'])) {
        $stmt = $conn->prepare("UPDATE $sqltables_rezerwacje SET status = 'anulowana' WHERE id = ?");
        $stmt->bind_param("i", $_POST['id']);
        $stmt->execute();
        header("Location: admin_rezerwacje.php");
        exit();
    }
}



$sql = "SELECT r.id, u.email, k.imie, k.nazwisko, w.nazwa, w.data, w.liczba_dni, r.data_rezerwacji, r.status 
        FROM $sqltables_rezerwacje r
        JOIN $sqltables_users u ON r.id_uzytkownika = u.id
        LEFT JOIN $sqltables_klienci k ON k.user_id = u.id
        JOIN $sqltables_wycieczki w ON r.id_wycieczki = w.id
        ORDER BY r.data_rezerwacji DESC";

$rezerwacje = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>TatraTrips </title>
    <link rel="stylesheet" href="../user/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>

.admin-header {
    text-align: center;
    font-size: 28px;
    margin: 40px auto 20px;
    color: #fff;
    text-shadow: 1px 1px 4px #000;
}


.admin-table {
    width: 95%;
    margin: 0 auto 60px;
    border-collapse: collapse;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    font-size: 14px;
    color: #fff;
}

.admin-table th {
    background-color:  rgba(167,179,93,0.95);
    padding: 14px 12px;
    font-size: 14px;
    font-weight: bold;
    border-bottom: 2px solid rgba(255, 255, 255, 0.1);
    text-align: center;
}

.admin-table td {
    padding: 12px;
    color: #000;
    background: rgba(255, 255, 255, 0.3);
    text-align: center;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.admin-table tr:hover {
    background: rgba(255, 255, 255, 0.2);
}


.admin-table button {
    padding: 5px 10px;
    margin: 2px;
    border: none;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s ease;
}

.admin-table button[name="zatwierdz"] {
    background-color: #66bb6a;
    color: white;
}

.admin-table button[name="anuluj"] {
    background-color: #e53935;
    color: white;
}

.admin-table button:hover {
    transform: scale(1.05);
}
</style>

</head>
<body>

<header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i> TatraTrips</h2>
    <nav class="navbar">
        <a href="./admin_wycieczki.php">Wycieczki</a>
       <a href="./admin_komentarze.php">Komentarze</a>
        <a href="./admin_zgloszenia.php">Zgłoszenia</a>
        <a href="./admin_rezerwacje.php">Rezerwacje</a>
        <a href="./admin_uzytkownicy.php">Użytkownicy</a>
    </nav>
    <div class="user-menu">
        <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User" class="user-pic" onclick="toggleMenu()">
        <div class="sub-menu-wrap" id="subMenu">
            <div class="sub-menu">
                <div class="user-info">
                    <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User">
                    <h3><?= htmlspecialchars($email) ?></h3>
                </div>
                <hr>
                <a href="../logout.php" class="sub-menu-link"><p><i class='bx bx-log-out'></i>Wyloguj się</p></a>
            </div>
        </div>
    </div>
</header>

<div class="background"></div>
<h2 class="admin-header">Rezerwacje użytkowników</h2>
<table class="admin-table" border="1" cellpadding="10">
    <tr>
        <th>Id</th>
        <th>Klient</th>
        <th>Wycieczka</th>
        <th>Data rozpoczęcia</th>
        <th>Liczba dni</th>
        <th>Data rezerwacji</th>
        <th>Status</th>
        <th>Akcje</th>
    </tr>
   <?php while ($r = $rezerwacje->fetch_assoc()): ?>
    <tr>
        <td><?= $r['id'] ?></td>
        <td>
            <?= htmlspecialchars($r['imie'] . ' ' . $r['nazwisko']) ?><br>
            <small style="font-size: 13px; color: #444;">
                <?= htmlspecialchars($r['email']) ?>
            </small>
        </td>
        <td><?= htmlspecialchars($r['nazwa']) ?></td>
        <td><?= htmlspecialchars($r['data']) ?></td>
        <td><?= (int)$r['liczba_dni'] ?></td>
        <td><?= $r['data_rezerwacji'] ?></td>
        <td><?= $r['status'] ?></td>
        <td>
            <?php if ($r['status'] === 'oczekująca'): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button type="submit" name="zatwierdz">Zatwierdź</button>
                    <button type="submit" name="anuluj" onclick="return confirm('Na pewno anulować?')">Anuluj</button>
                </form>
            <?php else: ?>
                brak akcji
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>

</table>
<script src="../user/script.js"></script>
</body>
</html>
