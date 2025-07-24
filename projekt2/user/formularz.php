<?php
require '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? null;
$profil_zdjecie = './user.png';

$stmt = $conn->prepare("SELECT zdjecie, imie, nazwisko, telefon FROM $sqltables_klienci WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$imie = $nazwisko = $telefon = "";
if ($result && $result->num_rows > 0) {
    $klient = $result->fetch_assoc();
    $imie = $klient['imie'];
    $nazwisko = $klient['nazwisko'];
    $telefon = $klient['telefon'];
    if (!empty($klient['zdjecie']) && file_exists(__DIR__ . '/../uploads/' . $klient['zdjecie'])) {
        $profil_zdjecie = '../uploads/' . $klient['zdjecie'];
    }
}

$wycieczka_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM $sqltables_wycieczki WHERE id = ? AND status = 'aktywna'");
$stmt->bind_param("i", $wycieczka_id);
$stmt->execute();
$wycieczka = $stmt->get_result()->fetch_assoc();

if (!$wycieczka) {
    echo "Nie znaleziono wycieczki.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>TatraTrips</title>
    <link rel="stylesheet" href="./style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reservation-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            margin: 60px auto;
            max-width: 500px;
            border-radius: 15px;
            backdrop-filter: blur(8px);
            color: white;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .reservation-form label {
            display: block;
            margin-bottom: 10px;
        }

        .reservation-form input[type="text"] {
            width: 100%;
            padding: 8px 10px;
            margin-top: 4px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: none;
        }

        .reservation-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg,rgb(145,157,94),  rgb(163,188,101));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
            transition: 0.3s ease;
        }

        .reservation-btn:hover {
            transform: scale(1.03);
        }

        .reservation-header, .trip-name, .trip-desc {
            text-align: center;
            margin-top: 20px;
            color: #222;
        }

        .trip-desc {
            font-size: 16px;
            padding: 0 20px;
        }
    </style>
</head>
<body>

<header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i> TatraTrips</h2>
    <button class="menu-toggle2" onclick="toggleMenu2()">Menu</button>
    <nav class="navbar">
        <a href="../index.php">Strona Główna</a>
        <a href="./komentarze.php">O nas</a>
        <a href="./kontakt.php">Zgłoś</a>
        <a href="./chat.php">Powiadomienia</a>
    </nav>

    <?php if ($email): ?>
    <div class="user-menu">
        <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User" class="user-pic" onclick="toggleMenu()">
        <div class="sub-menu-wrap" id="subMenu">
            <div class="sub-menu">
                <div class="user-info">
                    <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User">
                    <h3><?= htmlspecialchars($email) ?></h3>
                </div>
                <hr>
                <a href="../profil.php" class="sub-menu-link"><p><i class='bx bxs-id-card'></i>Mój profil</p></a>
                <a href="../mojerezerwacje.php" class="sub-menu-link"><p><i class='bx bxs-cool'></i>Moje rezerwacje</p></a>
                <a href="../logout.php" class="sub-menu-link"><p><i class='bx bx-log-out'></i>Wyloguj się</p></a>
            </div>
        </div>
    </div>
    <?php endif; ?>
</header>

<div class="background"></div>

<h2 class="trip-name"><?= htmlspecialchars($wycieczka['nazwa']) ?></h2>
<p class="trip-desc">Opis: <?= nl2br(htmlspecialchars($wycieczka['opis'])) ?></p>
<p class="trip-desc">Cena: <?= htmlspecialchars($wycieczka['cena']) ?> zł</p>

<h2 class="reservation-header">
    Rezerwacja na <?= htmlspecialchars($wycieczka['data']) ?> (<?= htmlspecialchars($wycieczka['liczba_dni']) ?> dni)
</h2>

<form action="rezerwuj.php" method="post" class="reservation-form">
    <input type="hidden" name="wycieczka_id" value="<?= (int)$wycieczka['id'] ?>">

    <label>Imię:
        <input type="text" name="imie" value="<?= htmlspecialchars($imie) ?>" required>
    </label>
    <label>Nazwisko:
        <input type="text" name="nazwisko" value="<?= htmlspecialchars($nazwisko) ?>" required>
    </label>
    <label>Telefon:
        <input type="text" name="telefon" value="<?= htmlspecialchars($telefon) ?>" required>
    </label>

    <button type="submit" class="reservation-btn">Zarezerwuj</button>
</form>

<script src="./script.js"></script>
</body>
</html>
