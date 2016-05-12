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
<?php
$edit_mode = false;
if (isset($loader->getParams()[1])) {
	$publisher = new \PS\Publisher($db);
	if ($publisher->getDataFromDb('id', (int)$loader->getParams()[1]))
		$edit_mode = true;
}
?>
<!--poniżej includuję belkę menu-->
<?php $loader->loadModule('inc/menu'); ?>
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5><?= ($edit_mode ? 'Edycja wydawnictwa' : 'Dodawanie wydawnictwa') ?></h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">nazwa:</span>
						<input class="form-control" name="publisher_name" placeholder="nazwa"<?= ($edit_mode ? ' value="' . $publisher->getName() . '"' : '') ?> required />
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="<?= GLOBAL_ROOT ?>/publishers" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
<?php
# edit existing
if ($edit_mode) {
	if (isset($_POST['publisher_name'])) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['publisher_name'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "nazwa" jest wymagane.</div>';
		}

		if ($requirements_met) {
			# collect new data
			if ($_POST['publisher_name'] != $publisher->getName())
				$data['name'] = $_POST['publisher_name'];

			# save data to the db
			if (empty($data)) {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały zmienione.</div>';
			} else {
				$publisher->setData($data);
				if ($publisher->saveDataToDb('array_keys+object_properties', $data)) {
					$_SESSION['tmp']['publisher_form']['status'] = true;
					$loader->redirect('/publisher_form/' . $publisher->getId() . '-' . str_replace(' ', '_', mb_strtolower($publisher->getName())));
				} else {
					echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
				}
			}
		}
	} else {
		# print status info
		if (isset($_SESSION['tmp']['publisher_form']['status'])) {
			if ($_SESSION['tmp']['publisher_form']['status'])
				echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Dane zostały zapisane.</div>';
			unset($_SESSION['tmp']['publisher_form']['status']);
		}
	}
# add new
} else {
	if (isset($_POST['publisher_name'])) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['publisher_name'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "nazwa" jest wymagane.</div>';
		}

		if ($requirements_met) {
			# collect new data
			$data['name'] = $_POST['publisher_name'];

			# save data to the db
			if (empty($data)) {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały podane.</div>';
			} else {
				$publisher = new \PS\Publisher($db);
				$publisher->setData($data);
				if ($publisher->saveDataToDb('new')) {
					$_SESSION['tmp']['publisher_form']['status'] = true;
					$loader->redirect('/publisher_form/' . $publisher->getId() . '-' . str_replace(' ', '_', mb_strtolower($publisher->getName())));
				} else {
					echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
				}
			}
		}
	}
}
?>
	</main>
</body>
</html>
