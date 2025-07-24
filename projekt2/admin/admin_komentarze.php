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






if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['zatwierdz'])) {
        $komentarz_id = $_POST['zatwierdz'];

     
        $stmt = $conn->prepare("SELECT id_uzytkownika FROM $sqltables_komentarze WHERE id = ?");
        $stmt->bind_param("i", $komentarz_id);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $id_uzytkownika = $row['id_uzytkownika'];

            $wiadomosc = "Twój komentarz został zatwierdzony przez administratora.";
            $stmt_wiad = $conn->prepare("INSERT INTO $sqltables_wiadomosci_systemowe (user_id, tresc) VALUES (?, ?)");
            $stmt_wiad->bind_param("is", $id_uzytkownika, $wiadomosc);
            $stmt_wiad->execute();
        }

       
        $stmt = $conn->prepare("UPDATE $sqltables_komentarze SET zatwierdzony = 1 WHERE id = ?");
        $stmt->bind_param("i", $komentarz_id);
        $stmt->execute();
    }

    if (isset($_POST['usun']) && isset($_POST['powod'])) {
    $komentarz_id = $_POST['usun'];
    $powod = trim($_POST['powod']);

   
    $stmt = $conn->prepare("SELECT id_uzytkownika, tresc FROM $sqltables_komentarze WHERE id = ?");
    $stmt->bind_param("i", $komentarz_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        $id_uzytkownika = $row['id_uzytkownika'];
        $tresc_komentarza = $row['tresc'];
        $wiadomosc = "Twój komentarz został usunięty przez administratora.\nPowód: „{$powod}”.\nTreść komentarza: „{$tresc_komentarza}”.";
        $stmt_w = $conn->prepare("INSERT INTO $sqltables_wiadomosci_systemowe (user_id, tresc) VALUES (?, ?)");
        $stmt_w->bind_param("is", $id_uzytkownika, $wiadomosc);
        $stmt_w->execute();
    }

    
    $stmt = $conn->prepare("DELETE FROM $sqltables_komentarze WHERE id = ?");
    $stmt->bind_param("i", $komentarz_id);
    $stmt->execute();
}

}

$sql = "SELECT DISTINCT k.id, k.tresc, k.data_dodania, k.zatwierdzony, u.email, w.nazwa 
        FROM $sqltables_komentarze k
        JOIN $sqltables_users u ON k.id_uzytkownika = u.id
        JOIN $sqltables_wycieczki w ON k.id_wycieczki = w.id
        ORDER BY k.data_dodania DESC";

$result = $conn->query($sql);
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
       .admin-header {
    text-align: center;
    font-size: 28px;
    margin-top: 80px;
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
}

.admin-table-wrapper {
    max-width: 90%;
    margin: 40px auto;
    padding: 20px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
}

table {
    width: 100%;
    border-collapse: collapse;
    color: white;
}

th {
    background-color: rgba(167,179,93,0.95);
    color: #fff;
    padding: 16px;
    font-size: 15px;
    text-align: left;
}

td {
    padding: 14px 16px;
    font-size: 14px;
    color: black;
    background-color: rgba(255, 255, 255, 0.3);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

tr:hover td {
    background-color: rgba(255, 255, 255, 0.15);
}

.admin-header {
    text-align: center;
    font-size: 30px;
    margin-top: 80px;
    margin-bottom: 20px;
    color: #fff;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.6);
}

button {
    padding: 6px 12px;
    border-radius: 6px;
    border: none;
    background-color: #a7b35d;
    color: white;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s ease;
}

button:hover {
       background-color: #c62828;
}
.approve-btn {
    background-color:  #a7b35d;
    color: white;
    font-weight: bold;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    margin-right: 8px;
    transition: 0.3s ease;
}

.approve-btn:hover {
    background-color: #388e3c;
}
.powod-input {
    padding: 6px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 13px;
    margin-right: 8px;
    background: rgba(255, 255, 255, 0.85);
    color: #333;
    width: 160px;
}

.powod-input::placeholder {
    color: #888;
    font-style: italic;
}

@media screen and (max-width: 768px) {
  .admin-header {
    font-size: 22px;
    margin-top: 50px;
  }

  .admin-table-wrapper {
    padding: 10px;
    max-width: 100%;
    overflow-x: auto;
  }

  table {
    font-size: 13px;
    min-width: 600px; 
  }

  th, td {
    padding: 10px;
  }

  .powod-input {
    width: 100%;
    margin: 8px 0;
  }

  .approve-btn,
  button {
    width: 100%;
    margin-top: 8px;
  }
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

<h2 class="admin-header">Komentarze – Panel administratora</h2>
<div class="admin-table-wrapper">
<table>
<tr>
    <th>Wycieczka</th>
    <th>Email</th>
    <th>Data</th>
    <th>Treść</th>
    <th>Status</th>
    <th>Akcje</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['nazwa']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['data_dodania']) ?></td>
    <td><?= nl2br(htmlspecialchars($row['tresc'])) ?></td>
    <td><?= $row['zatwierdzony'] ? 'zatwierdzony' : 'niezatwierdzony' ?></td>
    <td>
        <?php if (!$row['zatwierdzony']): ?>
            <form method="post" style="display:inline;">
                <button type="submit" name="zatwierdz" value="<?= $row['id'] ?>" class="approve-btn">Zatwierdź</button>

            </form>
        <?php endif; ?>
    <form method="post" class="delete-form" style="display:inline;" onsubmit="return showReasonField(this);">
    <input type="hidden" name="usun" value="<?= $row['id'] ?>">
    
    <div class="reason-wrapper" style="display: none; margin-top: 5px;">
        <input type="text" name="powod" class="powod-input" placeholder="Podaj powód" required>
        <button type="submit">Potwierdź usunięcie</button>
    </div>

    <button type="button" class="show-reason-btn">Usuń</button>
</form>





    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<script src="../user/script.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.show-reason-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const wrapper = this.parentElement.querySelector('.reason-wrapper');
            wrapper.style.display = 'block';
            this.style.display = 'none';
        });
    });
});

function showReasonField(form) {
    const input = form.querySelector('input[name="powod"]');
    if (!input || input.value.trim() === "") {
        alert("Podaj powód usunięcia komentarza.");
        return false;
    }
    return true;
}
</script>

</body>

</html>
