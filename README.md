# Projekt Systemu – Biblioteka

## Instalacja

Wsadź wszystkie pliki do publicznego folderu Apache (`/var/www/html`) i zaimportuj `setup-db.sql` do bazy.

## Wymagania

- PHP 5.4+
- mod Apache: `rewrite`
- plik `.htaccess`, upewnij się, że twój serwer pozwala na przepisywanie URL (sprawdź plik `/etc/apache2/apache2.conf` czy `<Directory /var/www/>` ma `AllowOverride All`)
- baza MySQL z zaimportowanym skryptem `setup-db.sql`

## Foldery

- css – CSS
- doc – dokumentacja
- etc – config i różne śmieci
- fonts – fonty
- gfx – grafika
- inc – fragmenty HTML, które będą gdzieś includowane
- js – JavaScript
- lib – biblioteki PHP
- page – podstrony

