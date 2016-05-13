<?php
global $loader;
global $db;
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	$loader->redirect('/login');
	exit();
}

# entry only for admins
if ($user->getPermission() !== 1) {
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
<?php
$book_writers = [];
$edit_mode = false;
if (isset($loader->getParams()[1])) {
	$book = new \PS\Book($db);
	if ($book->getDataFromDb('id', (int)$loader->getParams()[1])) {
		$edit_mode = true;
		$authors = new \PS\Author($db);
		$book_authors_array = $authors->search('book', $book->getId());
		unset($authors);
		foreach($book_authors_array as $item)
			$book_writers[] = $item['writer'];
	}
}
?>
<!--poniżej includuję belkę menu-->
<?php $loader->loadModule('inc/menu'); ?>
		<div class="panel panel-primary" style="max-width: 500px; margin: 0px auto; margin-top: 10px;">
			<div class="panel-heading" style="padding: 5px;"><h5><?= ($edit_mode ? 'Edycja książki' : 'Dodawanie książki') ?></h5></div>
			<div class="panel-body">
				<form method="post">
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">tytuł:</span>
						<input class="form-control" name="book_title" placeholder="tytuł"<?= ($edit_mode ? ' value="' . $book->getTitle() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">autor:</span>
						<select name="book_author[]" class="form-control" multiple required>
<?php
$writers = new \PS\Writer($db);
foreach($writers->search('plain') as $writer) {
	echo '<option value="' . $writer['id'] . '"' . (($edit_mode and in_array($writer['id'], $book_writers)) ? ' selected' : '') . '>' . $writer['name'] . ' ' . $writer['surname'] . '</option>';
}
unset($writers);
?>
						</select>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">opis:</span>
						<textarea class="form-control" name="book_desc" placeholder="opis" required><?= ($edit_mode ? $book->getDescription() : '') ?></textarea>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">wydawnictwo:</span>
						<select name="book_publisher" class="form-control" required>
<?php
$publishers = new \PS\Publisher($db);
foreach($publishers->search('plain') as $publisher) {
	echo '<option value="' . $publisher['id'] . '"' . (($edit_mode and $publisher['id'] == $book->getPublisher()) ? ' selected' : '') . '>' . $publisher['name'] . '</option>';
}
unset($publishers);
?>
						</select>
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">rok wydania:</span>
						<input class="form-control" name="book_year" placeholder="rok"<?= ($edit_mode ? ' value="' . $book->getPublicationYear() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">ISBN:</span>
						<input class="form-control" name="book_isbn" placeholder="isbn"<?= ($edit_mode ? ' value="' . $book->getIsbn() . '"' : '') ?> required />
					</div><br/>
					<div class="input-group input-group" style="margin: 0px auto;">
						<span class="input-group-addon" id="sizing-addon1" style="min-width: 150px;">ilość egzemplarzy:</span>
						<input class="form-control" name="book_count" placeholder="ilość"<?= ($edit_mode ? ' value="' . $book->getTotalCount() . '"' : '') ?> required />
					</div><br/>
					<input type="submit" class="btn btn-primary" value="Zatwierdź">
					<a href="<?= GLOBAL_ROOT ?>/books" class="btn btn-primary">Powrót</a>
				</form>
			</div>
		</div>
<?php
# edit existing
if ($edit_mode) {
	if (
		isset($_POST['book_title']) and
		isset($_POST['book_desc']) and
		isset($_POST['book_publisher']) and
		isset($_POST['book_year']) and
		isset($_POST['book_isbn']) and
		isset($_POST['book_count'])
	) {
		$data = [];
		$data_author = [];

		# check required input
		$requirements_met = true;
		if ($_POST['book_title'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "tytuł" jest wymagane.</div>';
		}
		if (empty($_POST['book_author'])) {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "autor" jest wymagane.</div>';
		}
		if ($_POST['book_desc'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "opis" jest wymagane.</div>';
		}
		if ($_POST['book_publisher'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "wydawnictwo" jest wymagane.</div>';
		}
		if ($_POST['book_year'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "rok wydania" jest wymagane.</div>';
		}
		if ($_POST['book_isbn'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "ISBN" jest wymagane.</div>';
		}
		if ($_POST['book_count'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "ilość egzemplarzy" jest wymagane.</div>';
		}

		if ($requirements_met) {
			# collect new data
			if ($_POST['book_title'] != $book->getTitle())
				$data['title'] = $_POST['book_title'];
			if ($_POST['book_desc'] != $book->getDescription())
				$data['description'] = $_POST['book_desc'];
			if ($_POST['book_publisher'] != $book->getPublisher())
				$data['publisher'] = $_POST['book_publisher'];
			if ($_POST['book_year'] != $book->getPublicationYear())
				$data['publicationYear'] = $_POST['book_year'];
			if ($_POST['book_isbn'] != $book->getIsbn())
				$data['isbn'] = $_POST['book_isbn'];
			if ((int)$_POST['book_count'] != $book->getTotalCount()) {
				if ((int)$_POST['book_count'] >= ($book->getTotalCount() - $book->getAvailableCount())) {
					$data['totalCount'] = (int)$_POST['book_count'];
					$data['availableCount'] = (int)$_POST['book_count'] - ($book->getTotalCount() - $book->getAvailableCount());
				} else {
					echo '<br /><div class="alert alert-warning" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zmniejszyć ilości egzemplarzy do ilości mniejszej niż liczba aktualnie wypożyczonych egzemplarzy.</div>';
					$_SESSION['tmp']['book_form']['totalCountWarning'] = true;
				}
			}

			if ($_POST['book_author'] !== $book_writers)
				$data_author[] = 1;

			# save data to the db
			if (empty($data) and empty($data_author)) {
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Żadne dane nie zostały zmienione.</div>';
			} else {
				# save authors
				if (!empty($data_author)) {
					$author = new \PS\Author($db);

					# remove authors
					$error1 = false;
					foreach ($book_authors_array as $key => $item) {
						if (!in_array($item['writer'], $_POST['book_author'])) {
							$author->setData($item);
							if ($error1 or !$author->removeDataFromDb()) {
								$error1 = true;
								echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Wystąpił błąd podczas zapisywania autorów do bazy.</div>';
							}
							unset($book_authors_array[$key]);
						}
					}

					# add authors
					$error2 = false;
					foreach ($_POST['book_author'] as $key => $item) {
						if (!in_array($item, $book_writers)) {
							$author->setData(array(
								'book'   => $book->getId(),
								'writer' => $item
							));
							if ($error2 or !$author->saveDataToDb('new')) {
								$error2 = true;
								echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Wystąpił błąd podczas zapisywania autorów do bazy.</div>';
							}
							unset($book_authors_array[$key]);
						}
					}

					if (!$error1 and !$error2 and empty($data)) {
						$_SESSION['tmp']['book_form']['status'] = true;
						$loader->redirect('/book_form/' . $book->getId() . '-' . str_replace(' ', '_', mb_strtolower($book->getTitle())));
					}

					unset($author);
				}

				# save book data
				$book->setData($data);
				if (
					(empty($data_author) or (!empty($data_author) and !$error1 and !$error2)) and
					$book->saveDataToDb('array_keys+object_properties', $data)
				) {
					$_SESSION['tmp']['book_form']['status'] = true;
					$loader->redirect('/book_form/' . $book->getId() . '-' . str_replace(' ', '_', mb_strtolower($book->getTitle())));
				} else {
					echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
				}
			}

			unset($_SESSION['tmp']['book_form']['totalCountWarning']);
		}
	} else {
		# print status info
		if (isset($_SESSION['tmp']['book_form']['status'])) {
			if (isset($_SESSION['tmp']['book_form']['totalCountWarning'])) {
				if ($_SESSION['tmp']['book_form']['totalCountWarning'])
					echo '<br /><div class="alert alert-warning" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zmniejszyć ilości egzemplarzy do ilości mniejszej niż liczba aktualnie wypożyczonych egzemplarzy.</div>';
				unset($_SESSION['tmp']['book_form']['totalCountWarning']);
			}
			if ($_SESSION['tmp']['book_form']['status'])
				echo '<br /><div class="alert alert-success" role="alert" style="max-width: 500px; margin: 0px auto;">Dane zostały zapisane.</div>';
			unset($_SESSION['tmp']['book_form']['status']);
		}
	}
# add new
} else {
	if (
		isset($_POST['book_title']) and
		isset($_POST['book_desc']) and
		isset($_POST['book_publisher']) and
		isset($_POST['book_year']) and
		isset($_POST['book_isbn']) and
		isset($_POST['book_count'])
	) {
		$data = [];

		# check required input
		$requirements_met = true;
		if ($_POST['book_title'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "tytuł" jest wymagane.</div>';
		}
		if (empty($_POST['book_author'])) {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "autor" jest wymagane.</div>';
		}
		if ($_POST['book_desc'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "opis" jest wymagane.</div>';
		}
		if ($_POST['book_publisher'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "wydawnictwo" jest wymagane.</div>';
		}
		if ($_POST['book_year'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "rok wydania" jest wymagane.</div>';
		}
		if ($_POST['book_isbn'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "ISBN" jest wymagane.</div>';
		}
		if ($_POST['book_count'] == '') {
			$requirements_met = false;
			echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Pole "ilość egzemplarzy" jest wymagane.</div>';
		}

		if ($requirements_met) {
			# collect new data
			$data['title'] = $_POST['book_title'];
			$data['description'] = $_POST['book_desc'];
			$data['publisher'] = $_POST['book_publisher'];
			$data['publicationYear'] = $_POST['book_year'];
			$data['isbn'] = $_POST['book_isbn'];
			$data['totalCount'] = (int)$_POST['book_count'];
			$data['availableCount'] = (int)$_POST['book_count'];

			# save book data
			$error1 = false;
			$book = new \PS\Book($db);
			$book->setData($data);
			if (!$book->saveDataToDb('new')) {
				$error1 = true;
				echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Nie można zapisać danych do bazy.</div>';
			}

			# save authors
			if (!$error1) {
				$author = new \PS\Author($db);
				$error2 = false;
				foreach ($_POST['book_author'] as $key => $item) {
					$author->setData(array(
						'book'   => $book->getId(),
						'writer' => $item
					));
					if ($error2 or !$author->saveDataToDb('new')) {
						$error2 = true;
						echo '<br /><div class="alert alert-danger" role="alert" style="max-width: 500px; margin: 0px auto;">Wystąpił błąd podczas zapisywania autorów do bazy.</div>';
					}
				}
				unset($author);
			}

			if (!$error1 and !$error2) {
				$_SESSION['tmp']['book_form']['status'] = true;
				$loader->redirect('/book_form/' . $book->getId() . '-' . str_replace(' ', '_', mb_strtolower($book->getTitle())));
			}
		}
	}
}
?>
	</main>
</body>
</html>
