<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$profil_zdjecie = './user/user.png';


$stmt = $conn->prepare("SELECT * FROM $sqltables_klienci WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$klient = $result->fetch_assoc();


if ($klient && !empty($klient['zdjecie']) && file_exists(__DIR__ . '/uploads/' . $klient['zdjecie'])) {
    $profil_zdjecie = 'uploads/' . $klient['zdjecie'];
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>TatraTrips</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./style-profile.css">
</head>
<body>

<header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i> TatraTrips</h2>
    <button class="menu-toggle2" onclick="toggleMenu2()">Menu</button>
    <nav class="navbar">
        <a href="index.php">Strona Główna</a>
        <a href="./user/komentarze.php">O nas</a>
        <a href="./user/kontakt.php">Zgłoś</a>
        <a href="./user/chat.php">Powiadomienia</a>
    </nav>
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
</header>

<div class="background"></div>

<div class="container-profil">
  <div class="profil-box">
    <h2 style="text-align:center;">Twój profil</h2>

    <?php if (!$klient): ?>
    <form method="post" action="zapiszProfil.php" enctype="multipart/form-data">
        <div class="input-box email">
          <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly class="filled">
          <label>Email</label>
        </div>
        <div class="input-box"><input type="text" name="imie" required><label>Imię</label></div>
        <div class="input-box"><input type="text" name="nazwisko" required><label>Nazwisko</label></div>
        <div class="input-box"><input type="text" name="telefon" required><label>Telefon</label></div>
        <div class="input-box"><textarea name="adres" required></textarea><label>Adres</label></div>
        <div class="input-box"><textarea name="opis"></textarea><label>Opis</label></div>
        <div class="input-box"><input type="file" name="zdjecie" accept="image/*"><label>Zdjęcie</label></div>
        <button type="submit" class="btn">Zapisz</button>
    </form>
    <form method="post" action="zmien_haslo.php" style="margin-top: 30px;">
      <h3 style="text-align:center;">Zmiana hasła</h3>
      <div class="input-box"><input type="password" name="stare_haslo" required><label>Stare hasło</label></div>
      <div class="input-box"><input type="password" name="nowe_haslo" required><label>Nowe hasło</label></div>
      <div class="input-box"><input type="password" name="powtorz_haslo" required><label>Powtórz nowe hasło</label></div>
      <button type="submit" class="btn">Zmień hasło</button>
    </form>

    <form method="post" action="usun_konto.php" onsubmit="return confirm('Czy na pewno chcesz usunąć swoje konto?')" style="margin-top: 30px;">
      <h3 style="text-align:center; ">Usuń konto</h3>
      <div class="input-box"><input type="password" name="potwierdz_haslo" required><label>Potwierdź hasłem</label></div>
        <button type="submit" class="btn">Usuń konto</button>
    </form>
    <?php else: ?>
    <div class="input-box readonly"><div class="readonly-field"><?= htmlspecialchars($email) ?></div><label>Email</label></div>
    <div class="input-box readonly"><div class="readonly-field"><?= htmlspecialchars($klient['imie']) . ' ' . htmlspecialchars($klient['nazwisko']) ?></div><label>Imię i nazwisko</label></div>
    <div class="input-box readonly"><div class="readonly-field"><?= htmlspecialchars($klient['telefon']) ?></div><label>Telefon</label></div>
    <div class="input-box readonly"><div class="readonly-field"><?= nl2br(htmlspecialchars($klient['adres'])) ?></div><label>Adres</label></div>
    <div class="input-box readonly"><div class="readonly-field"><?= nl2br(htmlspecialchars($klient['opis'])) ?></div><label>Opis</label></div>
    <?php if (!empty($klient['zdjecie'])): ?>
    <div class="input-box readonly"><img src="uploads/<?= htmlspecialchars($klient['zdjecie']) ?>" alt="Zdjęcie profilowe" style="max-width: 100%; border-radius: 8px;"><label>Zdjęcie</label></div>
    <?php endif; ?>
    <div class="button-row" style="margin-top: 20px;"><a href="./edytuj_profil.php"><button class="btn">Edytuj profil</button></a></div>

    <form method="post" action="zmien_haslo.php" style="margin-top: 30px;">
      <h3 style="text-align:center;">Zmiana hasła</h3>
      <div class="input-box"><input type="password" name="stare_haslo" required><label>Stare hasło</label></div>
      <div class="input-box"><input type="password" name="nowe_haslo" required><label>Nowe hasło</label></div>
      <div class="input-box"><input type="password" name="powtorz_haslo" required><label>Powtórz nowe hasło</label></div>
      <button type="submit" class="btn">Zmień hasło</button>
    </form>

    <form method="post" action="usun_konto.php" onsubmit="return confirm('Czy na pewno chcesz usunąć swoje konto?')" style="margin-top: 30px;">
      <h3 style="text-align:center; ">Usuń konto</h3>
      <div class="input-box"><input type="password" name="potwierdz_haslo" required><label>Potwierdź hasłem</label></div>
        <button type="submit" class="btn">Usuń konto</button>
    </form>
    <?php endif; ?>
  </div>
</div>

<script src="../aplikacja/user/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
<script>
<?php
$message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'niepoprawny_telefon': $message = 'Podany numer telefonu jest nieprawidłowy.'; break;
        case 'puste_pola': $message = 'Wszystkie pola są wymagane.'; break;
        case 'niepoprawny_typ_pliku': $message = 'Dozwolone formaty zdjęć to jpg, png, gif.'; break;
        case 'plik_za_duzy': $message = 'Zdjęcie jest za duże. Maksymalny rozmiar to 2MB.'; break;
        case 'blad_zapisu': $message = 'Wystąpił błąd podczas zapisywania danych.'; break;
        case 'puste_haslo': $message = 'Wprowadź wszystkie pola hasła.'; break;
        case 'hasla_nie_zgadzaja': $message = 'Podane hasła nie są takie same.'; break;
        case 'haslo_za_krotkie': $message = 'Hasło musi mieć co najmniej 6 znaków.'; break;
        case 'zle_stare_haslo': $message = 'Stare hasło jest nieprawidłowe.'; break;
        case 'bledne_haslo_usuniecie': $message = 'Hasło nieprawidłowe – nie można usunąć konta.'; break;
        default: $message = 'Wystąpił nieznany błąd.'; break;
    }
    echo "Swal.fire({ icon: 'error', title: 'Błąd!', text: '$message', confirmButtonText: 'OK' });";
} elseif (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'haslo_zmienione': $message = 'Hasło zostało pomyślnie zmienione.'; break;
        case 'konto_usuniete': $message = 'Konto zostało usunięte pomyślnie.'; break;
        default: $message = 'Dane zapisane pomyślnie.'; break;
    }
    echo "Swal.fire({ icon: 'success', title: 'Sukces!', text: '$message', confirmButtonText: 'Super' });";
}
?>
</script>
<?php endif; ?>
<script src="./user/script.js"></script>
</body>
</html>
