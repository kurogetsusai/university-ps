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
# show password change errors
if (isset($_POST['old_password']) && isset($_POST['new_password1']) && isset($_POST['new_password2'])) {
	echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Login code: ' . $user->getRequestDataResult() . '</div>';
}
?>
	</main>
</body>
</html>
