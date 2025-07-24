<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка системы</title>
    <link rel="stylesheet" href="./styl_instalator.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
</head>
<body>
    <div id="instalator_main">
        <h2>Błąd połączenia</h2>
        <p>
        <?php
            echo $conn->connect_error;
        ?></p>
        <br><p><b>Jeśli nie instalowałeś</b> aplikacji zrób to <a href='./install.php'>tutaj</a>.</p>
    </div>
</body>
</html>