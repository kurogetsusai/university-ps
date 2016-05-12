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
		<div class="panel panel-primary" style="margin: 10px auto;">
			<div class="panel-heading" style="padding: 5px;"><h5>Lista książek</h5></div>
			<div class="panel-body">
<?php if ($user->getPermission() === 1) { ?>
				<a href="<?= GLOBAL_ROOT ?>/book_form" class="btn btn-primary">Dodaj książkę</a>
<?php } ?>
				<div class="well" style="margin-top: 10px; padding: 5px;">
					<h4>Opcje wyświetlania</h4>
					<form method="post" class="form-inline">
						<div class="form-group" style="margin: 5px;">
							<label for="search_title">tytuł:</label>
							<input class="form-control" id="search_title" name="search_title" placeholder="tytuł" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_name">imię autora:</label>
							<input class="form-control" id="search_name" name="search_name" placeholder="imię" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_surname">nazwisko autora:</label>
							<input class="form-control" id="search_surname" name="search_surname" placeholder="nazwisko" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_publisher">wydawnictwo:</label>
							<input class="form-control" id="search_publisher" name="search_publisher" placeholder="wydawnictwo" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_isbn">ISBN:</label>
							<input class="form-control" id="search_isbn" name="search_isbn" placeholder="isbn" />
						</div>
						<div class="form-group" style="margin: 5px;">
							<label for="search_order">sortowanie:</label>
							<select name="search_order" class="form-control">
								<option value="title">tytuł</option>
								<option value="title">nazwisko autora</option>
								<option value="title">ISBN</option>
								<option value="title">rok</option>
							</select>
						</div>
						<input type="submit" class="btn btn-primary" value="pokaż">
					</form>
				</div>
				<table class="table table-striped table-bordered" style="margin-top: 10px;">
					<thead>
						<th>Nr</th>
						<th>Tytuł</th>
						<th>Autor</th>
						<th>Wydawnictwo</th>
						<th>Dostępne<br />egzemplarze</th>
						<th>Rok<br />wydania</th>
						<th>ISBN</th>
						<th>Opcje</th>
					</thead>
					<tr>
						<td>1</td>
						<td>Brief History of Time</td>
						<td>Stephen Hawking</td>
						<td>jakies tam</td>
						<td>6/8</td>
						<td>1996</td>
						<td>666</td>
						<td>
							<a href="<?= GLOBAL_ROOT ?>/book_details" class="btn btn-default">szczegóły</a>
							<a href="" class="btn btn-default">zamów</a> <!-- jeśli dostępna ilość > 0 -->
<?php if ($user->getPermission() === 1) { ?>
							<a href="" class="btn btn-default">edytuj</a>
<?php } ?>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</main>
</body>
</html>
