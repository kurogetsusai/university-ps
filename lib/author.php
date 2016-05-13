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
		$this->id     = (int)$row['id'];
		$this->book   = (int)$row['book'];
		$this->writer = (int)$row['writer'];

		return true;
	}

	public function setData($data)
	{
		foreach ($data as $key => $item)
			switch ($key) {
			case 'id':
				$this->id = $item;
				break;
			case 'book':
				$this->book = $item;
				break;
			case 'writer':
				$this->writer = $item;
				break;
			}
	}

	public function saveDataToDb($mode)
	{
		switch ($mode) {
		case 'new':
			# save to db
			$stmt = $this->db->base->prepare('INSERT INTO author (book, writer) VALUES (:book, :writer)');
			$stmt->execute(array(
				':book'   => $this->book,
				':writer' => $this->writer
			));

			# check if that worked
			if ($stmt->rowCount() !== 1) {
				return false;
			}

			# get ID
			$this->id = $this->db->base->lastInsertId();

			break;
		}

		return true;
	}

	public function removeDataFromDb()
	{
		if ($this->id == null)
			return false;

		$stmt = $this->db->base->prepare('DELETE FROM author WHERE id = :id');
		$stmt->execute(array('id' => $this->id));

		# check if that worked
		if ($stmt->rowCount() !== 1) {
			return false;
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

