<?php
$config = include(__DIR__ . '/config/config.php');

$sqltables_users = "`" . $config["prefix"] . "_users" . "`";
$sqltables_klienci = "`" .$config["prefix"] . "_klienci". "`";
$sqltables_komentarze = "`" .$config["prefix"] . "_komentarze" . "`";
$sqltables_rezerwacje = "`" .$config["prefix"] . "_rezerwacje". "`";
$sqltables_wiadomosci_systemowe = "`" .$config["prefix"] . "_wiadomosci_systemowe". "`";
$sqltables_wycieczki = "`" .$config["prefix"] . "_wycieczki". "`";
$sqltables_zgloszenia = "`" .$config["prefix"] . "_zgloszenia". "`";

$conn = new mysqli($config["host"], $config["user"], $config["pass"], $config['dbname']);

if (!$conn || $conn->connect_error) {

    include("./please_install.php");
    die();
}
?>