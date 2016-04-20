<?php
global $user;

# no entry for logged in
if ($user->isLoggedIn()) {
	header('Location: ' . (GLOBAL_ROOT != '' ? GLOBAL_ROOT : '/'));
	exit();
}
?>
<!DOCTYPE html>
<html lang="<?= DEFAULT_LANG ?>">
<head>
	<title>Logowanie – <?= DEFAULT_TITLE ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="<?= GLOBAL_ROOT ?>/css/main.css">
</head>
<body>
	<main id="page">
<?php
# show login errors
if (isset($_POST['login_pesel']) && isset($_POST['login_password'])) {
	echo '<div class="warning">Login code: ' . $user->getRequestDataResult() . '</div>';
}
?>
		<h1>Logowanie</h1>
		<form method="post">
			<input type="number" maxlength="11" name="login_pesel" placeholder="PESEL" required><br>
			<input type="password" name="login_password" placeholder="Hasło" required><br>
			<br>
			<input type="submit" value="Zaloguj">
		</form>
		<a href="<?= GLOBAL_ROOT ?>/register">Rejestracja</a>
	</main>
</body>
</html>
