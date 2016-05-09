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
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading"><h5>Lista wydawnictw</h5></div>
			<div class="panel-body">
				<a href="" class="btn btn-primary">Dodaj wydawnictwo</a>
				<table class="table table-striped table-bordered" style="margin-top: 10px;">
					<thead>
						<th>Nr</th>
						<th>Nazwa</th>
						<th>Opcje</th>
					</thead>
<?php
$publishers = new \PS\Publisher($db);

foreach ($publishers->search('plain') as $publisher) {
	echo '<tr>';

	echo '<td>' . $publisher['id'] . '</td>';
	echo '<td>' . $publisher['name'] . '</td>';
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
