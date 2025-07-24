 <?php 
session_start();
require 'db.php';
$pokaz_alert = false;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header("Location: ./admin/admin_wycieczki.php");
    exit();
}



if (isset($_SESSION['rezerwacja_udana'])) {
    $pokaz_alert = true;
    unset($_SESSION['rezerwacja_udana']);
}

$pokaz_alert_zgloszenie = false;
if (isset($_SESSION['zgloszenie_wyslane'])) {
    $pokaz_alert_zgloszenie = true;
    unset($_SESSION['zgloszenie_wyslane']);
}



if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['email'] = $_COOKIE['email'];
    

    if (!isset($_SESSION['role'])) {
    $stmt = $conn->prepare("SELECT role FROM $sqltables_users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $_SESSION['role'] = $row['role'];
    }
}

}


$email = isset($_SESSION['email']) ? $_SESSION['email'] : null;

$profil_zdjecie = './user/user.png'; 

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT zdjecie FROM $sqltables_klienci WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

   
   if ($row = $result->fetch_assoc()) {
        // tylko jeśli plik istnieje fizycznie
       if (!empty($row['zdjecie']) && file_exists(__DIR__ . '/./uploads/' . $row['zdjecie'])) {
            $profil_zdjecie = './uploads/' . $row['zdjecie'];
        }
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
    <link rel="stylesheet" href="./user/style.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
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
    <div class="header-right"></div>

    <?php if ($email): ?>
        <div class="user-menu">
            <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User" class="user-pic" onclick="toggleMenu()">
            <div class="sub-menu-wrap" id="subMenu">
                <div class="sub-menu">
                    <div class="user-info">
                        <img src="<?= htmlspecialchars($profil_zdjecie) ?>" alt="User">
                        <h3><?php echo htmlspecialchars($email); ?></h3>
                    </div>
                    <hr>
                     <a href="./profil.php" class="sub-menu-link">
                <p><i class='bx bxs-id-card'></i>Mój profil</p>
                     </a>
                    <a href="./mojerezerwacje.php" class="sub-menu-link">
                        <p><i class='bx bxs-cool'></i>Moje rezerwacje</p>
                    </a>
                    <a href="./logout.php" class="sub-menu-link">
                        <p><i class='bx bx-log-out'></i>Wyloguj się</p>
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="guest-menu">
            <a href="login.html" class="login-link">Zaloguj się</a>
        </div>
    <?php endif; ?>
</header>



<div class="background"></div>

<section class="home" id="home">
    <div class="content">
        <h3>TatraTrips</h3>
        <span> Odkryj piękno Tatr i ciesz się niezapomnianymi widokami!</span>
        <p>Witaj w TatraTrips – Twoim przewodniku po polskich Tatrach! Znajdziesz tutaj najlepsze oferty na spontaniczne wycieczki w sercu gór. Wystarczy kilka kliknięć, aby zaplanować swoją przygodę w Tatrach i zarezerwować wycieczkę, która spełni Twoje marzenia.</p>
        <a href="#about" class="btn">Zaplanuj swoją wyprawę</a>
    </div>
</section>

   <div id="about" class="container swiper">
      <div class="brush-text-wrapper">
        <div class="brush-text">
                   NA CZYM POLEGA NASZA WYJĄTKOWOŚĆ?
        </div>
     </div>
        <div class="slider-wrapper">
            <div class="card-list swiper-wrapper">

                <div class= "card-item swiper-slide">
                    <img src="./images/tatry1.jpg" alt="" class="user-image">
                    <h2 class="user-name">🏔️ Wyprawy skrojone na miarę</h2>
                    <p class="user-profession">Szukasz relaksującego spaceru doliną? A może zdobycia Rysów? Dzięki naszym dopasowanym ofertom znajdziesz wycieczkę idealną dla siebie – bez zbędnego szukania i stresu.</p>
                </div>
   
        
                <div class= "card-item swiper-slide">
                    <img src="./images/tatry2.jpg" alt="" class="user-image">
                    <h2 class="user-name">🌤️ Zawsze trafiona pogoda i dobry termin</h2>
                    <p class="user-profession">Nie musisz już sprawdzać miliona prognoz. U nas zobaczysz aktualną pogodę w Tatrach i kalendarz wycieczek – wybierz dzień, który naprawdę Ci odpowiada.</p>
                </div>
    
                <div class= "card-item swiper-slide">
                    <img src="./images/tatry4.jpg" alt="" class="user-image">
                    <h2 class="user-name">📅 Pełna kontrola nad planem podróży</h2>
                    <p class="user-profession">Zarezerwuj miejsce, sprawdź szczegóły wyprawy i zarządzaj swoim górskim planem – wszystko w jednym miejscu, prosto i wygodnie</p>
                </div>
        
                <div class= "card-item swiper-slide">
                    <img src="./images/tatry3.jpg" alt="" class="user-image">
                    <h2 class="user-name">📸 Tatry w najlepszym wydaniu</h2>
                    <p class="user-profession">Przeglądaj zdjęcia tras, poznaj opisy szlaków i zobacz, co czeka na Ciebie za zakrętem – zanim jeszcze wyruszysz.</p>
                </div>

        
   
                <div class= "card-item swiper-slide">
                    <img src="./images/tatry5.jpg" alt="" class="user-image">
                    <h2 class="user-name">⭐ Sprawdzone opinie i rekomendacje</h2>
                    <p class="user-profession">Zanim wybierzesz się w góry, możesz zobaczyć, co sądzą o wyprawie inni turyści. A po powrocie – podziel się swoją opinią i pomóż innym wybrać najlepiej.</p>
                </div>

            </div>
        <div class="swiper-slide-button swiper-button-prev"></div>
        <div class="swiper-slide-button swiper-button-next"></div>
        </div>
    </div>

            <div class="brush-text-wrapper">
          <div class="brush-text">NASZE WYCIECZKI</div>
            </div>
<div class="trip-cards-wrapper">
  <!-- 3 DNI -->
  <div class="tour-box yellow">
    <div class="tour-title">TUR - 3 DNI / 2 NOCE</div>
    <div class="tour-price">od 700 zł</div>
    <div class="tour-buttons">
      <a href="./terminy.php?dni=3" class="tour-btn">Zobacz terminy</a>
      <button class="tour-btn plan-btn" onclick="openModal('plan3')">📋 Czytaj plan</button>
    </div>
  </div>

  <!-- 4 DNI -->
  <div class="tour-box green">
    <div class="tour-title">TUR - 4 DNI / 3 NOCE</div>
    <div class="tour-price">od 800 zł</div>
    <div class="tour-buttons">
      <a href="./terminy.php?dni=4" class="tour-btn">Zobacz terminy</a>
      <button class="tour-btn plan-btn" onclick="openModal('plan4')">📋 Czytaj plan</button>
    </div>
  </div>

  <!-- 6 DNI -->
  <div class="tour-box blue">
    <div class="tour-title">TUR - 6 DNI / 5 NOCY</div>
    <div class="tour-price">1000 zł</div>
    <div class="tour-buttons">
      <a href="./terminy.php?dni=6" class="tour-btn">Zobacz terminy</a>
      <button class="tour-btn plan-btn" onclick="openModal('plan6')">📋 Czytaj plan</button>
    </div>
  </div>
</div>


<div class="modal" id="plan3">
  <div class="modal-content">
    <span class="close" onclick="closeModal('plan3')">&times;</span>
    <h2>Plan wycieczki – 3 dni</h2>
    <p>🗓️ Dzień 1: Przejazd, zakwaterowanie, spacer</p>
    <p>🗓️ Dzień 2: Wyprawa górska z przewodnikiem</p>
    <p>🗓️ Dzień 3: Śniadanie i powrót</p>
  </div>
</div>

<div class="modal" id="plan4">
  <div class="modal-content">
    <span class="close" onclick="closeModal('plan4')">&times;</span>
    <h2>Plan wycieczki – 4 dni</h2>
    <p>Dzień 1: Przyjazd, integracja</p>
    <p>Dzień 2: Zwiedzanie dolin</p>
    <p>Dzień 3: Górska wyprawa + ognisko</p>
    <p>Dzień 4: Powrót</p>
  </div>
</div>

<div class="modal" id="plan6">
  <div class="modal-content">
    <span class="close" onclick="closeModal('plan6')">&times;</span>
    <h2>Plan wycieczki – 6 dni</h2>
    <p>Dzień 1–2: Doliny i jeziora</p>
    <p>Dzień 3–4: Wspinaczki, zajęcia sportowe</p>
    <p>Dzień 5: Relaks i piknik</p>
    <p>Dzień 6: Wyjazd</p>
  </div>
</div>

</section>
 <section id="weather" class="weather-section">
    <h3>Ładowanie pogody...</h3>
</section>


    


<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="./user/script.js"></script>
<script>
const apiKey = '50987805170f3dad14664bdd6372baed';
const city = 'Zakopane';

fetch(`https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric&lang=pl`)
  .then(response => response.json())
  .then(data => {
    const weatherContainer = document.getElementById('weather');
    const temp = data.main.temp;
    const description = data.weather[0].description;
    const icon = data.weather[0].icon;

    weatherContainer.innerHTML = `
      <h3>Aktualna pogoda w Tatrach (Zakopane):</h3>
      <p><img src="https://openweathermap.org/img/wn/${icon}.png" alt="${description}"> 
      ${description}, ${temp}°C</p>
    `;
  })
  .catch(error => {
    document.getElementById('weather').innerHTML = `<p>Nie udało się załadować pogody 😢</p>`;
    console.error('Błąd:', error);
  });
</script>

<?php if ($pokaz_alert): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Rezerwacja zapisana!',
            text: 'Dziękujemy za dokonanie rezerwacji.',
            confirmButtonText: 'OK'
        });
    </script>
<?php endif; ?> 

<?php if ($pokaz_alert_zgloszenie): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Zgłoszenie wysłane!',
        text: 'Dziękujemy za kontakt z administracją.',
        confirmButtonText: 'OK'
    });
</script>
<?php endif; ?>

</body>
</html> 