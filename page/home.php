<?php
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
</head>
<body>
	<main id="page">
		<h1>Hello World!</h1>
		<a href="<?= GLOBAL_ROOT ?>/logout">Wyloguj</a>
		<pre>
<?= GLOBAL_ROOT ?>

<?= CURRENT_PATH ?>
		</pre>
	</main>
</body>
</html>
