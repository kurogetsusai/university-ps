<?php
global $loader;
global $db;
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	header('Location: ' . GLOBAL_ROOT . '/login');
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
						<th>nr</th>
						<th>Nazwa</th>
						<th>Opcje</th>
					</thead>
					<tr>
						<td>6</td>
						<td>Fabryka Słów</td>
						<td><a href="" class="btn btn-default">edytuj</a></td>
					</tr>
					<tr>
						<td>15</td>
						<td>Runa</td>
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
