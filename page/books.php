<?php
global $loader;
global $db;
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	$loader->redirect('/login');
	exit();
}
?>
<!DOCTYPE html>
<html lang="<?= DEFAULT_LANG ?>">
<head>
	<title><?= DEFAULT_TITLE ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="<?= GLOBAL_ROOT ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?= GLOBAL_ROOT ?>/css/bootstrap.css">
</head>
<body>
	<main id="page">
<!--poniżej includuję belkę menu-->
<?php $loader->loadModule('inc/menu'); ?>
		<div class="panel panel-primary" style="margin: 10px auto;">
			<div class="panel-heading" style="padding: 5px;"><h5>Lista książek</h5></div>
			<div class="panel-body">
<?php if ($user->getPermission() === 1) { ?>
				<a href="<?= GLOBAL_ROOT ?>/book_form" class="btn btn-primary">Dodaj książkę</a>
<?php } ?>
				<div class="well" style="margin-top: 10px; padding: 5px;">
					<h4>Opcje wyświetlania</h4>
					<form method="post" class="form-inline">
						<div class="form-group" style="margin: 5px;">
							<label for="search_title">tytuł:</label>
							<input class="form-control" id="search_title" name="search_title" placeholder="tytuł"<?= ((isset($_POST['search_title']) and $_POST['search_title'] != '') ? ' value="' . $_POST['search_title'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_name">imię autora:</label>
							<input class="form-control" id="search_name" name="search_name" placeholder="imię"<?= ((isset($_POST['search_name']) and $_POST['search_name'] != '') ? ' value="' . $_POST['search_name'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_surname">nazwisko autora:</label>
							<input class="form-control" id="search_surname" name="search_surname" placeholder="nazwisko"<?= ((isset($_POST['search_surname']) and $_POST['search_surname'] != '') ? ' value="' . $_POST['search_surname'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_publisher">wydawnictwo:</label>
							<input class="form-control" id="search_publisher" name="search_publisher" placeholder="wydawnictwo"<?= ((isset($_POST['search_publisher']) and $_POST['search_publisher'] != '') ? ' value="' . $_POST['search_publisher'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_isbn">ISBN:</label>
							<input class="form-control" id="search_isbn" name="search_isbn" placeholder="isbn"<?= ((isset($_POST['search_isbn']) and $_POST['search_isbn'] != '') ? ' value="' . $_POST['search_isbn'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_order">sortowanie:</label>
							<select name="search_order" class="form-control">
								<option value="title">tytuł</option>
								<option value="surname"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'surname') ? ' selected' : '') ?>>nazwisko autora</option>
								<option value="isbn"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'isbn') ? ' selected' : '') ?>>ISBN</option>
								<option value="year"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'year') ? ' selected' : '') ?>>rok</option>
							</select>
						</div>
						<input type="submit" class="btn btn-primary" value="pokaż">
					</form>
				</div>
				<table class="table table-striped table-bordered" style="margin-top: 10px;">
					<thead>
						<th>Nr</th>
						<th>Tytuł</th>
						<th>Autor</th>
						<th>Wydawnictwo</th>
						<th>Dostępne<br />egzemplarze</th>
						<th>Rok<br />wydania</th>
						<th>ISBN</th>
						<th>Opcje</th>
					</thead>
<?php
$filter = [];
if (isset($_POST['search_title']) and $_POST['search_title'] != '')
	$filter['title'] = $_POST['search_title'];
if (isset($_POST['search_name']) and $_POST['search_name'] != '')
	$filter['authorsName'] = $_POST['search_name'];
if (isset($_POST['search_surname']) and $_POST['search_surname'] != '')
	$filter['authorsSurname'] = $_POST['search_surname'];
if (isset($_POST['search_publisher']) and $_POST['search_publisher'] != '')
	$filter['publisherName'] = $_POST['search_publisher'];
if (isset($_POST['search_isbn']) and $_POST['search_isbn'] != '')
	$filter['isbn'] = $_POST['search_isbn'];

$order = 0;
if (isset($_POST['search_order'])) {
	if ($_POST['search_order'] == 'surname')
		$order = 1;
	elseif ($_POST['search_order'] == 'isbn')
		$order = 2;
	elseif ($_POST['search_order'] == 'year')
		$order = 3;
}

$books = new \PS\Book($db);

foreach ($books->search('books+publishers+authors', null, $filter, $order) as $book) {
	echo '<tr>';

	echo '<td>' . $book['id'] . '</td>';
	echo '<td>' . $book['title'] . '</td>';

	# authors
	echo '<td>';
	$authorsName    = explode(',', $book['authorsName']);
	$authorsSurname = explode(',', $book['authorsSurname']);
	$first = true;
	foreach ($authorsName as $i => $item) {
		if ($first)
			$first = false;
		else
			echo ', ';
		echo $authorsName[$i] . ' ' . $authorsSurname[$i];
	}
	echo '</td>';

	echo '<td>' . $book['publisherName'] . '</td>';
	echo '<td>' . $book['availableCount'] . ' / ' . $book['totalCount'] . '</td>';
	echo '<td>' . $book['publicationYear'] . '</td>';
	echo '<td>' . $book['isbn'] . '</td>';

	echo '<td>';

	echo '<a href="' . GLOBAL_ROOT .
	'/book_details/' . $book['id'] . '-' . str_replace(' ', '_', mb_strtolower($book['title'])) .
	'" class="btn btn-default">szczegóły</a>';

	if ((int)$book['totalCount'] - (int)$book['availableCount'] > 0)
		echo ' <a href="" class="btn btn-default">zamów</a>';

	if ($user->getPermission() === 1)
		echo ' <a href="' . GLOBAL_ROOT .
		'/book_form/' . $book['id'] . '-' . str_replace(' ', '_', mb_strtolower($book['title'])) .
		'" class="btn btn-default">edytuj</a>';

	echo '</td>';

	echo '</tr>';
}
?>
				</table>
			</div>
		</div>
	</main>
</body>
</html>
