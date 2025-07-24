<?php
session_start();
require 'db.php';
global $sqltables_rezerwacje, $sqltables_wycieczki;

$pokaz_alert_komentarz = false;
if (isset($_SESSION['komentarz_dodany'])) {
    $pokaz_alert_komentarz = true;
    unset($_SESSION['komentarz_dodany']);
}


if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? null;
$profil_zdjecie = './user/user.png';

$stmt = $conn->prepare("SELECT zdjecie FROM $sqltables_klienci WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_z = $stmt->get_result();
if ($row_z = $result_z->fetch_assoc()) {
    if (!empty($row_z['zdjecie']) && file_exists(__DIR__ . '/uploads/' . $row_z['zdjecie'])) {
        $profil_zdjecie = 'uploads/' . $row_z['zdjecie'];
    }
}

$sql = "SELECT r.id AS id_rezerwacji, r.data_rezerwacji, r.status AS status_rezerwacji, 
       w.id AS id_wycieczki, w.nazwa, w.data, w.liczba_dni, w.status AS status_wycieczki
       FROM $sqltables_rezerwacje r
        JOIN $sqltables_wycieczki w ON r.id_wycieczki = w.id
        WHERE r.id_uzytkownika = ?
       ORDER BY r.data_rezerwacji DESC";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>TatraTrips</title>
    <link rel="stylesheet" href="./user/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        table {
            width: 90%;
            margin: 40px auto;
            border-collapse: separate;
            border-spacing: 0;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            color: #fff;
        }
        th {
            background-color: rgba(167,179,93,0.95);
            padding: 16px;
            font-size: 15px;
            font-weight: 600;
        }
        td {
            padding: 14px 16px;
            font-size: 14px;
            color: black;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .rezerwacje-header {
            text-align: center;
            font-size: 26px;
            margin-top: 60px;
            color: rgb(0, 0, 0);
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        .cancel-btn {
            display: inline-block;
            margin-top: 8px;
            padding: 6px 16px;
            color: white;
            background: linear-gradient(135deg,rgb(145,157,94),rgb(163,188,101));
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15);
        }
        .cancel-btn:hover {
            background: linear-gradient(135deg, #c62828, #b71c1c);
            transform: scale(1.05);
        }
        textarea {
            width: 100%;
            border-radius: 6px;
            padding: 5px;
            resize: vertical;
            margin-top: 8px;
        }
    </style>
</head>
<body>

<header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i>TatraTrips</h2>
    <nav class="navbar">
        <a href="./index.php">Strona Główna</a>
        <a href="./user/komentarze.php">O nas</a>
        <a href="./user/kontakt.php">Zgłoś</a>
        <a href="./user/chat.php">Powiadomienia</a>
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
                 <a href="./profil.php" class="sub-menu-link"><p><i class='bx bxs-id-card'></i>Mój profil</p></a>
                    <a href="./mojerezerwacje.php" class="sub-menu-link"><p><i class='bx bxs-cool'></i>Moje rezerwacje</p></a>
                    <a href="./logout.php" class="sub-menu-link"><p><i class='bx bx-log-out'></i>Wyloguj się</p></a>
            </div>
        </div>
    </div>
</header>

<div class="background"></div>

<h2 class="rezerwacje-header">Moje rezerwacje</h2>

<?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Data rezerwacji</th>
            <th>Wycieczka</th>
            <th>Data wycieczki</th>
            <th>Status wycieczki</th>
            <th>Liczba dni</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): 
           $data_od = new DateTime($row['data']);

            $data_do = (clone $data_od)->modify('+' . ($row['liczba_dni'] - 1) . ' days');
            $dzisiaj = new DateTime();
            $czy_zakonczona = $data_do < $dzisiaj;
        ?>
        <tr>
            <td><?= htmlspecialchars($row['data_rezerwacji']) ?></td>
            <td><?= htmlspecialchars($row['nazwa']) ?></td>
            <td><?= htmlspecialchars($row['data']) ?></td>
            <td><?= htmlspecialchars($row['status_wycieczki']) ?></td>
            <td><?= htmlspecialchars($row['liczba_dni']) ?></td>
            <td>
    <?= htmlspecialchars($row['status_rezerwacji']) ?>

    <?php
   $czy_mozna_anulowac = in_array($row['status_rezerwacji'], ['zatwierdzona', 'oczekująca']);

    $aktywana = $row['status_wycieczki'] !== 'nieaktywna';
    ?>

 <?php if ($czy_mozna_anulowac && $aktywana): ?>
    <?php if (!$czy_zakonczona): ?>
        <form method="post" action="./user/anuluj_rezerwacje.php" style="display:inline;">
            <input type="hidden" name="id_rezerwacji" value="<?= (int)$row['id_rezerwacji'] ?>">
            <button type="submit" class="cancel-btn" onclick="return confirm('Czy na pewno chcesz anulować tę rezerwację?')">Anuluj</button>
        </form>
    <?php else: ?>
        <form method="post" action="./user/dodaj_komentarz.php">
            <input type="hidden" name="id_wycieczki" value="<?= (int)$row['id_wycieczki'] ?>">
            <textarea name="tresc" placeholder="Napisz swoją opinię o wycieczce..." rows="3" required></textarea>
            <button type="submit" class="cancel-btn">Dodaj komentarz</button>
        </form>
    <?php endif; ?>
<?php elseif ($row['status_rezerwacji'] === 'zatwierdzona' && !$aktywana): ?>
    <span style="color: #a00; font-weight: bold;">! Wycieczka anulowana przez administratora !</span>
<?php endif; ?>

</td>
        </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p style="text-align:center;">Nie masz jeszcze żadnych rezerwacji.</p>
<?php endif; ?>


<script src="./user/script.js"></script>
<?php if ($pokaz_alert_komentarz): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Komentarz dodany!',
        text: 'Dziękujemy za Twoją opinię.',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>
<?php if (isset($_SESSION['haslo_zmienione'])): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
Swal.fire({
    icon: 'success',
    title: 'Hasło zmienione!',
    text: 'Twoje hasło zostało zaktualizowane.',
    confirmButtonText: 'OK'
});
</script>
<?php unset($_SESSION['haslo_zmienione']); endif; ?>


</body>
</html>
