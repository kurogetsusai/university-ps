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
			<div class="panel-heading" style="padding: 5px;"><h5>Dodawanie książki</h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">tytuł:</span>
						<input class="form-control" name="book_title" placeholder="tytuł" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">autor:</span>
						<select name="book_author" class="form-control">
							<option>Terry Pratchett</option>
							<option>Andrzej Sapkowski</option>
							<option>Ken Kesei</option>
						</select>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">opis:</span>
						<textarea class="form-control" name="book_desc" placeholder="opis" required></textarea>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">wydawnictwo:</span>
						<select name="book_publisher" class="form-control">
							<option>Zysk i ska</option>
							<option>Runa</option>
							<option>Fabryka Słów</option>
						</select>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">rok wydania:</span>
						<input class="form-control" name="book_year" placeholder="rok" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">ISBN:</span>
						<input class="form-control" name="book_isbn" placeholder="isbn" required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">ilość egzemplarzy:</span>
						<input class="form-control" name="book_count" placeholder="ilość" required />
					</div><br/>
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
