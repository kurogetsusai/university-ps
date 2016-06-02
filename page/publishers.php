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
			<div class="panel-heading" style="padding: 5px;"><h5>Lista wydawnictw</h5></div>
			<div class="panel-body">
				<a href="<?= GLOBAL_ROOT ?>/publisher_form" class="btn btn-primary">Dodaj wydawnictwo</a>
				<div class="well" style="margin-top: 10px; padding: 5px;">
					<h4>Opcje wyświetlania</h4>
					<form method="post" class="form-inline">
						<div class="form-group" style="margin: 5px;">
							<label for="search_name">nazwa:</label>
							<input class="form-control" id="search_name" name="search_name" placeholder="nazwa"<?= ((isset($_POST['search_name']) and $_POST['search_name'] != '') ? ' value="' . $_POST['search_name'] . '"' : '') ?> />
						</div>
						<input type="submit" class="btn btn-primary" value="pokaż">
					</form>
				</div>
				<table class="table table-striped table-bordered" style="margin-top: 10px;">
					<thead>
						<th>Nr</th>
						<th>Nazwa</th>
						<th>Opcje</th>
					</thead>
<?php
$filter = [];
if (isset($_POST['search_name']) and $_POST['search_name'] != '')
	$filter['name'] = $_POST['search_name'];

$publishers = new \PS\Publisher($db);

foreach ($publishers->search('plain', null, $filter, 0) as $publisher) {
	echo '<tr>';

	echo '<td>' . $publisher['id'] . '</td>';
	echo '<td>' . $publisher['name'] . '</td>';
	echo '<td><a href="' . GLOBAL_ROOT .
	'/publisher_form/' . $publisher['id'] . '-' . filter_var(str_replace(' ', '_', mb_strtolower($publisher['name'])), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) .
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
