<?php

namespace PS;

class Author {
	# constructor data
	private $db;

	# data from db
	private $id;
	private $book;
	private $writer;

	public function __construct($db, $id = null, $book = null, $writer = null)
	{
		$this->db   = $db;
		$this->id   = $id;
		$this->book = $book;
		$this->writer = $writer;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getBook()
	{
		return $this->book;
	}

	public function getWriter()
	{
		return $this->writer;
	}

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM author WHERE ';
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

		# does the author exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id     = $row['id'];
		$this->book   = $row['book'];
		$this->writer = $row['writer'];

		return true;
	}

	public function search($mode, $input)
	{
		# missing parameters
		if ($mode == null)
			return false;

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT * ' .
			         'FROM author';
			$placeholders = array();
			break;
		case 'book':
			$query = 'SELECT * ' .
			         'FROM author ' .
			         'WHERE book = :book';
			$placeholders = array(':book' => $input);
			break;
		case 'writer':
			$query = 'SELECT * ' .
			         'FROM author ' .
			         'WHERE writer = :writer';
			$placeholders = array(':writer' => $input);
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

