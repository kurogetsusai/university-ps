<?php

namespace PS;

class Town {
	# constructor data
	private $db;

	# data from db
	private $id;
	private $name;
	private $postCode;

	public function __construct($db, $id = null, $name = null, $postCode = null)
	{
		$this->db       = $db;
		$this->id       = $id;
		$this->name     = $name;
		$this->postCode = $postCode;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getPostCode()
	{
		return $this->postCode;
	}

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM town WHERE ';
		switch($mode) {
		case 'id':
			$query .= 'id = :id';
			break;
		case 'name':
			$query .= 'name = :name';
			break;
		case 'postCode':
			$query .= 'postCode = :postCode';
			break;
		default:
			return false;	# invalid mode = abort
		}
		$query .= ' LIMIT 1';

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute(array(':' . $mode => $input));
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		# does the town exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id       = (int)$row['id'];
		$this->name     = $row['name'];
		$this->postCode = $row['postCode'];

		return true;
	}
}

