<?php

$create[] =  "CREATE TABLE `{$prefix}_klienci` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `imie` varchar(100) DEFAULT NULL,
  `nazwisko` varchar(100) DEFAULT NULL,
  `telefon` varchar(20) DEFAULT NULL,
  `adres` text DEFAULT NULL,
  `zdjecie` varchar(255) DEFAULT NULL,
  `opis` text DEFAULT NULL,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

$create[] = "CREATE TABLE `{$prefix}_komentarze` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_uzytkownika` int(11) NOT NULL,
  `id_wycieczki` int(11) NOT NULL,
  `tresc` text NOT NULL,
  `data_dodania` datetime DEFAULT current_timestamp(),
  `zatwierdzony` tinyint(1) DEFAULT 1,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

$create[] = "CREATE TABLE `{$prefix}_rezerwacje` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_uzytkownika` int(11) NOT NULL,
  `id_wycieczki` int(11) NOT NULL,
  `data_rezerwacji` datetime DEFAULT current_timestamp(),
  `status` enum('oczekująca','zatwierdzona','anulowana') DEFAULT 'oczekująca',
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"; 

$create[] = "CREATE TABLE `{$prefix}_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `password` varchar(255) DEFAULT NULL,
  `aktywny` tinyint(1) DEFAULT 1,
    PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";


$create[] = "CREATE TABLE `{$prefix}_wiadomosci_systemowe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tresc` text NOT NULL,
  `data_dodania` datetime DEFAULT current_timestamp(),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";



$create[] = "CREATE TABLE `{$prefix}_wycieczki` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nazwa` varchar(255) NOT NULL,
  `opis` text DEFAULT NULL,
  `data` date NOT NULL,
  `cena` decimal(10,2) NOT NULL,
  `dostepne_miejsca` int(11) NOT NULL,
  `status` enum('aktywna','nieaktywna') DEFAULT 'aktywna',
  `liczba_dni` int(11) DEFAULT 3,
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";


$create[] = "CREATE TABLE `{$prefix}_zgloszenia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `temat` varchar(255) DEFAULT NULL,
  `tresc` text NOT NULL,
  `data_zgloszenia` datetime DEFAULT current_timestamp(),
  PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";



