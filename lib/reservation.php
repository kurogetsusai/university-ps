<?php

namespace PS;

class Reservation {
	# constructor data
	private $db;

	# data from db
	private $id;
	private $reserver;
	private $book;
	private $status;
	private $description;

	# tmp
	private $old_status = null;

	# status codes
	private $status_code = array(
		'oczekujące',
		'anulowane (czytelnik)',
		'anulowane (bibliotekarz)',
		'gotowe do odbioru',
		'wypożyczone',
		'oddane (zakończone)'
	);

	public function __construct($db,
	                            $id = null,
	                            $reserver = null,
	                            $book = null,
	                            $status = null,
	                            $description = null)
	{
		$this->db          = $db;
		$this->id          = $id;
		$this->reserver    = $reserver;
		$this->book        = $book;
		$this->status      = $status;
		$this->description = $description;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getReserver()
	{
		return $this->reserver;
	}

	public function getBook()
	{
		return $this->book;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function getStatusName($status)
	{
		return ($status === null ? $this->status_code[$this->status] : $this->status_code[$status]);
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM reservation WHERE ';
		switch($mode) {
		case 'id':
			$query .= 'id = :id';
			break;
		default:
			return false;	# invalid mode = abort
		}
		$query .= ' LIMIT 1';

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute(array(':' . $mode => $input));
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		# does the reservation exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id          = (int)$row['id'];
		$this->reserver    = (int)$row['reserver'];
		$this->book        = (int)$row['book'];
		$this->status      = (int)$row['status'];
		$this->description = $row['description'];

		return true;
	}

	public function setData($data)
	{
		foreach ($data as $key => $item)
			switch ($key) {
			case 'id':
				$this->id = (int)$item;
				break;
			case 'reserver':
				$this->reserver = (int)$item;
				break;
			case 'book':
				$this->book = (int)$item;
				break;
			case 'status':
				$this->old_status = $this->status;
				$this->status = (int)$item;
				break;
			case 'description':
				$this->description = $item;
				break;
			}
	}

	public function saveDataToDb($mode, $input)
	{
		switch ($mode) {
		case 'new':
			# save to db
			$stmt = $this->db->base->prepare('INSERT INTO reservation (reserver, book, status, description) ' .
			'VALUES (:reserver, :book, :status, :description)');
			$stmt->execute(array(
				':reserver' => $this->reserver,
				':book' => $this->book,
				':status' => $this->status,
				':description' => $this->description
			));

			# check if that worked
			if ($stmt->rowCount() !== 1) {
				return false;
			}

			# update book
			$stmt = $this->db->base->prepare('UPDATE book SET availableCount = availableCount - 1 WHERE id = :id');
			$stmt->execute(array(':id' => $this->book));

			# get ID
			$this->id = $this->db->base->lastInsertId();

			break;
		case 'array_keys+object_properties':
			$placeholders = [];
			$data = '';
			$first = true;
			foreach ($input as $key => $item)
				if (
					$key == 'status' or
					$key == 'description'
				) {
					$placeholders[':' . $key] = $this->$key;
					if ($first)
						$first = false;
					else
						$data .= ', ';
					$data .= $key . ' = :' . $key;
				}

			if (empty($placeholders))
				return false;

			# check if any books are available
			if (
				isset($input['status']) and
				($this->status === 0 || $this->status === 3 || $this->status === 4) and
				($this->old_status === 1 || $this->old_status === 2 || $this->old_status === 5)
			) {
				$stmt = $this->db->base->prepare('SELECT availableCount FROM book WHERE id = :id');
				$stmt->execute(array(':id' => $this->book));
				if ($stmt->fetchAll(\PDO::FETCH_ASSOC)[0]['availableCount'] <= 0)
					return false;
			}

			# save to db
			$stmt = $this->db->base->prepare('UPDATE reservation SET ' . $data . ' WHERE id = :id');
			$placeholders[':id'] = $this->id;
			$stmt->execute($placeholders);

			# check if that worked
			if ($stmt->rowCount() !== 1) {
				return false;
			}

			# update book
			if (
				isset($input['status']) and
				($this->old_status === 0 || $this->old_status === 3 || $this->old_status === 4) and
				($this->status === 1 || $this->status === 2 || $this->status === 5)
			) {
				$stmt = $this->db->base->prepare('UPDATE book SET availableCount = availableCount + 1 WHERE id = :id');
				$stmt->execute(array(':id' => $this->book));
			} elseif (
				isset($input['status']) and
				($this->status === 0 || $this->status === 3 || $this->status === 4) and
				($this->old_status === 1 || $this->old_status === 2 || $this->old_status === 5)
			) {
				$stmt = $this->db->base->prepare('UPDATE book SET availableCount = availableCount - 1 WHERE id = :id');
				$stmt->execute(array(':id' => $this->book));
			}

			break;
		}

		return true;
	}

	public function search($mode, $input = null)
	{
		# missing parameters
		if ($mode == null)
			return false;

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT * ' .
			         'FROM reservation';
			$placeholders = array();
			break;
		case 'reserver':
			$query = 'SELECT id, ' .
			                'book, ' .
			                'status, ' .
			                'description ' .
			         'FROM reservation ' .
			         'WHERE reserver = :reserver';
			$placeholders = array(':reserver' => $input);
			break;
		case 'reserver+books':
			$query = 'SELECT reservation.id, ' .
			                'reservation.book, ' .
			                'reservation.status, ' .
			                'reservation.description, ' .
			                'book.isbn AS bookIsbn, ' .
			                'book.title AS bookTitle, ' .
			                'book.publicationYear AS bookPublicationYear, ' .
			                'book.publisher AS bookPublisher, ' .
			                'book.totalCount AS bookTotalCount, ' .
			                'book.availableCount AS bookAvailableCount, ' .
			                'book.description AS bookDescription ' .
			         'FROM reservation INNER JOIN book ON reservation.book = book.id ' .
			         'WHERE reserver = :reserver ' .
			         'ORDER BY reservation.id';
			$placeholders = array(':reserver' => $input);
			break;
		case 'books+users':
			$query = 'SELECT reservation.id, ' .
			                'reservation.reserver, ' .
			                'reservation.book, ' .
			                'reservation.status, ' .
			                'reservation.description, ' .
			                'book.isbn AS bookIsbn, ' .
			                'book.title AS bookTitle, ' .
			                'book.publicationYear AS bookPublicationYear, ' .
			                'book.publisher AS bookPublisher, ' .
			                'book.totalCount AS bookTotalCount, ' .
			                'book.availableCount AS bookAvailableCount, ' .
			                'book.description AS bookDescription, ' .
			                'user.name AS reserverName, ' .
			                'user.surname AS reserverSurname ' .
			         'FROM reservation INNER JOIN book ON reservation.book = book.id INNER JOIN user ON reservation.reserver = user.id ' .
			         'ORDER BY reservation.id';
			$placeholders = array();
			break;
		case 'book':
			$query = 'SELECT id, ' .
			                'reserver, ' .
			                'status, ' .
			                'description ' .
			         'FROM reservation ' .
			         'WHERE book = :book';
			$placeholders = array(':book' => $input);
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

