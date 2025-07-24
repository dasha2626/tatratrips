<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$profil_zdjecie = './user.png'; 


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
        <a href="./user/index.php">Strona Główna</a>
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
    <div class="form-box login">
      <form method="post" action="zaktualizuj_profil.php" enctype="multipart/form-data">
        <h2>Edytuj swój profil</h2>

       <div class="input-box email">
      <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly class="filled">
       <label>Email</label>
        </div>

        <div class="input-box">
          <input type="text" name="imie" value="<?= htmlspecialchars($klient['imie']) ?>" required>
          <label>Imię</label>
        </div>

        <div class="input-box">
          <input type="text" name="nazwisko" value="<?= htmlspecialchars($klient['nazwisko']) ?>" required>
          <label>Nazwisko</label>
        </div>

        <div class="input-box">
          <input type="text" name="telefon" value="<?= htmlspecialchars($klient['telefon']) ?>" required>
          <label>Telefon</label>
        </div>

        <div class="input-box">
          <textarea name="adres" required><?= htmlspecialchars($klient['adres']) ?></textarea>
          <label>Adres</label>
        </div>

        <div class="input-box">
          <textarea name="opis"><?= htmlspecialchars($klient['opis']) ?></textarea>
          <label>Opis</label>
        </div>

        <div class="input-box">
          <input type="file" name="zdjecie" accept="image/*">
          <label>Zdjęcie</label>
        </div>

        <button type="submit" class="btn">Zapisz zmiany</button>

        </div>
      </form>
    </div>
  </div>
</div>

<script src="./user/script.js"></script>
</body>
</html>