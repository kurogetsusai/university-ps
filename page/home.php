<?php
global $loader;
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
<!--poniżej kod odpowiadający za tabelę z zamówieniami użytkownika-->
		<div class="panel panel-primary" style="display: inline-block; min-width: 500px; margin: 10px auto;">
			<div class="panel-heading"><h5>twoje zamówienia</h5></div>
			<div class="panel-body">
				<table class="table table-striped table-bordered">
					<thead>
						<th>Tytuł</th>
						<th>Autor</th>
						<th>Status</th>
						<th>Komentarz</th>
						<th>Opcje</th>
					</thead>
					<tr>
						<td>Nocna Straż</td>
						<td>Terry Pratchett</td>
						<td>oczekujące</td>
						<td></td>
						<td><a class="btn btn-default" href="#" role="button">anuluj</a></td>
					</tr>
					<tr>
						<td>Prawem i Lewem</td>
						<td>Władysław Łoziński</td>
						<td>zrealizowane</td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td>Gnój, czyli antybiografia</td>
						<td>Wojciech Kuczok</td>
						<td>anulowane</td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</div>
		</div>
<!--zamówienia użytkownika koniec-->
	</main>
</body>
</html>
