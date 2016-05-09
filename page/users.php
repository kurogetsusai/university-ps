<?php
global $loader;
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
						<th>nr</th>
						<th>Imię i Nazwisko</th>
						<th>PESEL</th>
						<th>Adres</th>
						<th>Uprawnienia</th>
						<th>Opcje</th>
					</thead>
					<tr>
						<td>6</td>
						<td>Grzegorz Szymański</td>
						<td>666666666</td>
						<td>jakieś tam miasto i ulica</td>
						<td>Bibliotekarz</td>
						<td><a href="" class="btn btn-default">edytuj</a></td>
					</tr>
					<tr>
						<td>7</td>
						<td>Paweł Wąż</td>
						<td>6666677777</td>
						<td>jakieś tam miasto i ulica</td>
						<td>Bibliotekarz</td>
						<td><a href="" class="btn btn-default">edytuj</a></td>
					</tr>
				</table>
			</div>
		</div>
<?php
# show password change errors
if (isset($_POST['old_password']) && isset($_POST['new_password1']) && isset($_POST['new_password2'])) {
	echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Login code: ' . $user->getRequestDataResult() . '</div>';
}
?>
	</main>
</body>
</html>
