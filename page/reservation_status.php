<?php
global $loader;
global $db;
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	$loader->redirect('/login');
	exit();
}

# edit mode only
if (!isset($loader->getParams()[1])) {
	$loader->redirect('/');
	exit();
}

$reservation = new \PS\Reservation($db);
if (!$reservation->getDataFromDb('id', (int)$loader->getParams()[1])) {
	$loader->redirect('/');
	exit();
}

if ($user->getPermission() !== 1) {
	$data = [];
	$data['status'] = 1;

	# save data to the db
	$reservation->setData($data);
	$reservation->saveDataToDb('array_keys+object_properties', $data);
	$loader->redirect('/');
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
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5>Zmiana statusu zamówienia nr 6</h5></div>
			<div class="panel-body">
<?php
$u = new \PS\User($db);
$u->getDataFromDb('id', $reservation->getReserver());
echo 'Użytkownik: ' . $u->getFullName() . ' (' . $u->getPesel() . ')<br />';

$book = new \PS\Book($db);
$book->getDataFromDb('id', $reservation->getBook());
# authors
$book_authors = '';
$authors = new \PS\Author($db);
$first = true;
foreach ($authors->search('book', $book->getId()) as $author) {
	$writer = new \PS\Writer($db);
	$writer->getDataFromDb('id', $author['writer']);
	$book_authors .= ($first ? '' : ', ') . $writer->getFullName();
	unset($writer);
	$first = false;
}
unset($authors);
echo 'Książka: ' . $book->getTitle() . ' (' . $book_authors . ')';
?>
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">komentarz:</span>
						<textarea class="form-control" name="reservation_desc" placeholder="opis"><?= $reservation->getDescription() ?></textarea>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">status:</span>
						<select name="reservation_status" class="form-control">
<?php
for ($i = 0; $i < 6; ++$i)
	echo '<option value="' . $i . '"' . ($reservation->getStatus() === $i ? ' selected' : '') . '>' . $reservation->getStatusName($i) . '</option>';
?>
						</select>
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="<?= GLOBAL_ROOT ?>/" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
<?php
if (
	isset($_POST['reservation_desc']) &&
	isset($_POST['reservation_status'])
) {
	$data = [];

	# check required input
	$requirements_met = true;
	if ($_POST['reservation_status'] == '') {
		$requirements_met = false;
		echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "status" jest wymagane.</div>';
	}

	if ($requirements_met) {
		# collect new data
		if ($_POST['reservation_status'] != $reservation->getStatus())
			$data['status'] = $_POST['reservation_status'];
		if ($_POST['reservation_desc'] != $reservation->getDescription())
			$data['description'] = $_POST['reservation_desc'];

		# save data to the db
		if (empty($data)) {
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały podane.</div>';
		} else {
			$reservation->setData($data);
			if ($reservation->saveDataToDb('array_keys+object_properties', $data)) {
				$_SESSION['tmp']['reservation_form']['status'] = true;
				$loader->redirect('/reservation_status/' . $reservation->getId());
			} else {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
			}
		}
	}
} else {
	# print status info
	if (isset($_SESSION['tmp']['reservation_form']['status'])) {
		if ($_SESSION['tmp']['reservation_form']['status'])
			echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Dane zostały zapisane.</div>';
		unset($_SESSION['tmp']['reservation_form']['status']);
	}
}
?>
	</main>
</body>
</html>
