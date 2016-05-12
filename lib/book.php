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
	private $totalCount;
	private $availableCount;
	private $description;

	public function __construct($db,
	                            $id              = null,
	                            $isbn            = null,
	                            $title           = null,
	                            $publicationYear = null,
	                            $publisher       = null,
	                            $totalCount      = null,
	                            $availableCount  = null,
	                            $description     = null)
	{
		$this->db              = $db;
		$this->id              = $id;
		$this->isbn            = $isbn;
		$this->title           = $title;
		$this->publicationYear = $publicationYear;
		$this->publisher       = $publisher;
		$this->totalCount      = $totalCount;
		$this->availableCount  = $availableCount;
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

	public function getTotalCount()
	{
		return $this->totalCount;
	}

	public function getAvailableCount()
	{
		return $this->availableCount;
	}

	public function getDescription()
	{
		return $this->description;
	}

	public function isAvailable()
	{
		return $this->availableCount > 0;
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
		$this->totalCount      = (int)$row['totalCount'];
		$this->availableCount  = (int)$row['availableCount'];
		$this->description     = $row['description'];

		return true;
	}

	public function setData($data)
	{
		foreach ($data as $key => $item)
			switch ($key) {
			case 'id':
				$this->id = $item;
				break;
			case 'isbn':
				$this->isbn = $item;
				break;
			case 'title':
				$this->title = $item;
				break;
			case 'publicationYear':
				$this->publicationYear = $item;
				break;
			case 'publisher':
				$this->publisher = $item;
				break;
			case 'totalCount':
				$this->totalCount = $item;
				break;
			case 'availableCount':
				$this->availableCount = $item;
				break;
			case 'description':
				$this->description = $item;
				break;
			}
	}

	public function saveDataToDb($mode, $input)
	{
		return false;	# TODO
	}

	public function search($mode, $input = null, $filter = null, $order = 0)
	{
		# missing parameters
		if ($mode == null)
			return false;

		$placeholders = [];
		$filters = '';

		# parse filters
		$i = 0;
		if ($filter != null)
			foreach ($filter as $key => $item) {
				# column whitelist
				switch ($key) {
				case 'title':
				case 'isbn':
					$filter_key = $key;
					break;
				case 'publisherName':
					if ($mode == 'plain+publishers' or $mode == 'books+publishers+authors')
						$filter_key = 'publisher.name';
					else
						$filter_key = null;
					break;
				case 'authorsName':
					if ($mode == 'books+publishers+authors')
						$filter_key =
							'(SELECT GROUP_CONCAT(writer.name) ' .
							'FROM author INNER JOIN writer ON author.writer = writer.id ' .
							'WHERE author.book = book.id ' .
							'LIMIT 1)';
					else
						$filter_key = null;
					break;
				case 'authorsSurname':
					if ($mode == 'books+publishers+authors')
						$filter_key =
							'(SELECT GROUP_CONCAT(writer.surname) ' .
							'FROM author INNER JOIN writer ON author.writer = writer.id ' .
							'WHERE author.book = book.id ' .
							'LIMIT 1)';
					else
						$filter_key = null;
					break;
				default:
					$filter_key = null;
				}

				if ($filter_key == null)
					break;

				$placeholders[':filter_' . $i] = '%' . $item . '%';
				if ($i == 0)
					$filters .= ' WHERE ' . $filter_key . ' LIKE :filter_' . $i;
				else
					$filters .= ' AND ' . $filter_key . ' LIKE :filter_' . $i;
				++$i;
			}

		# order mode
		switch ($order) {
		default:
		case 0:
			$orders = 'title';
			break;
		case 1:
			$orders = 'authorsSurname';
			break;
		case 2:
			$orders = 'isbn';
			break;
		case 3:
			$orders = 'publicationYear';
			break;
		}

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT * ' .
			         'FROM book' . $filters . ' ' .
			         'ORDER BY ' . $orders;
			break;
		case 'plain+publishers':
			$query = 'SELECT book.id, book.isbn, book.title, book.publicationYear, book.publisher, book.totalCount, book.availableCount, book.description, publisher.name AS publisherName ' .
			         'FROM book INNER JOIN publisher ON book.publisher = publisher.id' . $filters . ' ' .
			         'ORDER BY ' . $orders;
			break;
		case 'books+publishers+authors':
			$query = 'SELECT book.id, book.isbn, book.title, book.publicationYear, book.publisher, book.totalCount, book.availableCount, book.description, ' .
			         'publisher.name AS publisherName, ' .
			         '(SELECT GROUP_CONCAT(writer.name) ' .
			         'FROM author INNER JOIN writer ON author.writer = writer.id ' .
			         'WHERE author.book = book.id ' .
			         'LIMIT 1) AS authorsName, ' .
			         '(SELECT GROUP_CONCAT(writer.surname) ' .
			         'FROM author INNER JOIN writer ON author.writer = writer.id ' .
			         'WHERE author.book = book.id ' .
			         'LIMIT 1) AS authorsSurname ' .
			         'FROM book INNER JOIN publisher ON book.publisher = publisher.id' . $filters . ' ' .
			         'ORDER BY ' . $orders;
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

