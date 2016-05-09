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
			<div class="panel-heading"><h5>Zmiana hasła</h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="max-width: 500px; margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="width:200px">stare hasło:</span>
						<input class="form-control" type="password" name="old_password" placeholder="stare hasło" required />
					</div><br/>
					<div class="input-group input-group" style="max-width: 500px; margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="width:200px">nowe hasło:</span>
						<input class="form-control" type="password" name="new_password1" placeholder="nowe hasło" required />
					</div><br/>
					<div class="input-group input-group" style="max-width: 500px; margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="width:200px">powtórz nowe hasło:</span>
						<input class="form-control" type="password" name="new_password2" placeholder="powtórz nowe hasło" required />
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
				</form>
			</div>
		</div>
<?php
# change password
if (isset($_POST['old_password']) && isset($_POST['new_password1']) && isset($_POST['new_password2'])) {
	$text = '';
	if ($_POST['new_password1'] != $_POST['new_password2']) {
		$text = 'Nowe hasła są różne.';
	} else {
		switch ($user->changePassword($_POST['old_password'], $_POST['new_password1'])) {
		case 0:
			break;
		case 1:
			$text = 'Użytkownik nie jest zalogowany.';
			break;
		case 2:
			$text = 'Nowe hasło jest puste.';
			break;
		case 3:
			$text = 'Stare hasło jest złe.';
			break;
		case 4:
			$text = 'Nie można zapisać nowego hasła w bazie danych.';
			break;
		default:
			$text = 'Nieznany błąd (kod 10000).';
		}
	}

	if ($text == '')
		echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Hasło zostało zmienione.</div>';
	else
		echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Błąd: ' . $text . '</div>';
}
?>
	</main>
</body>
</html>
