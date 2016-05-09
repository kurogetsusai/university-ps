<?php
global $loader;
global $user;

# no entry for logged in
if ($user->isLoggedIn()) {
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
		<img src="<?= GLOBAL_ROOT ?>/gfx/books.png" style="margin:10px;" />
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto;">
			<div class="panel-heading"><h5>Logowanie</h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="max-width: 500px; margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="width:90px">login:</span>
						<input class="form-control" maxlength="11" name="login_pesel" placeholder="login" required />
					</div><br/>
					<div class="input-group input-group" style="max-width: 500px; margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="width:90px">hasło:</span>
						<input class="form-control" type="password" name="login_password" placeholder="hasło" required />
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zaloguj">
				</form>
			</div>
		</div>
<?php
# show login errors
if (isset($_POST['login_pesel']) && isset($_POST['login_password'])) {
	echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Błąd: ' . $user->getRequestDataString() . '</div>';
}
?>
	</main>
	<script src="<?= GLOBAL_ROOT ?>/js/bootstrap.min.js"></script>
</body>
</html>
