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
		<div class="panel panel-primary" style="margin: 10px auto; max-width: 1000px;">
			<div class="panel-heading"><h5>Lista użytkowników</h5></div>
			<div class="panel-body">
				<a href="" class="btn btn-primary">Dodaj użytkownika</a>
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
$users = new \PS\User($db);

foreach ($users->search('plain') as $u) {
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
