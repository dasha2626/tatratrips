<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation</title>
    <link rel="stylesheet" href="./styl_instalator.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
</head>
<body>
    <div id="instalator"><div id="instalator_main">
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$config_file = __DIR__ . '/config/config.php';
$step = isset($_GET['step']) ? (int)$_GET['step'] : 0;

// KROK 1 – formularz do zbierania danych
function form_install_1() {
    echo '
    <h2>Instalator :: krok: 1</h2>
    <form method="post" action="install.php?step=2">
        <label>Host bazy danych: <input type="text" name="host" required></label><br>
        <label>Nazwa bazy danych: <input type="text" name="dbname" required></label><br>
        <label>Użytkownik: <input type="text" name="user" required></label><br>
        <label>Hasło: <input type="password" name="pass"></label><br>
        <label>Prefix tabeli: <input type="text" name="prefix" required></label><br>
        <button type="submit" name="zapisz">Zapisz konfigurację</button>
    </form>';
}

// KROK 2 – zapis danych do config.php
function step2($config_file) {
    $host = $_POST['host'];
    $dbname = $_POST['dbname'];
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    $prefix = $_POST['prefix'];

    $content = "<?php\n";
    $content .= "return [\n";
    $content .= "  'host' => '$host',\n";
    $content .= "  'dbname' => '$dbname',\n";
    $content .= "  'user' => '$user',\n";
    $content .= "  'pass' => '$pass',\n";
    $content .= "  'prefix' => '$prefix'\n";
    $content .= "];?>\n";

    echo "<h2>Instalator :: krok: 2</h2>";
    if (file_put_contents($config_file, $content)) {
        echo "<p>✅ Krok 2 zakończony. Konfiguracja została zapisana.</p>";
        echo '<a href="install.php?step=3">Przejdź do kroku 3</a>';
    } else {
        echo "<p>❌ Nie udało się zapisać pliku <code>config/config.php</code>.</p>";
    }
}

// KROK 3 – tworzenie tabel
function step3($config_file) {
    echo "<h2>Instalator :: krok: 3</h2>";
    if (!file_exists($config_file)) {
        echo "Brak pliku konfiguracyjnego.";
        return;
    }

    $config = include($config_file);
    $host = $config['host'];
    $user = $config['user'];
    $pass = $config['pass'];
    $dbname = $config['dbname'];
    $prefix = $config['prefix'];

    $conn = mysqli_connect($host, $user, $pass);
    if (!$conn) {
        die("❌ Błąd połączenia z bazą danych: " . mysqli_connect_error());
    }

 
//   $conn->query("CREATE DATABASE `$dbname`");

    if (file_exists("sql/sql.php")) {
        include("sql/sql.php");
        echo "<h2>Tworzę tabele bazy: <strong>$dbname</strong></h2><div id='instalator_content'><ol>";
        $conn->select_db($dbname);
        foreach ($create as $index => $query) {
            echo "<li><pre><code>$query</code></pre></li>";
            $conn->query($query);
        }
        echo '</ol></div><p>✅ Tabele utworzone. <a href="install.php?step=4">Przejdź do kroku 4</a></p>';
    } else {
        echo "❌ Nie znaleziono pliku sql/sql.php.";
    }
}

// KROK 4 – import danych
function step4($config_file) {
    echo "<h2>Instalator :: krok: 4</h2>";
    if (!file_exists($config_file)) {
        echo "Brak pliku konfiguracyjnego.";
        return;
    }

    $config = include($config_file);
    $host = $config['host'];
    $user = $config['user'];
    $pass = $config['pass'];
    $dbname = $config['dbname'];
    $prefix = $config['prefix'];

    $link = mysqli_connect($host, $user, $pass, $dbname);
    if (!$link) {
        die("❌ Błąd połączenia: " . mysqli_connect_error());
    }

    if (file_exists("sql/insert.php")) {
        include("sql/insert.php"); // plik ustawia tablicę $insert[]
        echo "<h2>Wstawiam dane do tabel bazy: <strong>$dbname</strong></h2><div id='instalator_content'><ol>";
        mysqli_select_db($link, $dbname);
        foreach ($insert as $i => $query) {
            echo "<li><pre><code>$query</code></pre></li>";
            mysqli_query($link, $query);
        }
        echo '</ol></div><p>✅ Dane zostały zaimportowane. Instalacja zakończona. <a href="install.php?step=5">Przejdź do kroku 5</a></p>';
    } else {
        echo "❌ Nie znaleziono pliku sql/insert.php.";
    }
}



