<?php
global $loader;
global $db;
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	$loader->redirect('/login');
	exit();
}

# entry only for admins
if ($user->getPermission() !== 1) {
	$loader->redirect('/');
	exit();
}

$reservation = new \PS\Reservation($db);
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
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5>Dodawanie zamówienia/wypożyczenia</h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">użytkownik:</span>
						<select name="reservation_user" class="form-control">
<?php
$users = new \PS\User($db);
foreach($users->search('plain') as $u) {
	echo '<option value="' . $u['id'] . '">' . $u['name'] . ' ' . $u['surname']  . ' (' . $u['pesel'] . ')</option>';
}
unset($users);
?>
						</select>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">książka:</span>
						<select name="reservation_book" class="form-control">
<?php
$books = new \PS\Book($db);
foreach($books->search('plain:available') as $book) {
	# authors
	$book_authors = '';
	$authors = new \PS\Author($db);
	$first = true;
	foreach ($authors->search('book', $book['id']) as $author) {
		$writer = new \PS\Writer($db);
		$writer->getDataFromDb('id', $author['writer']);
		$book_authors .= ($first ? '' : ', ') . $writer->getFullName();
		unset($writer);
		$first = false;
	}
	unset($authors);

	echo '<option value="' . $book['id'] . '">' . $book['title'] . ' (' . $book_authors . ')</option>';
}
unset($books);
?>
						</select>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">komentarz:</span>
						<textarea class="form-control" name="reservation_desc" placeholder="opis"></textarea>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">status:</span>
						<select name="reservation_status" class="form-control">
							<option value="0"><?= $reservation->getStatusName(0) ?></option>
							<option value="4"><?= $reservation->getStatusName(4) ?></option>
						</select>
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="<?= GLOBAL_ROOT ?>/" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
<?php
if (
	isset($_POST['reservation_user']) &&
	isset($_POST['reservation_book']) &&
	isset($_POST['reservation_desc']) &&
	isset($_POST['reservation_status'])
) {
	$data = [];

	# check required input
	$requirements_met = true;
	if ($_POST['reservation_user'] == '') {
		$requirements_met = false;
		echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "użytkownik" jest wymagane.</div>';
	}
	if ($_POST['reservation_book'] == '') {
		$requirements_met = false;
		echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "książka" jest wymagane.</div>';
	}
	if ($_POST['reservation_status'] == '') {
		$requirements_met = false;
		echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "status" jest wymagane.</div>';
	}

	if ($requirements_met) {
		# collect new data
		$data['reserver']    = $_POST['reservation_user'];
		$data['book']        = $_POST['reservation_book'];
		$data['status']      = $_POST['reservation_status'];
		$data['description'] = $_POST['reservation_desc'];

		# save data to the db
		if (empty($data)) {
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały podane.</div>';
		} else {
			$reservation->setData($data);
			if ($reservation->saveDataToDb('new')) {
				$_SESSION['tmp']['reservation_form']['status'] = true;
				$loader->redirect('/');
			} else {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
			}
		}
	}
}
?>
	</main>
</body>
</html>
