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
	$writer = new \PS\Writer($db);
	if ($writer->getDataFromDb('id', (int)$loader->getParams()[1]))
		$edit_mode = true;
}
?>
<!--poniżej includuję belkę menu-->
<?php $loader->loadModule('inc/menu'); ?>
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5><?= ($edit_mode ? 'Edycja autora' : 'Dodawanie autora') ?></h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">imię:</span>
						<input class="form-control" name="writer_name" placeholder="john"<?= ($edit_mode ? ' value="' . $writer->getName() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">nazwisko:</span>
						<input class="form-control" name="writer_surname" placeholder="doe"<?= ($edit_mode ? ' value="' . $writer->getSurname() . '"' : '') ?> required />
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="<?= GLOBAL_ROOT ?>/writers" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
<?php
# edit existing
if ($edit_mode) {
	if (isset($_POST['writer_name']) and isset($_POST['writer_surname'])) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['writer_name'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "imię" jest wymagane.</div>';
		}
		if ($_POST['writer_surname'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "nazwisko" jest wymagane.</div>';
		}

		if ($requirements_met) {
			# collect new data
			if ($_POST['writer_name'] != $writer->getName())
				$data['name'] = $_POST['writer_name'];
			if ($_POST['writer_surname'] != $writer->getSurname())
				$data['surname'] = $_POST['writer_surname'];

			# save data to the db
			if (empty($data)) {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały zmienione.</div>';
			} else {
				$writer->setData($data);
				if ($writer->saveDataToDb('array_keys+object_properties', $data)) {
					$_SESSION['tmp']['writer_form']['status'] = true;
					$loader->redirect('/writer_form/' . $writer->getId() . '-' . str_replace(' ', '_', mb_strtolower($writer->getName() . ' ' . $writer->getSurname())));
				} else {
					echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
				}
			}
		}
	} else {
		# print status info
		if (isset($_SESSION['tmp']['writer_form']['status'])) {
			if ($_SESSION['tmp']['writer_form']['status'])
				echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Dane zostały zapisane.</div>';
			unset($_SESSION['tmp']['writer_form']['status']);
		}
	}
# add new
} else {
	if (isset($_POST['writer_name']) and isset($_POST['writer_surname'])) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['writer_name'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "imię" jest wymagane.</div>';
		}
		if ($_POST['writer_surname'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "nazwisko" jest wymagane.</div>';
		}

		if ($requirements_met) {
			# collect new data
			$data['name']    = $_POST['writer_name'];
			$data['surname'] = $_POST['writer_surname'];

			# save data to the db
			if (empty($data)) {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały podane.</div>';
			} else {
				$writer = new \PS\Writer($db);
				$writer->setData($data);
				if ($writer->saveDataToDb('new')) {
					$_SESSION['tmp']['writer_form']['status'] = true;
					$loader->redirect('/writer_form/' . $writer->getId() . '-' . str_replace(' ', '_', mb_strtolower($writer->getName() . ' ' . $writer->getSurname())));
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
