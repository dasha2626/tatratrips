<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit();
}

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



$stmt = $conn->prepare("SELECT tresc, data_dodania FROM $sqltables_wiadomosci_systemowe WHERE user_id = ? ORDER BY data_dodania DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
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

.chat-box {
    max-width: 600px;
    margin: 80px auto;
    background: rgba(0, 0, 0, 0.7); 
    padding: 20px;
    border-radius: 12px;
    backdrop-filter: blur(6px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    color: #f1f1f1;
}

.chat-box h3 {
    text-align: center;
    margin-bottom: 20px;
    font-size: 22px;
    color:rgb(163,188,101);
}

.wiadomosc {
    background: rgba(255, 255, 255, 0.08);
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 12px;

}

.wiadomosc p {
    margin: 0;
    font-size: 15px;
    color: #fff;
}

.wiadomosc span {
    display: block;
    font-size: 12px;
    color: #ccc;
    margin-top: 5px;
    text-align: right;
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

<div class="chat-box">
    <h3>Wiadomości systemowe</h3>
 <?php while ($row = $result->fetch_assoc()): ?>
    <div class="wiadomosc">
        <?php
        $tresc = $row['tresc'];

        
             if (str_starts_with($tresc, 'Odpowiedź administratora na zgłoszenie')) {
        if (preg_match('/zgłoszenie o treści „(.+?)”[;:] *„(.+?)$/ui', $tresc, $match)) {
            echo "<p><strong>Zgłoszenie:</strong><br>" . htmlspecialchars($match[1]) . "</p>";
            echo "<p><strong>Odpowiedź administratora:</strong><br>" . htmlspecialchars($match[2]) . "</p>";
        } else {
            echo "<p>" . htmlspecialchars($tresc) . "</p>";
        } 
        
        } elseif (preg_match('/Powód:\s*[„"](.*?)[”"]\.?\s*Treść komentarza:\s*[„"](.*?)[”"]/ui', $tresc, $match)) {
            echo "<p><strong>Twój komentarz został usunięty:</strong></p>";
            echo "<p><strong>Powód:</strong> " . htmlspecialchars($match[1]) . "</p>";
            echo "<p><strong>Komentarz:</strong> " . htmlspecialchars($match[2]) . "</p>";

      
        } else {
            echo "<p>" . htmlspecialchars($tresc) . "</p>";
        }
        ?>
        <span><?= $row['data_dodania'] ?></span>
    </div>
<?php endwhile; ?>



</div>
<script src="./script.js"></script>
</body>
</html>
