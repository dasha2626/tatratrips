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

if (isset($_POST['dezaktywuj_user']) && isset($_POST['user_id'])) {
    $stmt = $conn->prepare("UPDATE $sqltables_users SET aktywny = 0 WHERE id = ?");
    $stmt->bind_param("i", $_POST['user_id']);
    $stmt->execute();
}

if (isset($_POST['aktywuj_user']) && isset($_POST['user_id'])) {
    $stmt = $conn->prepare("UPDATE $sqltables_users SET aktywny = 1 WHERE id = ?");
    $stmt->bind_param("i", $_POST['user_id']);
    $stmt->execute();
}

if (isset($_POST['update_user']) && isset($_POST['user_id'])) {
    $new_email = $_POST['new_email'];
    $new_password = $_POST['new_password'];

    if (!empty($new_email)) {
        $stmt = $conn->prepare("UPDATE $sqltables_users SET email = ? WHERE id = ?");
        $stmt->bind_param("si", $new_email, $_POST['user_id']);
        $stmt->execute();
    }

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE $sqltables_users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $_POST['user_id']);
        $stmt->execute();
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


$users = $conn->query("SELECT u.id, u.email, k.imie, k.nazwisko, u.aktywny FROM $sqltables_users u LEFT JOIN $sqltables_klienci k ON u.id = k.user_id");

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>TatraTrips </title>
    <link rel="stylesheet" href="../user/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
    .admin-users-title {
        text-align: center;
        font-size: 26px;
        color: #000;
        margin-top: 60px;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.4);
    }

    .wrap {
        max-width: 900px;
        margin: 30px auto;
        background: rgba(255,255,255,0.1);
        border-radius: 12px;
        padding: 25px;
        backdrop-filter: blur(6px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    }
.wrap form {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    background: rgba(255, 255, 255, 0.15);
    padding: 12px;
    border-radius: 8px;
}

.wrap form input[type="text"],
.wrap form input[type="password"],
.wrap form button {
    flex: 1 1 160px; 
    min-width: 140px;
}

.wrap .del,
.wrap .add,
.wrap .save {
    flex: 0 0 auto;         
    padding: 6px 16px;
    min-width: 120px;
    text-align: center;
}


    .wrap input[type="text"] {
        flex: 1;
        padding: 6px 10px;
        border: none;
        border-radius: 6px;
        background: rgba(255,255,255,0.2);
        color: #000;
        font-size: 14px;
    }

    .wrap input[disabled] {
        cursor: not-allowed;
        color:rgb(0, 0, 0);
    }

    .wrap .del {
        background-color: #c62828;
        color: white;
        border: none;
        padding: 6px 12px;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .wrap .del:hover {
        background-color: #b71c1c;
        transform: scale(1.05);
    }

       .wrap .add {
        background-color:rgb(124, 247, 41);
        color: white;
        border: none;
        padding: 6px 12px;
        font-weight: bold;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s ease;
    }

    .wrap .add:hover {
        background-color:rgb(12, 218, 15);
        transform: scale(1.05);
    }

    .wrap input[type="password"] {
    flex: 1;
    padding: 6px 10px;
    border: none;
    border-radius: 6px;
    background: rgba(255, 255, 255, 0.2);
    color: #000;
    font-size: 14px;
    transition: background 0.3s ease;
}

.wrap input[type="password"]::placeholder {
     color: #fff;
}

.wrap input[type="password"]:focus {
    outline: none;
    background: rgba(255, 255, 255, 0.35);
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
<h2 class="admin-users-title">Zarejestrowani użytkownicy</h2>
<div class="wrap">
    <?php while($row = $users->fetch_assoc()): ?>
    <form method="post" class="row" style="margin-bottom: 10px;">
        <input type="hidden" name="user_id" value="<?= $row['id'] ?>">

        
        <input type="text" name="new_email" value="<?= htmlspecialchars($row['email']) ?>" 
               placeholder="Nowy e-mail" <?= ($row['id'] == $user_id) ? 'readonly style="background:#eee;"' : '' ?>>

        
        <?php if ($row['id'] != $user_id): ?>
            <input type="password" name="new_password" placeholder="Nowe hasło (opcjonalnie)">
        <?php else: ?>
            <input type="password" disabled placeholder="Brak dostępu">
        <?php endif; ?>


        
        <?php if ($row['id'] != $user_id): ?>
            <?php if ($row['aktywny']): ?>
                <button name="dezaktywuj_user" class="del" onclick="return confirm('Dezaktywować konto?')">Dezaktywuj</button>
            <?php else: ?>
                <button name="aktywuj_user" class="add">Aktywuj</button>
            <?php endif; ?>
            <button name="update_user" class="add" onclick="return confirm('Zaktualizować dane użytkownika?')">Zapisz zmiany</button>
        <?php endif; ?>
    </form>
    <?php endwhile; ?>
</div>
    <script src="../user/script.js"></script> 
</body>
</html>