function step5() {
    echo '
    <h2>Instalator :: krok: 5</h2>
    <form method="post" action="install.php?step=6">
        <label>Nazwa aplikacji: <input type="text" name="nazwa_aplikacji" required></label><br>
        <label>Adres serwisu: <input type="text" name="base_url" required></label><br>
        <label>Data powstania: <input type="date" name="data_powstania" required></label><br>
        <label>Wersja: <input type="text" name="wersja" required></label><br>
        <label>Nazwa firmy: <input type="text" name="brand" required></label><br>
        <label>Adres firmy (ulica): <input type="text" name="adres1" required></label><br>
        <label>Adres firmy (miasto, kod): <input type="text" name="adres2" required></label><br>
        <label>Telefon: <input type="text" name="phone" required></label><br><hr>
        <h3>Konto administratora</h3>
        <label>Login administratora: <input type="text" name="admin_login" required></label><br>
        <label>Hasło administratora: <input type="password" name="passwd" required></label><br>
        <label>Powtórz hasło: <input type="password" name="passwd2" required></label><br>
        <button type="submit" name="save_admin">Zakończ instalację</button>
    </form>';
}


function step6($config_file) {
    echo "<h2>Instalator :: krok: 6</h2>";
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_admin'])) {
        // WALIDACJA HASEŁ
        if ($_POST['passwd'] !== $_POST['passwd2']) {
            echo "<p>❌ Hasła się nie zgadzają.</p>";
            echo '<a href="install.php?step=5">Wróć</a>';
            exit;
        }

        // Zapis konfiguracji do pliku
        $config = "\n<?php\n# konfiguracja aplikacji\n";
        $config .= "\$base_url=\"".$_POST['base_url']."\";\n";
        $config .= "\$nazwa_aplikacji=\"".$_POST['nazwa_aplikacji']."\";\n";
        $config .= "\$data_powstania=\"".$_POST['data_powstania']."\";\n";
        $config .= "\$wersja=\"".$_POST['wersja']."\";\n";
        $config .= "\$brand=\"".$_POST['brand']."\";\n";
        $config .= "\$adres1=\"".$_POST['adres1']."\";\n";
        $config .= "\$adres2=\"".$_POST['adres2']."\";\n";
        $config .= "\$phone=\"".$_POST['phone']."\";\n";
        $config .= "\$img_footer=\"".$_POST['base_url']."img/kashyyyk.jpg\";\n?>";

        if (is_writable($config_file)) {
            $uchwyt = fopen($config_file, 'a');
            fwrite($uchwyt, $config);
            fclose($uchwyt);
            echo "<p>✅ Konfiguracja została zapisana.</p>";
        } else {
            echo "<p>❌ Plik $config_file nie jest zapisywalny.</p>";
        }

        // Zapis administratora do bazy
        $config = include($config_file);
        $prefix = $config['prefix'];
        $link = mysqli_connect($config['host'], $config['user'], $config['pass'], $config['dbname']);

        $insert = [];
        $insert[] = "INSERT INTO `{$prefix}_users` (`id`, `email`, `role`, `created_at`, `password`) VALUES (1, '".$_POST['admin_login']."', 'admin', NOW(), '".password_hash($_POST['passwd'], PASSWORD_DEFAULT)."');";
        echo "<div id='instalator_content'><ol>";
        foreach ($insert as $i => $sql) {
            echo "<li><pre><code>$sql</code></pre></li>";
            mysqli_query($link, $sql);
        }
        echo "</ol></div>";

        echo "
        <h2>Instalacja zakończona!</h2>Przejdź do serwisu <a href='index.php'>" . $_POST["nazwa_aplikacji"] . "</a>, który powstał " . $_POST['data_powstania'] . "
        ";
    }
}

// Główna logika kontrolera
switch ($step) {
    case 2:
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zapisz'])) {
            step2($config_file);
        }
        break;

    case 3:
        step3($config_file);
        break;

    case 4:
        step4($config_file);
        break;
    case 5: 
        step5();
        break;

    case 6:
        step6($config_file);
        break;

    default:
        // KROK 0 – utwórz plik, jeśli nie istnieje
        if (file_exists($config_file)) {
            if (is_writable($config_file)) {
                form_install_1(); // wyświetl formularz
            } else {
                echo "<p>🛑 Zmień uprawnienia do pliku <code>$config_file</code><br>np. <code>chmod o+w $config_file</code></p>";
                echo '<button onclick="location.reload()">Odśwież stronę</button>';
            }
        } else {
            echo "<p>🔧 Stwórz plik <code>$config_file</code><br>np. <code>touch config/config.php</code></p>";
            echo '
            <form method="post"><button name="create_empty">Utwórz pusty plik</button></form>';
            if (isset($_POST['create_empty'])) {
                if (!is_dir(__DIR__ . '/config')) mkdir(__DIR__ . '/config');
                if (touch($config_file)) {
                    echo "<p>✅ Plik został utworzony. Odśwież stronę.</p>";
                } else {
                    echo "<p>❌ Nie udało się utworzyć pliku. Sprawdź uprawnienia.</p>";
                }
            }
        }
        break;
}
?>
    </div></div>
</body>
</html>