CREATE DATABASE IF NOT EXISTS library DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
GRANT ALL PRIVILEGES ON library.* TO 'library'@'localhost' IDENTIFIED BY 'V!OSvw^4QMY:Q4F+G';
USE library;

CREATE TABLE IF NOT EXISTS user (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	pesel VARCHAR(11) NOT NULL,
	password VARCHAR(255) NOT NULL,
	name VARCHAR(32) NOT NULL,
	surname VARCHAR(32) NOT NULL,
	town VARCHAR(64) NOT NULL,
	postCode VARCHAR(5) NOT NULL,
	street VARCHAR(32) NOT NULL,
	houseNumber VARCHAR(10) NOT NULL,
	permission TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS publisher (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(64) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS book (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	isbn VARCHAR(32) NOT NULL,
	title VARCHAR(128) NOT NULL,
	publicationYear VARCHAR(4) NOT NULL,
	publisher INT UNSIGNED NOT NULL,
	count INT UNSIGNED NOT NULL,
	description VARCHAR(512),
	FOREIGN KEY (publisher) REFERENCES publisher(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS writer (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	name VARCHAR(32) NOT NULL,
	surname VARCHAR(32) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS author (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	book INT UNSIGNED NOT NULL,
	writer INT UNSIGNED NOT NULL,
	FOREIGN KEY (book) REFERENCES book(id),
	FOREIGN KEY (writer) REFERENCES writer(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservation (
	id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	reserver INT UNSIGNED NOT NULL,
	book INT UNSIGNED NOT NULL,
	status TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
	description VARCHAR(512),
	FOREIGN KEY (reserver) REFERENCES user(id),
	FOREIGN KEY (book) REFERENCES book(id)
) ENGINE=InnoDB;

-- Some random data to test things
INSERT IGNORE INTO user (id, pesel, password, name, surname, town, postCode, street, houseNumber, permission) VALUES
(1, '0', '$2y$11$imUZili8Mlb4cGuH2rel/eWJ9sG1a/O6Nd.p944hh1NpOmvzqCha6', 'Charlie', 'Root', 'Debug Valley', '00000', 'Test', '1', 1),
(2, '1', '$2y$11$/ArvUc3R1RqjRh8k4Ptuxe6sZuzvg36FGx0geSvwVhrwdvsOSfgjG', 'Lame', 'User', 'Debug Valley', '00000', 'Test', '2', 0),
(3, '2', '$2y$11$/ArvUc3R1RqjRh8k4Ptuxe6sZuzvg36FGx0geSvwVhrwdvsOSfgjG', 'Pro', 'User', 'Debug Valley', '00000', 'Test', '3', 0);
INSERT IGNORE INTO publisher (id, name) VALUES
(1, 'Wydawnictwo Test'),
(2, 'Super książki');
INSERT IGNORE INTO writer (id, name, surname) VALUES
(1, 'Terry', 'Pratchett'),
(2, 'Władysław', 'Łoziński'),
(3, 'Wojciech', 'Kuczok'),
(4, 'Francek', 'Buła'),
(5, 'Achim', 'Gelynder');
INSERT IGNORE INTO book (id, isbn, title, publicationYear, publisher, count, description) VALUES
(1, '00000000001', 'Nocna Straż', 2020, 1, 0, ''),
(2, '00000000002', 'Prawem i Lewem', 1994, 1, 0, ''),
(3, '00000000003', 'Gnój, czyli antybiografia', 2008, 1, 5, ''),
(4, '00000000004', '101 sposobów na sianie marchewek', 2222, 2, 2, 'Posiej se marchewki :D'),
(5, '00000000005', 'Poradnik kopania leżącego', 1876, 2, 0, ''),
(6, '00000000006', 'Jak dobrze pisać w Javie', 2020, 1, 0, 'Ta książka to 100 pustych stron');
INSERT IGNORE INTO author (id, book, writer) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3),
(4, 4, 4),
(5, 5, 4),
(6, 5, 5),
(7, 6, 1);
INSERT IGNORE INTO reservation (id, reserver, book, status, description) VALUES
(1, 2, 1, 0, 'hello world'),
(2, 2, 2, 1, 'all work and no play makes jack a dull boy'),
(3, 2, 5, 2, 'lorem ipsum'),
(4, 3, 6, 0, '???');

