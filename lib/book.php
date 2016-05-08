<?php

namespace PS;

class Book {
	# constructor data
	private $db;

	# data from db
	private $id;
	private $isbn;
	private $title;
	private $publicationYear;
	private $publisher;
	private $count;
	private $description;

	public function __construct($db,
	                            $id              = null,
	                            $isbn            = null,
	                            $title           = null,
	                            $publicationYear = null,
	                            $publisher       = null,
	                            $count           = null,
	                            $description     = null)
	{
		$this->db              = $db;
		$this->id              = $id;
		$this->isbn            = $isbn;
		$this->title           = $title;
		$this->publicationYear = $publicationYear;
		$this->publisher       = $publisher;
		$this->count           = $count;
		$this->description     = $description;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getIsbn()
	{
		return $this->isbn;
	}

	public function getTitle()
	{
		return $this->title;
	}

	public function getPublicationYear()
	{
		return $this->publicationYear;
	}

	public function getPublisher()
	{
		return $this->publisher;
	}

	public function getCount()
	{
		return $this->count;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function isAvailable()
	{
		return $this->count > 0;
	}

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM book WHERE ';
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

		# does the book exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id              = (int)$row['id'];
		$this->isbn            = $row['isbn'];
		$this->title           = $row['title'];
		$this->publicationYear = $row['publicationYear'];
		$this->publisher       = (int)$row['publisher'];
		$this->count           = (int)$row['count'];
		$this->description     = $row['description'];

		return true;
	}

	public function search($mode)
	{
		# missing parameters
		if ($mode == null)
			return false;

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT id, isbn, title, publicationYear, publisher, count, description ' .
			         'FROM book';
			$placeholders = array();
			break;
		case 'plain+publishers':
			$query = 'SELECT book.id, book.isbn, book.title, book.publicationYear, book.publisher, book.count, book.description, publisher.name AS publisherName ' .
			         'FROM book INNER JOIN publisher ON book.publisher = publisher.id ' .
			         'ORDER BY book.id';
			$placeholders = array();
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

