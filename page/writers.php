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
<!--poniżej includuję belkę menu-->
<?php $loader->loadModule('inc/menu'); ?>
	<div class="panel panel-primary" style="margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5>Lista autorów</h5></div>
			<div class="panel-body">
				<a href="<?= GLOBAL_ROOT ?>/writer_form" class="btn btn-primary">Dodaj autora</a>
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
							<label for="search_order">sortowanie:</label>
							<select name="search_order" id="search_order" class="form-control">
								<option name="surname_name">nazwisko, imię</option>
								<option name="name_surname"<?= ((isset($_POST['search_order']) and $_POST['search_order'] == 'imię, nazwisko') ? ' selected' : '') ?>>imię, nazwisko</option>
							</select>
						</div>
						<input type="submit" class="btn btn-primary" value="pokaż">
					</form>
				</div>
				<table class="table table-striped table-bordered" style="margin-top: 10px;">
					<thead>
						<th>Nr</th>
						<th>Imię</th>
						<th>Nazwisko</th>
						<th>Opcje</th>
					</thead>
<?php
$filter = [];
if (isset($_POST['search_name']) and $_POST['search_name'] != '')
	$filter['name'] = $_POST['search_name'];
if (isset($_POST['search_surname']) and $_POST['search_surname'] != '')
	$filter['surname'] = $_POST['search_surname'];

$order = 0;
if (isset($_POST['search_order'])) {
	if ($_POST['search_order'] == 'imię, nazwisko')
		$order = 1;
}

$writers = new \PS\Writer($db);

foreach ($writers->search('plain', null, $filter, $order) as $writer) {
	echo '<tr>';

	echo '<td>' . $writer['id'] . '</td>';
	echo '<td>' . $writer['name'] . '</td>';
	echo '<td>' . $writer['surname'] . '</td>';
	echo '<td><a href="' . GLOBAL_ROOT .
	'/writer_form/' . $writer['id'] . '-' . filter_var(str_replace(' ', '_', mb_strtolower($writer['name'] . ' ' . $writer['surname'])), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) .
	'" class="btn btn-default">edytuj</a></td>';

	echo '</tr>';
}
?>
				</table>
			</div>
		</div>
	</main>
</body>
</html>
