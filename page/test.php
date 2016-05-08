<?php
global $db;
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
		<br>
		<h1>Testuje sobie tu różne rzeczy, nie psuć mi tego</h1>
		<br>
		<div class="panel panel-primary" style="display: inline-block; min-width: 500px; margin: 10px auto;">

			<div class="panel-heading"><h5>Katalog książek</h5></div>
			<div class="panel-body">
				<table class="table table-striped table-bordered">
					<thead>
						<th>ID</th>
						<th>Tytuł</th>
						<th>Autorzy</th>
						<th>Wydawnictwo</th>
						<th>ISBN</th>
						<th>Rok</th>
						<th>Opis</th>
						<th>Ilość</th>
					</thead>
<?php

$books = new \PS\Book($db);

# print all books
foreach ($books->search('plain+publishers') as $book) {
	echo '<tr>';

	echo '<td>' . $book['id'] . '</td>';
	echo '<td>' . $book['title'] . '</td>';

	# authors
	echo '<td>';
	$authors = new \PS\Author($db);
	$first = true;
	foreach ($authors->search('book', $book['id']) as $author) {
		$writer = new \PS\Writer($db);
		$writer->getDataFromDb('id', $author['writer']);
		echo ($first ? '' : ', ') . $writer->getFullName();
		unset($writer);
		$first = false;
	}
	echo '</td>';
	unset($authors);

	echo '<td>' . $book['publisherName'] . '</td>';
	echo '<td>' . $book['isbn'] . '</td>';
	echo '<td>' . $book['publicationYear'] . '</td>';
	echo '<td>' . $book['description'] . '</td>';
	echo '<td>' . $book['availableCount'] . '</td>';

	echo '</tr>';
}

?>
				</table>
			</div>

			<div class="panel-heading"><h5>SELECT * FROM book</h5></div>
			<div class="panel-body">
				<table class="table table-striped table-bordered">
<?php

$books = new \PS\Book($db);

$first = true;
foreach ($books->search('plain') as $book) {

	if ($first) {
		echo '<thead>';
		foreach ($book as $key => $item)
			echo '<th>' . $key . '</th>';
		echo '</thead>';
		$first = false;
	}

	echo '<tr>';

	foreach ($book as $item)
		echo '<td>' . $item . '</td>';

	echo '</tr>';
}

?>
				</table>
			</div>

			<div class="panel-heading"><h5>SELECT * FROM book INNER JOIN publisher</h5></div>
			<div class="panel-body">
				<table class="table table-striped table-bordered">
<?php

$books = new \PS\Book($db);

$first = true;
foreach ($books->search('plain+publishers') as $book) {

	if ($first) {
		echo '<thead>';
		foreach ($book as $key => $item)
			echo '<th>' . $key . '</th>';
		echo '</thead>';
		$first = false;
	}

	echo '<tr>';

	foreach ($book as $item)
		echo '<td>' . $item . '</td>';

	echo '</tr>';
}

?>
				</table>
			</div>

		</div>
	</main>
</body>
</html>
