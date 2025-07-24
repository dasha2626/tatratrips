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


function dodajPowiadomienia($conn, $wycieczka_id, $tresc) {
    global $sqltables_rezerwacje, $sqltables_wiadomosci_systemowe;
    $stmt_users = $conn->prepare("SELECT DISTINCT id_uzytkownika FROM $sqltables_rezerwacje WHERE id_wycieczki = ?");
    $stmt_users->bind_param("i", $wycieczka_id);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();

    $stmt_insert = $conn->prepare("INSERT INTO $sqltables_wiadomosci_systemowe (user_id, tresc, data_dodania) VALUES (?, ?, NOW())");

    while ($row = $result_users->fetch_assoc()) {
        $user_id = $row['id_uzytkownika'];
        $stmt_insert->bind_param("is", $user_id, $tresc);
        $stmt_insert->execute();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {



    if (isset($_POST['dodaj'])) {
        $stmt = $conn->prepare("INSERT INTO $sqltables_wycieczki (nazwa, opis, data, cena, dostepne_miejsca, status, liczba_dni) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssdisi", $_POST['nazwa'], $_POST['opis'], $_POST['data'], $_POST['cena'], $_POST['dostepne_miejsca'], $_POST['status'], $_POST['liczba_dni']);
        $stmt->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }


  if (isset($_POST['edytuj'], $_POST['id'])) {
    $stmt = $conn->prepare("UPDATE $sqltables_wycieczki SET nazwa=?, opis=?, data=?, cena=?, dostepne_miejsca=?, status=?, liczba_dni=? WHERE id=?");
    $stmt->bind_param("sssdisii", $_POST['nazwa'], $_POST['opis'], $_POST['data'], $_POST['cena'], $_POST['dostepne_miejsca'], $_POST['status'], $_POST['liczba_dni'], $_POST['id']);
    $stmt->execute();
    $nazwa = $_POST['nazwa'];
    $tresc = "System: status lub dane wycieczki „{$nazwa}” zostały zmienione przez administratora. Sprawdź szczegóły w Twoich rezerwacjach.";
    dodajPowiadomienia($conn, $_POST['id'], $tresc);
}

if (isset($_POST['usun'], $_POST['id'])) {
    $stmt_name = $conn->prepare("SELECT nazwa FROM $sqltables_wycieczki WHERE id = ?");
    $stmt_name->bind_param("i", $_POST['id']);
    $stmt_name->execute();
    $res = $stmt_name->get_result();
    $nazwa = $res->fetch_assoc()['nazwa'] ?? 'nieznana wycieczka';

    
    $tresc = "System: wycieczka „{$nazwa}” została wycofana przez administratora.";
    dodajPowiadomienia($conn, $_POST['id'], $tresc);

    
    $stmt = $conn->prepare("DELETE FROM $sqltables_wycieczki WHERE id = ?");
    $stmt->bind_param("i", $_POST['id']);
    $stmt->execute();
}

}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TatraTrips</title>
    <link rel="stylesheet" href="../user/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
<style>
        body      {font-family:Arial, sans-serif;}
        .admin-title {
            text-align: center;
            margin: 25px 0;
            color: #000;
                      }

        .wrap     {max-width:1000px;margin:0 auto;background:rgba(255,255,255,0.15);
                   padding:25px;border-radius:12px;backdrop-filter:blur(6px);}
        .wrap form{margin-bottom:12px;background:rgba(0,0,0,0.1);padding:10px;border-radius:8px;}
        input,textarea,select{padding:6px 8px;margin:4px;border-radius:4px;border:none;width:110px;font-size:13px;}
        textarea  {width:180px;height:46px;resize:vertical;}
        button    {padding:6px 10px;border:none;border-radius:6px;font-weight:bold;cursor:pointer}
        .add      {background:#4caf50;color:#fff;}
        .save     {background:#66bb6a;color:#000;}
        .del      {background:#c62828;color:#fff;}
        .row      {display:flex;flex-wrap:wrap;align-items:center;gap:4px}
        label     {font-size:12px;color:#eee}
        .filter-btn {
                padding: 6px 12px;
                margin: 0 6px;
                background-color: #4caf50;
                color: white;
                border-radius: 6px;
                text-decoration: none;
                font-weight: bold;
         }

    </style>
</head>
<body>

<header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i> TatraTrips</h2>
    <button class="menu-toggle2" onclick="toggleMenu2()">Menu</button>
   <nav class="navbar">
        <a href="./admin_wycieczki.php">Wycieczki</a>
        <a href="./admin_komentarze.php">Komentarze</a>
        <a href="./admin_zgloszenia.php">Zgłoszenia</a>
        <a href="./admin_rezerwacje.php">Rezerwacje</a>
        <a href="./admin_uzytkownicy.php">Użytkownicy</a>
</nav>
    <div class="header-right"></div>

   
        <div class="user-menu">
            <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User" class="user-pic" onclick="toggleMenu()">
            <div class="sub-menu-wrap" id="subMenu">
                <div class="sub-menu">
                    <div class="user-info">
                        <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User">
                        <h3><?php echo htmlspecialchars($email); ?></h3>
                    </div>
                    <hr>
                        <a href="../logout.php" class="sub-menu-link">
                        <p><i class='bx bx-log-out'></i>Wyloguj się</p>
                    </a>
                
                </div>
            </div>
        </div>
</header>



<div class="background"></div>
<h2 class="admin-title">Dodaj nową wycieczkę</h2>
<div class="wrap">
    <form method="post" class="row">
        <input type="text" name="nazwa" placeholder="Nazwa" required />
        <textarea name="opis" placeholder="Opis" required></textarea>
        <input type="date" name="data" required />
        <input type="number" name="cena" step="0.01" placeholder="Cena" required />
        <input type="number" name="dostepne_miejsca" placeholder="Miejsca" required />
        <select name="status">
            <option value="aktywna">aktywna</option>
            <option value="nieaktywna">nieaktywna</option>
        </select>
        <select name="liczba_dni" required>
            <option value="" disabled selected>Liczba dni</option>
            <option value="3">3 dni</option>
            <option value="4">4 dni</option>
            <option value="6">6 dni</option>
        </select>
        <button type="submit" name="dodaj" class="add">Dodaj</button>
    </form>
</div>

<h2 class="admin-title">Wszystkie wycieczki</h2>
<div style="text-align:center; margin: 20px auto;">
    <p style="font-size: 18px; margin-bottom: 10px;">Filtruj wycieczki według długości:</p>
    <a href="admin_wycieczki.php" class="filter-btn">Wszystkie</a>
    <a href="admin_wycieczki.php?dni=3" class="filter-btn">3 dni</a>
    <a href="admin_wycieczki.php?dni=4" class="filter-btn">4 dni</a>
    <a href="admin_wycieczki.php?dni=6" class="filter-btn">6 dni</a>
</div>

<div class="wrap">
<?php
$dni_filter = isset($_GET['dni']) ? (int)$_GET['dni'] : 0;

if (in_array($dni_filter, [3, 4, 6])) {
    $stmt = $conn->prepare("SELECT * FROM $sqltables_wycieczki WHERE liczba_dni = ? ORDER BY data ASC");
    $stmt->bind_param("i", $dni_filter);
    $stmt->execute();
    $wycieczki = $stmt->get_result();
} else {
    $wycieczki = $conn->query("SELECT * FROM $sqltables_wycieczki ORDER BY data ASC");
}

while ($w = $wycieczki->fetch_assoc()): ?>
    <form method="post" class="row">
        <input type="hidden" name="id" value="<?= $w['id'] ?>">
        <input type="text" name="nazwa" value="<?= htmlspecialchars($w['nazwa']) ?>">
        <textarea name="opis"><?= htmlspecialchars($w['opis']) ?></textarea>
        <input type="date" name="data" value="<?= $w['data'] ?>">
        <input type="number" step="0.01" name="cena" value="<?= $w['cena'] ?>">
        <input type="number" name="dostepne_miejsca" value="<?= $w['dostepne_miejsca'] ?>">
        <select name="status">
            <option value="aktywna" <?= $w['status'] == 'aktywna' ? 'selected' : '' ?>>aktywna</option>
            <option value="nieaktywna" <?= $w['status'] == 'nieaktywna' ? 'selected' : '' ?>>nieaktywna</option>
        </select>
        <select name="liczba_dni" required>
            <option value="3" <?= $w['liczba_dni'] == 3 ? 'selected' : '' ?>>3 dni</option>
            <option value="4" <?= $w['liczba_dni'] == 4 ? 'selected' : '' ?>>4 dni</option>
            <option value="6" <?= $w['liczba_dni'] == 6 ? 'selected' : '' ?>>6 dni</option>
        </select>
        <button name="edytuj" class="save">Zapisz</button>
        <button name="usun" class="del" onclick="return confirm('Na pewno usunąć?')">Usuń</button>
    </form>
<?php endwhile; ?>
</div>
<script src="../user/script.js"></script>
</body>
</html>
