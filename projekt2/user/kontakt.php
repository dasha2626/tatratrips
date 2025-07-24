<?php
require '../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}
$email = $_SESSION['email'] ?? '';
$user_id = $_SESSION['user_id'] ?? null;
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
    .form-zgloszenie {
    max-width: 500px;
    margin: 60px auto;
    padding: 30px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    color: white;
}

.form-zgloszenie h2 {
    text-align: center;
    color: white;
    margin-bottom: 20px;
}

.form-zgloszenie select,
.form-zgloszenie input,
.form-zgloszenie textarea {
    width: 100%;
    margin: 10px 0;
    padding: 10px;
    border-radius: 8px;
    border: none;
    font-size: 15px;
}

.form-zgloszenie label {
    font-weight: bold;
    display: block;
    margin-top: 12px;
    color: white;
}

.form-zgloszenie button {
    padding: 10px 20px;
    background-color:rgb(163,188,101);
    border: none;
    border-radius: 25px;
    color: white;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

.form-zgloszenie button:hover {
    background-color:rgb(163,188,101);
}

  </style>
</head>
<body>
    <header class="header">
    <h2 class="logo"><i class="fa-solid fa-mountain"></i>TatraTrips</h2>
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
 <form action="zgloszenie.php" method="post" class="form-zgloszenie">

    <h2>Wyślij zgłoszenie</h2>
    <label>Email:</label>
    <input type="email" name="email" required value="<?= htmlspecialchars($email) ?>" readonly>

    
    <label>Temat zgłoszenia:</label>
    <select name="temat" required>
    <option value="" disabled selected>Wybierz temat</option>
    <option value="błąd">Zgłoszenie błędu</option>
    <option value="sugestia">Sugestia / Propozycja</option>
    </select>


    <label>Treść:</label>
    <textarea name="tresc" rows="6" required></textarea>

    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
    <button type="submit">Wyślij zgłoszenie</button>
  </form>
  <script src="./script.js"></script>
</body>
</html>
