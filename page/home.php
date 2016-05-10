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
<?php if ($user->getPermission() === 1) { ?>
		<div class="panel panel-primary" style="margin: 10px auto;">
			<div class="panel-heading" style="padding: 5px;"><h5>Wszystkie zamówienia</h5></div>
			<div class="panel-body">
				<a href="<?= GLOBAL_ROOT ?>/order_form" class="btn btn-primary">Dodaj zamówienie/wypożyczenie</a>
				<div class="well" style="margin-top: 10px; padding: 5px;">
					<h4>Opcje wyświetlania</h4>
					<form method="post" class="form-inline">
						<div class="form-group" style="margin: 5px;">
							<label for="search_title">tytuł:</label>
							<input class="form-control" id="search_title" name="search_title" placeholder="tytuł" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_name">imię autora:</label>
							<input class="form-control" id="search_author_name" name="search_author_name" placeholder="imię" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_surname">nazwisko autora:</label>
							<input class="form-control" id="search_author_surname" name="search_author_surname" placeholder="nazwisko" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_name">imię czytelnika:</label>
							<input class="form-control" id="search_user_name" name="search_user_name" placeholder="imię" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_surname">nazwisko czytelnika:</label>
							<input class="form-control" id="search_user_surname" name="search_user_surname" placeholder="nazwisko" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_status">status:</label>
							<select name="search_status" class="form-control">
								<option>wszystkie</option>
								<option>oczekujące</option>
								<option>gotowe do odbioru</option>
								<option>anulowane (czytelnik)</option>
								<option>anulowane (bibliotekarz)</option>
								<option>oddane (zakończone)</option>
							</select>
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_order">sortowanie:</label>
							<select name="search_order" class="form-control">
								<option value="title">tytuł</option>
								<option value="title">nazwisko autora</option>
								<option value="title">ISBN</option>
								<option value="title">rok</option>
							</select>
						</div>
						<input type="submit" class="btn btn-primary" value="pokaż">
					</form>
				</div>
				<table class="table table-striped table-bordered">
					<thead>
						<th>Nr</th>
						<th>Użytkownik</th>
						<th>Tytuł</th>
						<th>Autor</th>
						<th>Status</th>
						<th>Komentarz</th>
						<th>Opcje</th>
					</thead>
<?php
$reservations = new \PS\Reservation($db);

foreach ($reservations->search('books+users') as $reservation) {
	echo '<tr>';

	echo '<td>' . $reservation['id'] . '</td>';
	echo '<td>' . $reservation['reserverName']  . ' ' . $reservation['reserverSurname'] . '</td>';
	echo '<td>' . $reservation['bookTitle'] . '</td>';

	# authors
	echo '<td>';
	$authors = new \PS\Author($db);
	$first = true;
	foreach ($authors->search('book', $reservation['book']) as $author) {
		$writer = new \PS\Writer($db);
		$writer->getDataFromDb('id', $author['writer']);
		echo ($first ? '' : ', ') . $writer->getFullName();
		unset($writer);
		$first = false;
	}
	echo '</td>';
	unset($authors);

	echo '<td>' . $reservations->getStatusName($reservation['status']) . '</td>';
	echo '<td>' . $reservation['description'] . '</td>';
	echo '<td>';
	echo '<a class="btn btn-default" href="' . GLOBAL_ROOT . '/status_form" role="button">zmień status</a>';
	echo '</td>';

	echo '</tr>';
}
?>
				</table>
			</div>
		</div>
<?php } else { ?>
<!--poniżej kod odpowiadający za tabelę z zamówieniami użytkownika-->
		<div class="panel panel-primary" style="display: inline-block; min-width: 500px; margin: 10px auto;">
			<div class="panel-heading"><h5>Twoje zamówienia</h5></div>
			<div class="panel-body">
				<table class="table table-striped table-bordered">
					<thead>
						<th>Tytuł</th>
						<th>Autor</th>
						<th>Status</th>
						<th>Komentarz</th>
						<th>Opcje</th>
					</thead>
<?php
$reservations = new \PS\Reservation($db);

foreach ($reservations->search('reserver+books', $user->getId()) as $reservation) {
	echo '<tr>';

	echo '<td>' . $reservation['bookTitle'] . '</td>';

	# authors
	echo '<td>';
	$authors = new \PS\Author($db);
	$first = true;
	foreach ($authors->search('book', $reservation['book']) as $author) {
		$writer = new \PS\Writer($db);
		$writer->getDataFromDb('id', $author['writer']);
		echo ($first ? '' : ', ') . $writer->getFullName();
		unset($writer);
		$first = false;
	}
	echo '</td>';
	unset($authors);

	echo '<td>' . $reservations->getStatusName($reservation['status']) . '</td>';
	echo '<td>' . $reservation['description'] . '</td>';
	if ($reservation['status'] == 0)
		echo '<td><a class="btn btn-default" href="#" role="button">anuluj</a></td>';
	else
		echo '<td></td>';

	echo '</tr>';
}
?>
				</table>
			</div>
		</div>
<!--zamówienia użytkownika koniec-->
<?php } ?>
	</main>
</body>
</html>
