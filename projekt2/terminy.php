<?php
require 'db.php';
session_start();

$dni = isset($_GET['dni']) ? intval($_GET['dni']) : 0;
if ($dni <= 0) {
    echo "Nieprawidłowa liczba dni.";
    exit();
}


$wybrany_miesiac = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
[$rok, $miesiac] = explode('-', $wybrany_miesiac);
$rok = intval($rok);
$miesiac = intval($miesiac);

$stmt = $conn->prepare("SELECT id, nazwa , data, dostepne_miejsca, cena, opis FROM $sqltables_wycieczki WHERE liczba_dni = ? AND status = 'aktywna' AND MONTH(data) = ? AND YEAR(data) = ?");
$stmt->bind_param("iii", $dni, $miesiac, $rok);
$stmt->execute();
$result = $stmt->get_result();

$terminy = [];
while ($row = $result->fetch_assoc()) {
    $terminy[$row['data']] = $row; 
}


$dni_w_miesiacu = cal_days_in_month(CAL_GREGORIAN, $miesiac, $rok);
$pierwszy_dzien = date('w', strtotime("$rok-$miesiac-01")); 
$dni_tygodnia = ['Nd', 'Pn', 'Wt', 'Śr', 'Cz', 'Pt', 'Sb'];

$prev = date('Y-m', strtotime("-1 month", strtotime("$rok-$miesiac-01")));
$next = date('Y-m', strtotime("+1 month", strtotime("$rok-$miesiac-01")));

setlocale(LC_TIME, 'pl_PL.UTF-8');
$nazwa_miesiaca = strftime('%B %Y', strtotime("$rok-$miesiac-01"));


$email = $_SESSION['email'] ?? null;
$profil_zdjecie = './user.png'; // domyślna ikona

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt_z = $conn->prepare("SELECT zdjecie FROM $sqltables_klienci WHERE user_id = ?");
    $stmt_z->bind_param("i", $user_id);
    $stmt_z->execute();
    $result_z = $stmt_z->get_result();
    if ($row_z = $result_z->fetch_assoc()) {
        if (!empty($row_z['zdjecie']) && file_exists(__DIR__ . '/uploads/' . $row_z['zdjecie'])) {
            $profil_zdjecie = 'uploads/' . $row_z['zdjecie'];
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Tatratrips</title>
    <link rel="stylesheet" href="./style-terminy.css">
    <link rel="stylesheet" href="./user/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        td a {
            display: inline-block;
            margin-top: 5px;
            padding: 4px 8px;
            background-color: #4caf50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        td a:hover {
            background-color: #388e3c;
        }
        .reserve-btn {
            display: inline-block;
            margin-top: 8px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            font-weight: bold;
            font-size: 14px;
            text-decoration: none;
            border: none;
            border-radius: 25px;
            transition: background 0.3s ease, transform 0.2s ease;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
         }

.reserve-btn:hover {
  background: linear-gradient(135deg, #388e3c, #43a047);
  transform: scale(1.05);
}
.tooltip-wrapper {
    position: relative;
    display: inline-block;
    cursor: pointer;
}

.tooltip-box {
    visibility: hidden;
    opacity: 0;
    width: 220px;
    background-color: #fff;
    color: #000;
    text-align: left;
    border-radius: 8px;
    padding: 12px;
    position: absolute;
    z-index: 1;
    bottom: 125%; 
    left: 50%;
    transform: translateX(-50%);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    transition: opacity 0.3s;
}

.tooltip-wrapper:hover .tooltip-box {
    visibility: visible;
    opacity: 1;
}

.tooltip-box::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    margin-left: -6px;
    border-width: 6px;
    border-style: solid;
    border-color: #fff transparent transparent transparent;
}


.tooltip-cell {
    background-color: #e0f7e9;
    position: relative;
}

    </style>
</head>
<body>
    

<header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i> TatraTrips</h2>
    <button class="menu-toggle2" onclick="toggleMenu2()">Menu</button>
    <nav class="navbar">
        <a href="./index.php">Strona Główna</a>
        <a href="./user/komentarze.php">O nas</a>
        <a href="./user/kontakt.php">Zgłoś</a>
        <a href="./user/chat.php">Powiadomienia</a>
    </nav>

    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="header-right">
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
    </div>
    <?php endif; ?>
    
       
</header>

<div class="background"></div>

<h2 class="calendar-header">
    <a href="?dni=<?= $dni ?>&month=<?= $prev ?>" class="arrow-btn"><i class="fa-solid fa-chevron-left"></i></a>
    Kalendarz dostępnych terminów (<?= $dni ?> dni) — <?= ucfirst($nazwa_miesiaca) ?>
    <a href="?dni=<?= $dni ?>&month=<?= $next ?>" class="arrow-btn"><i class="fa-solid fa-chevron-right"></i></a>
</h2>


<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <?php foreach ($dni_tygodnia as $dzien): ?>
            <th><?= $dzien ?></th>
        <?php endforeach; ?>
    </tr>

    <?php
    echo "<tr>";

    for ($i = 0; $i < $pierwszy_dzien; $i++) {
        echo "<td></td>";
    }

    for ($d = 1; $d <= $dni_w_miesiacu; $d++) {
        $data_str = sprintf('%04d-%02d-%02d', $rok, $miesiac, $d);

        if (isset($terminy[$data_str])) {
            $t = $terminy[$data_str];
            echo "<td class='tooltip-cell'>";
            echo "<div class='tooltip-wrapper'>";
            echo "<strong>$d</strong><br>";
            echo "Miejsca: {$t['dostepne_miejsca']}<br>";

            echo "<div class='tooltip-box'>";
            echo "<strong>" . htmlspecialchars($t['nazwa']) . "</strong><br>";
            echo "Opis: " . htmlspecialchars($t['opis']) . "<br>";
           echo "Cena: " . (isset($t['cena']) ? htmlspecialchars($t['cena']) . " zł" : "brak danych");

            echo "</div>";

            if ($t['dostepne_miejsca'] > 0) {
                echo "<br><a class='reserve-btn' href='./user/formularz.php?id={$t['id']}'>Rezerwuj</a>";
            } else {
                echo "<br><span style='color:red;'>Brak</span>";
            }

            echo "</div></td>";
        } else {
            echo "<td>$d</td>";
        }

       
        if ((($pierwszy_dzien + $d) % 7) === 0) {
            echo "</tr><tr>";
        }
    }

    echo "</tr>"; 
    ?>
</table>

<script src="./user/script.js"></script>
</body>
</html>