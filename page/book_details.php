<?php
global $loader;
global $db;
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	$loader->redirect('/login');
	exit();
}

# nothing to show here
if (!isset($loader->getParams()[1])) {
	$loader->redirect('/books');
} else {
	$book = new \PS\Book($db);
	if (!$book->getDataFromDb('id', (int)$loader->getParams()[1]))
		$loader->redirect('/books');
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
			<div class="panel-heading" style="padding: 5px;"><h5>Informacje o książce</h5></div>
			<div class="panel-body">
				<div class="well">
					<h4><?= $book->getTitle() ?></h4>
					Autor:
<?php
# authors
$authors = new \PS\Author($db);
$first = true;
foreach ($authors->search('book', $book->getId()) as $author) {
	$writer = new \PS\Writer($db);
	$writer->getDataFromDb('id', $author['writer']);
	echo ($first ? '' : ', ') . $writer->getFullName();
	unset($writer);
	$first = false;
}
unset($authors);
?><br />
					Wydawca:
<?php
# publisher
$publisher = new \PS\Publisher($db);
$publisher->getDataFromDb('id', $book->getPublisher());
echo $publisher->getName();
unset($publisher);
?><br />
					Rok wydania: <?= $book->getPublicationYear() ?><br />
					ISBN: <?= $book->getIsbn() ?><br />
					Dostępne egzemplarze: <?= $book->getAvailableCount() ?>
				</div>
				<div class="well"><?= $book->getDescription() ?></div>
<?php if ($user->getPermission() === 1) { ?>
				<a href="<?= GLOBAL_ROOT . '/book_form/' . $book->getId() . '-' . filter_var(str_replace(' ', '_', mb_strtolower($book->getTitle())), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH) ?>" class="btn btn-primary">Edytuj</a>
<?php } ?>
				<a href="" class="btn btn-primary">Zamów</a>
				<a href="<?= GLOBAL_ROOT ?>/books" class="btn btn-primary">Powrót</a>
			</div>
		</div>
	</main>
</body>
</html>
