
# TatraTrips
TatraTrips to aplikacja internetowa umożliwiająca rezerwację wycieczek w polskie Tatry. Projekt zapewnia użytkownikom przegląd ofert, możliwość składania rezerwacji, kontaktu z administracją oraz przegląd komentarzy i prognozy pogody.


## Wymagania systemowe
* Apache/2.4.41 (Ubuntu)
* PHP Version 8.1
* 10.3.39-MariaDB-0ubuntu0.20.04.2 - Ubuntu 20.04

## Instalacja
1. Przesłane pliki zamieścić na serwerze Manticore poprzez program „WinSCP”. 
2. Otworzyć stronę https://www.manticore.uni.lodz.pl/~shapovd/projekt/ 
3. Aplikacja samodzielnie sprawdzi swój stan oraz przejdzie do instalacji. Od tego momentu wystarczy aby użytkownik podążał za instrukcjami instalatora.
4. Zmień uprawnienia dla pliku db_connection.php np. chmod o+w db_connection.php a następnie odśwież stronę np. poprzez przycisk „F5”. 
5. Uzupełnij formularz wprowadzając odpowiednie dane. 
a. Nazwa lub adres serwera – informacje uzyskiwane u administratora serwera (w ramach tworzenia aplikacji używany był localhost). 
b. Nazwa bazy danych – z phpMyAdmin. 
c. Nazwa użytkownika -  z phpMyAdmin. 
d. Hasło – z phpMyAdmin , powiązane z nazwą użytkownika. 
6. Przy prawidłowym podaniu danych instalator w krokach 3-5 będzie zakulisowo tworzył plik konfiguracyjny, strukturę oraz wstawiał dane. Wystarczy klikać przyciski z nazwami odpowiednich kroków aż zostanie przeniesiony do kroku 5.
 7. Instalator wyświetli formularz tworzenia pierwszego konta czyli konta administratorskiego. 
 8. Na etapie kroku 7 instalacja jest ukończona. Aby przenieść się do działającej strony można wykorzystać link z pkt 2 lub kliknąć w link instalatora z ukazaną wcześniej nazwą serwisu. 
 9. Zmień prawa dostępu do db_connection.php np. chmod o-w db_connection.php oraz prawa dostępu do katalogu uploads, images np. chmod o+rwx images. Gdy będziesz pewny że aplikacja działa usuń install.php np. rm install.php



## Wykorzystane zewnętrzne biblioteki

* Boxicons 
* Font Awesome 
* SwiperJS 
* SweetAlert2 
* OpenWeatherMap API
