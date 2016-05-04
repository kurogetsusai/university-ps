<?php
namespace PS;

class Searcher {
	private $cols; //table columns. Must be an array or null (null = select *)
	private $table; //table name
	private $joins; //joins used in statemant. Must be an array of strings (pattern: [ "table;fk1;fk2", "table2;fk1;fk2", ... ]) or null if there aren't any joined tables
	private $conditions; //simple string like 'where id = 12' or something
	private $order; //simple string like 'order by id desc' or anything like that

	public function __construct($_cols, $_table, $_joins = null, $_conditions = null, $_order = null)
	{
		require_once "join.php";
		$this->cols       = $_cols;
		$this->table      = $_table;
		$this->conditions = $_conditions;
		$this->order      = $_order;
		if($_joins == null)
			$this->joins = null;
		else {
			$this->joins = array();
			foreach($_joins as $j)
				array_push($this->joins, new Join($j));
		}
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getCols()
	{
		return $this->cols;
	}

	public function getJoins()
	{
		return $this->joins;
	}

	public function getConditions()
	{
		return $this->conditions;
	}

	public function getOrder()
	{
		return $this->order();
	}

	public function setTable($newTable)
	{
		$this->table = $newTable;
	}

	public function setCols($newCols)
	{
		$this->cols = $newCols;
	}

	public function setJoins($newJoins)
	{
		$this->joins = null;
		if($newJoins != null) {
			$this->joins = array();
			foreach($_joins as $j)
				array_push($this->joins, new Join($j));
		}
	}

	public function setConditions($newConditions)
	{
		$this->conditions = $newConditions;
	}

	public function setOrder($newOrder)
	{
		$this->order = $newOrder;
	}


	/*
	 * returns a string with SQL statement
	 */
	public function getSQL()
	{
		$stmt = "select ";
		if($this->cols == null)
			$stmt .= "* ";
		else {
			for($i = 0; $i < count($this->cols); $i++) {
				$stmt .= $this->cols[$i];
				if($i != count($this->cols) - 1)
					$stmt .= ", ";
				else
					$stmt .= " ";
			}
		}
		$stmt .= "from $this->table ";
		if($this->joins != null) {
			foreach($this->joins as $j)
				$stmt .= $j->getSQL()." ";
		}
		if($this->conditions != null)
			$stmt .= $this->conditions." ";
		if($this->order != null)
			$stmt .= $this->order;
		return $stmt;
	}

	/*
	 * returns array with query results or null if an exception occurs
	 */
	public function getResults()
	{
		try {
			$stmt = $this->db->base->prepare($this->getSQL());
			$stmt->execute(array(':' . $mode => $input));
			$row = $stmt->fetch(\PDO::FETCH_ASSOC);
		}
		catch(PDOException e)
			return null;
	}
}

