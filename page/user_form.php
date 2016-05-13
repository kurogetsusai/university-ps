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
	$u = new \PS\User($db);
	if ($u->getDataFromDb('id', (int)$loader->getParams()[1]))
		$edit_mode = true;
}
?>
<!--poniżej includuję belkę menu-->
<?php $loader->loadModule('inc/menu'); ?>
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5><?= ($edit_mode ? 'Edycja użytkownika' : 'Dodawanie użytkownika') ?></h5></div>
<?php
# edit existing
if ($edit_mode) {
	if (
		isset($_POST['user_pesel']) and
		isset($_POST['user_password1']) and
		isset($_POST['user_password2']) and
		isset($_POST['user_name']) and
		isset($_POST['user_surname']) and
		isset($_POST['user_email']) and
		isset($_POST['user_phone']) and
		isset($_POST['user_town']) and
		isset($_POST['user_code']) and
		isset($_POST['user_street']) and
		isset($_POST['user_houseNumber']) and
		isset($_POST['user_permission'])
	) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['user_pesel'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "PESEL" jest wymagane.</div>';
		}
		if ($_POST['user_name'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "imię" jest wymagane.</div>';
		}
		if ($_POST['user_surname'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "nazwisko" jest wymagane.</div>';
		}
		if ($_POST['user_town'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "miasto" jest wymagane.</div>';
		}
		if ($_POST['user_code'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "kod pocztowy" jest wymagane.</div>';
		}
		if ($_POST['user_street'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "ulica" jest wymagane.</div>';
		}
		if ($_POST['user_houseNumber'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "numer domu" jest wymagane.</div>';
		}
		if ($_POST['user_permission'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "typ konta" jest wymagane.</div>';
		}

		if ($_POST['user_password1'] != $_POST['user_password2']) {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Hasła muszą być takie same.</div>';
		}

		if ($requirements_met) {
			# collect new data
			if ($_POST['user_pesel'] != $u->getPesel())
				$data['pesel'] = $_POST['user_pesel'];
			if ($_POST['user_name'] != $u->getName())
				$data['name'] = $_POST['user_name'];
			if ($_POST['user_surname'] != $u->getSurname())
				$data['surname'] = $_POST['user_surname'];
			if ($_POST['user_email'] != $u->getEmail())
				$data['email'] = $_POST['user_email'];
			if ($_POST['user_phone'] != $u->getPhone())
				$data['phone'] = $_POST['user_phone'];
			if ($_POST['user_town'] != $u->getTown())
				$data['town'] = $_POST['user_town'];
			if ($_POST['user_code'] != $u->getPostCode())
				$data['postCode'] = $_POST['user_code'];
			if ($_POST['user_street'] != $u->getStreet())
				$data['street'] = $_POST['user_street'];
			if ($_POST['user_houseNumber'] != $u->getHouseNumber())
				$data['houseNumber'] = $_POST['user_houseNumber'];
			if ((int)$_POST['user_permission'] !== $u->getPermission())
				$data['permission'] = (int)$_POST['user_permission'];

			# save data to the db
			if (empty($data) and $_POST['user_password1'] == '') {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały zmienione.</div>';
			} else {
				$u->setData($data);

				$error1 = false;
				if ($_POST['user_password1'] != '') {
					if ($u->changePassword('', $_POST['user_password1'], true) === 0) {
						echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Hasło zostało zmienione.</div>';
					} else {
						$error1 = true;
						echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zmienić hasła.</div>';
					}
				}

				$error2 = false;
				if (!empty($data))
					if (!$u->saveDataToDb('array_keys+object_properties', $data)) {
						$error2 = true;
						echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
					}

				if (!$error1 and !$error2) {
					$_SESSION['tmp']['user_form']['status'] = true;
					$loader->redirect('/user_form/' . $u->getId() . '-' . str_replace(' ', '_', mb_strtolower($u->getFullName())));
				} elseif ($error1 and !$error2) {
					$_SESSION['tmp']['user_form']['status'] = true;
					$_SESSION['tmp']['user_form']['changePasswordError'] = true;
					$loader->redirect('/user_form/' . $u->getId() . '-' . str_replace(' ', '_', mb_strtolower($u->getFullName())));
				}
			}
		}
	} else {
		# print status info
		if (isset($_SESSION['tmp']['user_form']['status'])) {
			if (isset($_SESSION['tmp']['changePasswordError']['status'])) {
				if ($_SESSION['tmp']['changePasswordError']['status'])
					echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zmienić hasła.</div>';
				unset($_SESSION['tmp']['changePasswordError']['status']);
			}
			if ($_SESSION['tmp']['user_form']['status'])
				echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Dane zostały zapisane.</div>';
			unset($_SESSION['tmp']['user_form']['status']);
		}
	}
# add new
} else {
	if (
		isset($_POST['user_pesel']) and
		isset($_POST['user_password1']) and
		isset($_POST['user_password2']) and
		isset($_POST['user_name']) and
		isset($_POST['user_surname']) and
		isset($_POST['user_email']) and
		isset($_POST['user_phone']) and
		isset($_POST['user_town']) and
		isset($_POST['user_code']) and
		isset($_POST['user_street']) and
		isset($_POST['user_houseNumber']) and
		isset($_POST['user_permission'])
	) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['user_pesel'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "PESEL" jest wymagane.</div>';
		}
		if ($_POST['user_password1'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "hasło" jest wymagane.</div>';
		}
		if ($_POST['user_password2'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "powtórz hasło" jest wymagane.</div>';
		}
		if ($_POST['user_name'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "imię" jest wymagane.</div>';
		}
		if ($_POST['user_surname'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "nazwisko" jest wymagane.</div>';
		}
		if ($_POST['user_town'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "miasto" jest wymagane.</div>';
		}
		if ($_POST['user_code'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "kod pocztowy" jest wymagane.</div>';
		}
		if ($_POST['user_street'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "ulica" jest wymagane.</div>';
		}
		if ($_POST['user_houseNumber'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "numer domu" jest wymagane.</div>';
		}
		if ($_POST['user_permission'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "typ konta" jest wymagane.</div>';
		}

		if ($_POST['user_password1'] != $_POST['user_password2']) {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Hasła muszą być takie same.</div>';
		}

		if ($requirements_met) {
			# collect new data
			$data['pesel'] = $_POST['user_pesel'];
			$data['plain_password'] = $_POST['user_password1'];
			$data['name'] = $_POST['user_name'];
			$data['surname'] = $_POST['user_surname'];
			$data['email'] = $_POST['user_email'];
			$data['phone'] = $_POST['user_phone'];
			$data['town'] = $_POST['user_town'];
			$data['postCode'] = $_POST['user_code'];
			$data['street'] = $_POST['user_street'];
			$data['houseNumber'] = $_POST['user_houseNumber'];
			$data['permission'] = (int)$_POST['user_permission'];

			$u = new \PS\User($db);
			$u->setData($data);
			if ($u->saveDataToDb('new')) {
				$_SESSION['tmp']['user_form']['status'] = true;
				$loader->redirect('/user_form/' . $u->getId() . '-' . str_replace(' ', '_', mb_strtolower($u->getFullName())));
			} else {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
			}
		}
	}
}
?>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">PESEL:</span>
						<input class="form-control" name="user_pesel" placeholder="00000000000"<?= ($edit_mode ? ' value="' . $u->getPesel() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="min-width: 150px;">hasło:</span>
						<input class="form-control" type="password" name="user_password1" placeholder="password"<?= (!$edit_mode ? ' required' : '') ?> />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="min-width: 150px;">powtórz hasło:</span>
						<input class="form-control" type="password" name="user_password2" placeholder="password"<?= (!$edit_mode ? ' required' : '') ?> />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">imię:</span>
						<input class="form-control" name="user_name" placeholder="john"<?= ($edit_mode ? ' value="' . $u->getName() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">nazwisko:</span>
						<input class="form-control" name="user_surname" placeholder="doe"<?= ($edit_mode ? ' value="' . $u->getSurname() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">e-mail:</span>
						<input type="email" class="form-control" name="user_email" placeholder="user@example.com"<?= ($edit_mode ? ' value="' . $u->getEmail() . '"' : '') ?> />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">telefon:</span>
						<input class="form-control" name="user_phone" placeholder="000000000"<?= ($edit_mode ? ' value="' . $u->getPhone() . '"' : '') ?> />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">miasto:</span>
						<input class="form-control" name="user_town" placeholder="miasto"<?= ($edit_mode ? ' value="' . $u->getTown() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">kod pocztowy:</span>
						<input class="form-control" name="user_code" placeholder="00-000"<?= ($edit_mode ? ' value="' . $u->getPostCode() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">ulica:</span>
						<input class="form-control" name="user_street" placeholder="ulica"<?= ($edit_mode ? ' value="' . $u->getStreet() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">numer domu:</span>
						<input class="form-control" name="user_houseNumber" placeholder="numer domu"<?= ($edit_mode ? ' value="' . $u->getHouseNumber() . '"' : '') ?> required />
					</div><br/>
					<div class="form-group" style="margin: 5px;">
						<label for="search_surname">typ konta:</label>
						<select name="user_permission" class="form-control" required>
							<option name="user" value="0">czytelnik</option>
							<option name="librarian" value="1"<?= (($edit_mode and $u->getPermission() === 1) ? ' selected' : '') ?>>bibliotekarz</option>
						</select>
					</div>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="<?= GLOBAL_ROOT ?>/users" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
	</main>
</body>
</html>
