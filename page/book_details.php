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
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5>informacje o książce</h5></div>
			<div class="panel-body">
				<div class="well"><h4>Tytuł książki</h4>autor: jakiś tam<br /> wydawca: jakiś tam<br />rok wydania: 2001<br />ISBN: 666<br />Dostępne egzemplarze: 2</div>
				<div class="well">opis bla bla bla</div>
<?php if ($user->getPermission() === 1) { ?>
				<a href="<?= GLOBAL_ROOT ?>/book_form" class="btn btn-primary">edytuj</a>
<?php } ?>
				<a href="" class="btn btn-primary">zamów</a>
				<a href="" class="btn btn-primary">powrót</a>
			</div>
		</div>
	</main>
</body>
</html>
