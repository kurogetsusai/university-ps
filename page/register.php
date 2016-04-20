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
# show register errors
if (isset($_POST['register_pesel'])) { // FIXME
	echo '<div class="warning">Register code: ' . $user->getRequestDataResult() . '</div>';
}
?>
		<h1>Rejestracja</h1>
		<form method="post">
			<input type="number" maxlength="11" name="register_pesel" placeholder="PESEL" required><br>
			<input type="password" name="register_" placeholder="Hasło" required><br>
			<input type="text" maxlength="32" name="register_name" placeholder="Imię" required><br>
			<input type="text" maxlength="32" name="register_surname" placeholder="Nazwisko" required><br>
			<input type="text" maxlength="64" name="register_town" placeholder="Miasto" required><br>
			<input type="number" maxlength="5" name="register_post_code" placeholder="Kod pocztowy" required><br>
			<input type="text" maxlength="32" name="register_street" placeholder="Ulica" required><br>
			<input type="text" maxlength="10" name="register_house_number" placeholder="Numer domu" required><br>
			<br>
			<input type="submit" value="Zarejestruj">
		</form>
	</main>
</body>
</html>
