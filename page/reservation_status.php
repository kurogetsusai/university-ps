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
			<div class="panel-heading" style="padding: 5px;"><h5>zmiana statusu zamówienia nr 6</h5></div>
			<div class="panel-body">
				Użytkownik: jakiśtam<br />Książka: jakaśtam
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">komentarz:</span>
						<textarea class="form-control" name="order_desc" placeholder="opis"></textarea>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">status:</span>
						<select name="order_status" class="form-control">
							<option>oczekujące</option>
							<option>gotowe do odbioru</option>
							<option>wypożyczone</option>
							<option>anulowane (czytelnik)</option>
							<option>anulowane (bibliotekarz)</option>
							<option>oddane (zakończone)</option>
						</select>
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
	</main>
</body>
</html>
