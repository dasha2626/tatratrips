<?php
require '../db.php';
session_start();

$email = $_SESSION['email'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
$profil_zdjecie = './user.png';
$imie = $nazwisko = $telefon = "";

if ($user_id) {
    $stmt = $conn->prepare("SELECT zdjecie, imie, nazwisko, telefon FROM $sqltables_klienci WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $klient = $result->fetch_assoc();
        $imie = $klient['imie'];
        $nazwisko = $klient['nazwisko'];
        $telefon = $klient['telefon'];
           if (!empty($klient['zdjecie']) && file_exists(__DIR__ . '/../uploads/' . $klient['zdjecie'])) {
    $profil_zdjecie = '../uploads/' . $klient['zdjecie'];
}
    }
}


$dni = isset($_GET['dni']) ? intval($_GET['dni']) : null;

$sql = "SELECT k.tresc, k.data_dodania, u.email, w.nazwa, w.data, w.liczba_dni, kl.imie, kl.nazwisko
        FROM $sqltables_komentarze k
        JOIN $sqltables_users u ON k.id_uzytkownika = u.id
        JOIN $sqltables_wycieczki w ON k.id_wycieczki = w.id
        LEFT JOIN $sqltables_klienci kl ON kl.user_id = u.id
        WHERE k.zatwierdzony = 1";


if ($dni) {
    $sql .= " AND w.liczba_dni = $dni";
}

$sql .= " ORDER BY w.data ASC, k.data_dodania ASC";
$result = $conn->query($sql);
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
        .komentarz-container {
            max-width: 900px;
            margin: 100px auto;
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 12px;
            backdrop-filter: blur(6px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        }

      
        .komentarz:last-child {
            border-bottom: none;
        }

                .komentarz {
            background: rgba(255, 255, 255, 0.9); 
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            color: #000; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .komentarz .meta {
            font-size: 13px;
            color: #333;
            margin-bottom: 8px;
        }

        .komentarz strong {
            color: #4CAF50; 
        }

                h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #fff;
            text-shadow: 1px 1px 2px black;
        }

        .filter-btn {
            display: inline-block;
            margin: 5px;
            padding: 10px 18px;
            background-color:rgb(145,157,94);
            color: #000;
            text-decoration: none;
            border-radius: 20px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .filter-btn:hover {
            background-color:rgb(163,188,101);
        }
            .contact-section {
        max-width: 900px;
        margin: 40px auto;
        background: rgba(255, 255, 255, 0.9);
        padding: 25px;
        border-radius: 12px;
        text-align: center;
        color: #000;
        box-shadow: 0 6px 18px rgba(0,0,0,0.2);
    }

    .contact-section h2 {
        font-size: 22px;
        margin-bottom: 10px;
        color: #2d2d2d;
    }

    .contact-info {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 15px;
        align-items: center;
    }

    .contact-info i {
        color: #4CAF50;
        margin-right: 8px;
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
    <?php else: ?>
    <div class="guest-menu">
        <a href="../login.html" class="login-link">Zaloguj się</a>
    </div>
    <?php endif; ?>
</header>

<div class="background"></div>

<div class="komentarz-container">
    <div class="brush-text-wrapper" style="margin-bottom: 10px;">
        <div class="brush-text">Lepiej o nas powiedzą nasze komentarze!</div>
    </div>

    <h1>Komentarze do wycieczek</h1>

    <div style="text-align:center; margin: 20px auto;">
        <p style="font-size: 18px; margin-bottom: 10px;">Wybierz, które komentarze chcesz zobaczyć:</p>
        <a href="komentarze.php" class="filter-btn">Wszystkie</a>
        <a href="komentarze.php?dni=3" class="filter-btn">Tylko 3-dniowe wycieczki</a>
        <a href="komentarze.php?dni=4" class="filter-btn">Tylko 4-dniowe wycieczki</a>
        <a href="komentarze.php?dni=6" class="filter-btn">Tylko 6-dniowe wycieczki</a>
    </div>

    <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="komentarz">
                <div class="meta">
                    <strong><?= htmlspecialchars($row['nazwa']) ?></strong>
                    (<?= htmlspecialchars($row['data']) ?>, <?= htmlspecialchars($row['liczba_dni']) ?> dni)<br>
                  <?php
                    $autor = (!empty($row['imie']) && !empty($row['nazwisko']))
                        ? htmlspecialchars($row['imie'] . ' ' . $row['nazwisko'])
                        : 'naszego klienta';
                    ?>
                    Dodano: <?= htmlspecialchars($row['data_dodania']) ?> przez <?= $autor ?>

                </div>
                <p><?= nl2br(htmlspecialchars($row['tresc'])) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Brak komentarzy.</p>
    <?php endif; ?>
</div>

<div class="contact-section">
    <h2>JEŻELI JEDNAK MASZ PYTANIA</h2>
    <p>Skontaktuj się z nami, a chętnie pomożemy!</p>
    <div class="contact-info">
        <div>
            <i class="fa-solid fa-envelope"></i> kontakt@tatra-trips.pl<br>
            <i class="fa-solid fa-phone"></i> +48 123 456 789<br>
            <i class="fa-solid fa-location-dot"></i> ul. Górska 1, Zakopane
        </div>
        <iframe 
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2501.295352505719!2d19.9808845!3d49.276889399999995!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4715f3776c1e5d4f%3A0x55d15029ddee8885!2sTeleskop%20przy%20drodze!5e1!3m2!1sru!2spl!4v1750353868010!5m2!1sru!2spl" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</div>


<script src="./script.js"></script>
</body>
</html>
