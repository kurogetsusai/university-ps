# Projekt Systemu – Biblioteka

## Instalacja

Wsadź wszystkie pliki do `/var/www/html`, czy gdzie tam masz folder Apache, zaimportuj `setup-db.sql` do bazy i już.

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
- gfx – ?
- inc – fragmenty HTML, które będą gdzieś includowane
- js – JavaScript
- lib – biblioteki PHP
- page – podstrony

## Do zrobienia

- Backend
	- [x] SQL setup
	- [x] libloader
	- [x] libdatabase
	- [x] libuser
	- [x] libbook
	- [x] libpublisher
	- [x] libwriter
	- [x] libauthor
	- [x] libreservation
	- [x] page/home
	- [x] page/login
	- [x] page/logout
	- [x] page/password
	- [x] page/publishers
	- [x] page/users
	- [x] page/writers
- Frontend

	- > możesz sobie tu zrobić jakąś listę jak chcesz, tak tylko na oko napisałem co bedzie potrzebne ;p  
	  > a skoro system ma być dostępny tylko dla zalogowanych, to możesz na stronie głównej już założyć, że user jest zalogowany, a niezalogowany będzie dostawał przekierowanie na stronę logowania i tyle

	- [ ] strona główna
	- [ ] strona logowania
	- [ ] strona rejestracji użytkownika
