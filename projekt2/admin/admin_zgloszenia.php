<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'] ?? null;
$profil_zdjecie = '../user.png';




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zgloszenie_id'], $_POST['odpowiedz'])) {
    $zgloszenie_id = intval($_POST['zgloszenie_id']);
    $odpowiedz = trim($_POST['odpowiedz']);

    $stmt = $conn->prepare("SELECT user_id, tresc FROM $sqltables_zgloszenia WHERE id = ?");
    $stmt->bind_param("i", $zgloszenie_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $odbiorca_id = $row['user_id'];
        $tresc_zgloszenia = $row['tresc'];

        $tresc = "Odpowiedź administratora na zgłoszenie o treści „{$tresc_zgloszenia}”: „{$odpowiedz}";
        $stmt2 = $conn->prepare("INSERT INTO $sqltables_wiadomosci_systemowe (user_id, tresc) VALUES (?, ?)");
        $stmt2->bind_param("is", $odbiorca_id, $tresc);
        $stmt2->execute();

        $stmt3 = $conn->prepare("DELETE FROM $sqltables_zgloszenia WHERE id = ?");
        $stmt3->bind_param("i", $zgloszenie_id);
        $stmt3->execute();
    }
}
$result = $conn->query("SELECT * FROM $sqltables_zgloszenia ORDER BY data_zgloszenia DESC");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>TatraTrips</title>
    <link rel="stylesheet" href="../user/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
       
        .container {
            max-width: 900px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 12px;
            background: rgba(90, 90, 90, 0.6);
            box-shadow: 0 0 18px rgba(0,0,0,0.5);
        }

        .zgloszenie {
            background-color: rgba(223, 222, 222, 0.1);
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        .zgloszenie h4 {
            margin: 0 0 10px;
            color: #9fe66d;
        }

        .zgloszenie small {
            color: #000;
        }

        textarea {
            width: 100%;
            height: 80px;
            margin-top: 10px;
            padding: 10px;
            font-size: 14px;
            border-radius: 6px;
            border: none;
            resize: vertical;
        }

        button {
            margin-top: 10px;
            padding: 8px 14px;
            border: none;
            background-color: #a7b35d;
            color: black;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #8ea041;
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
<div class="container">
    <h2>Oczekujące zgłoszenia od użytkowników</h2>

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="zgloszenie">
                <h4>Temat: <?= htmlspecialchars($row['temat']) ?></h4>
                <p><?= nl2br(htmlspecialchars($row['tresc'])) ?></p>
                <small>Data: <?= $row['data_zgloszenia'] ?> | Email: <?= htmlspecialchars($row['email']) ?></small>

                <form method="post">
                    <input type="hidden" name="zgloszenie_id" value="<?= $row['id'] ?>">
                    <textarea name="odpowiedz" placeholder="Napisz odpowiedź..." required></textarea>
                    <button type="submit">Wyślij odpowiedź</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center;">Brak zgłoszeń do wyświetlenia.</p>
    <?php endif; ?>
</div>
<script src="../user/script.js"></script>
</body>
</html>
