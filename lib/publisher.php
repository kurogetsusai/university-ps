<?php

namespace PS;

class Publisher {
	# constructor data
	private $db;

	# data from db
	private $id;
	private $name;

	public function __construct($db, $id = null, $name = null)
	{
		$this->db   = $db;
		$this->id   = $id;
		$this->name = $name;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM publisher WHERE ';
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

		# does the publisher exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id   = (int)$row['id'];
		$this->name = $row['name'];

		return true;
	}

	public function search($mode, $input = null, $filter = null)
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
				case 'name':
					$filter_key = $key;
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

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT * ' .
			         'FROM publisher' . $filters;
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

