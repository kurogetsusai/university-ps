<?php

namespace PS;

class Writer {
	# constructor data
	private $db;

	# data from db
	private $id;
	private $name;
	private $surname;

	public function __construct($db, $id = null, $name = null, $surname = null)
	{
		$this->db   = $db;
		$this->id   = $id;
		$this->name = $name;
		$this->surname = $surname;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getSurname()
	{
		return $this->surname;
	}

	public function getFullName()
	{
		return $this->name . ' ' . $this->surname;
	}

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM writer WHERE ';
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

		# does the writer exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id      = (int)$row['id'];
		$this->name    = $row['name'];
		$this->surname = $row['surname'];

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
			         'FROM writer';
			$placeholders = array();
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

