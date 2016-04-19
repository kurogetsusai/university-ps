# Projekt Systemu – Biblioteka

## Instalacja

Wsadź wszystkie pliki do `/var/www/html`, czy gdzie tam masz folder Apache, zaimportuj `setup-db.sql` do bazy i już.

## Wymagania

- PHP 5.4+
- mod Apache: `rewrite`
- plik `.htaccess`, upewnij się, że twój serwer pozwala na przepisywanie URL (sprawdź plik `/etc/apache2/apache2.conf` czy `<Directory /var/www/>` ma `AllowOverride All`)
- baza MySQL z zaimportowanym skryptem `setup-db.sql`

## Do zrobienia

- Backend
	- [x] SQL setup
	- [x] libloader
	- [x] libdatabase
	- [ ] libuser
	- [ ] libtown
	- [ ] libbook
	- [ ] libwriter
	- [ ] libauthor ???
	- [ ] libreservation
- Frontend

	- > możesz sobie tu zrobić jakąś listę jak chcesz, tak tylko na oko napisałem co bedzie potrzebne ;p  
	  > a skoro system ma być dostępny tylko dla zalogowanych, to możesz na stronie głównej już założyć, że user jest zalogowany, a niezalogowany będzie dostawał przekierowanie na stronę logowania i tyle

	- [ ] strona główna
	- [ ] strona logowania
	- [ ] strona rejestracji użytkownika

## Foldery

- css – CSS
- etc – config i różne śmieci
- inc – fragmenty HTML, które będą gdzieś includowane
- lib – biblioteki PHP
- page – podstrony
