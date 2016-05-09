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
		<div class="panel panel-primary" style="display: inline-block; min-width: 500px; margin: 10px auto;">
			<div class="panel-heading"><h5>Wszystkie zamówienia</h5></div>
			<div class="panel-body">
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
	echo '<td></td>';

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
