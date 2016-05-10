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
			<div class="panel-heading" style="padding: 5px;"><h5>Lista użytkowników</h5></div>
			<div class="panel-body">
				<a href="<?= GLOBAL_ROOT ?>/user_form" class="btn btn-primary">Dodaj użytkownika</a>
				<div class="well" style="margin-top: 10px; padding: 5px;">
					<h4>Opcje wyświetlania</h4>
					<form method="post" class="form-inline">
						<div class="form-group" style="margin: 5px;">
							<label for="search_name">imię:</label>
							<input class="form-control" id="search_name" name="search_name" placeholder="imię"<?= ((isset($_POST['search_name']) and $_POST['search_name'] != '') ? ' value="' . $_POST['search_name'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_surname">nazwisko:</label>
							<input class="form-control" id="search_surname" name="search_surname" placeholder="nazwisko"<?= ((isset($_POST['search_surname']) and $_POST['search_surname'] != '') ? ' value="' . $_POST['search_surname'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_pesel">PESEL:</label>
							<input class="form-control" id="search_pesel" name="search_pesel" placeholder="pesel"<?= ((isset($_POST['search_pesel']) and $_POST['search_pesel'] != '') ? ' value="' . $_POST['search_pesel'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_town">miasto:</label>
							<input class="form-control" id="search_town" name="search_town" placeholder="miasto"<?= ((isset($_POST['search_town']) and $_POST['search_town'] != '') ? ' value="' . $_POST['search_town'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_street">ulica:</label>
							<input class="form-control" id="search_street" name="search_street" placeholder="ulica"<?= ((isset($_POST['search_street']) and $_POST['search_street'] != '') ? ' value="' . $_POST['search_street'] . '"' : '') ?> />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_surname">typ konta:</label>
							<select name="search_permission" id="search_permission" class="form-control">
								<option name="all">wszystkie</option>
								<option name="user"<?= ((isset($_POST['search_permission']) and $_POST['search_permission'] == 'czytelnik') ? ' selected' : '') ?>>czytelnik</option>
								<option name="librarian"<?= ((isset($_POST['search_permission']) and $_POST['search_permission'] == 'bibliotekarz') ? ' selected' : '') ?>>bibliotekarz</option>
							</select>
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_order">sortowanie:</label>
							<select name="search_order" id="search_order" class="form-control">
								<option name="pesel">PESEL</option>
								<option name="name_surname"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'imię, nazwisko') ? ' selected' : '') ?>>imię, nazwisko</option>
								<option name="surname_name"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'nazwisko, imię') ? ' selected' : '') ?>>nazwisko, imię</option>
								<option name="permission"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'typ konta') ? ' selected' : '') ?>>typ konta</option>
							</select>
						</div>
						<input type="submit" class="btn btn-primary" value="pokaż">
					</form>
				</div>
				<table class="table table-striped table-bordered" style="margin-top: 10px;">
					<thead>
						<th>Nr</th>
						<th>Imię i Nazwisko</th>
						<th>PESEL</th>
						<th>Adres</th>
						<th>Uprawnienia</th>
						<th>Opcje</th>
					</thead>
<?php
$filter = [];
if (isset($_POST['search_name']) and $_POST['search_name'] != '')
	$filter['name'] = $_POST['search_name'];
if (isset($_POST['search_surname']) and $_POST['search_surname'] != '')
	$filter['surname'] = $_POST['search_surname'];
if (isset($_POST['search_pesel']) and $_POST['search_pesel'] != '')
	$filter['pesel'] = $_POST['search_pesel'];
if (isset($_POST['search_town']) and $_POST['search_town'] != '')
	$filter['town'] = $_POST['search_town'];
if (isset($_POST['search_street']) and $_POST['search_street'] != '')
	$filter['street'] = $_POST['search_street'];
if (isset($_POST['search_permission']) and $_POST['search_permission'] != '')
	switch ($_POST['search_permission']) {
	case 'czytelnik':
		$filter['permission'] = 0;
		break;
	case 'bibliotekarz':
		$filter['permission'] = 1;
		break;
	}

$order = 0;
if (isset($_POST['search_order'])) {
	if ($_POST['search_order'] == 'imię, nazwisko')
		$order = 1;
	if ($_POST['search_order'] == 'nazwisko, imię')
		$order = 2;
	if ($_POST['search_order'] == 'typ konta')
		$order = 3;
}

$users = new \PS\User($db);

foreach ($users->search('plain', null, $filter, $order) as $u) {
	echo '<tr>';

	echo '<td>' . $u['id'] . '</td>';
	echo '<td>' . $u['name'] . ' ' . $u['surname'] . '</td>';
	echo '<td>' . $u['pesel'] . '</td>';
	echo '<td>' . $u['street'] . ' ' . $u['houseNumber'] . '<br>' . substr($u['postCode'], 0, 2) . '-' . substr($u['postCode'], 2, 3) . ' ' . $u['town'] . '</td>';
	echo '<td>' . ($u['permission'] === '0' ? 'użytkownik' : 'bibliotekarz') . '</td>';
	echo '<td><a href="" class="btn btn-default">edytuj</a></td>';

	echo '</tr>';
}
?>
				</table>
			</div>
		</div>
	</main>
</body>
</html>
