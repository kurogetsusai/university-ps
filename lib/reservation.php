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

	# status codes
	private $status_code = array(
		'oczekujące',
		'zrealizowane',
		'anulowane'
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
		return ($status == null ? $this->status_code[$this->status] : $this->status_code[$status]);
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
			                'book.count AS bookCount, ' .
			                'book.description AS bookDescription ' .
			         'FROM reservation INNER JOIN book ON reservation.book = book.id ' .
			         'WHERE reserver = :reserver ' .
			         'ORDER BY reservation.id';
			$placeholders = array(':reserver' => $input);
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

