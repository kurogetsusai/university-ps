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
			<div class="panel-heading" style="padding: 5px;"><h5>Dodawanie użytkownika</h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">PESEL:</span>
						<input class="form-control" name="user_pesel" placeholder="00000000000" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="min-width: 150px;">hasło:</span>
						<input class="form-control" type="password" name="user_password1" placeholder="password" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon2" style="min-width: 150px;">powtórz hasło:</span>
						<input class="form-control" type="password" name="user_password2" placeholder="password" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">imię:</span>
						<input class="form-control" name="user_name" placeholder="john" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">nazwisko:</span>
						<input class="form-control" name="user_surname" placeholder="doe" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">e-mail:</span>
						<input class="form-control" name="user_email" placeholder="user@example.com" />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">telefon:</span>
						<input class="form-control" name="user_phone" placeholder="000000000" />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">miasto:</span>
						<input class="form-control" name="user_town" placeholder="miasto" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">kod pocztowy:</span>
						<input class="form-control" name="user_code" placeholder="00-000" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">ulica:</span>
						<input class="form-control" name="user_street" placeholder="ulica" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">numer domu:</span>
						<input class="form-control" name="user_houseNumber" placeholder="ulica" required />
					</div><br/>
					<div class="form-group" style="margin: 5px;">
						<label for="search_surname">typ konta:</label>
						<select name="user_permission" class="form-control">
							<option name="user">czytelnik</option>
							<option name="librarian">bibliotekarz</option>
						</select>
					</div>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="" class="btn btn-primary">Powrót</a>
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
