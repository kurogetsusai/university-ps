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

	public function setData($data)
	{
		foreach ($data as $key => $item)
			switch ($key) {
			case 'id':
				$this->id = $item;
				break;
			case 'name':
				$this->name = $item;
				break;
			case 'surname':
				$this->surname = $item;
				break;
			}
	}

	public function saveDataToDb($mode, $input)
	{
		switch ($mode) {
		case 'new':
			# save to db
			$stmt = $this->db->base->prepare('INSERT INTO writer (name, surname) VALUES (:name, :surname)');
			$stmt->execute(array(
				':name'    => $this->name,
				':surname' => $this->surname
			));

			# check if that worked
			if ($stmt->rowCount() !== 1) {
				return false;
			}

			# get ID
			$this->id = $this->db->base->lastInsertId();

			break;
		case 'array_keys+object_properties':
			$placeholders = [];
			$data = '';
			$first = true;
			foreach ($input as $key => $item)
				if (
					$key == 'name' or
					$key == 'surname'
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

			# save to db
			$stmt = $this->db->base->prepare('UPDATE writer SET ' . $data . ' WHERE id = :id');
			$placeholders[':id'] = $this->id;
			$stmt->execute($placeholders);

			# check if that worked
			if ($stmt->rowCount() !== 1) {
				return false;
			}

			break;
		}

		return true;
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
				case 'name':
				case 'surname':
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

		# order mode
		switch ($order) {
		default:
		case 0:
			$orders = 'surname, name';
			break;
		case 1:
			$orders = 'name, surname';
			break;
		}

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT * ' .
			         'FROM writer' . $filters . ' ' .
			         'ORDER BY ' . $orders;
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

