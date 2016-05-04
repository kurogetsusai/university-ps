<?php
namespace PS;

class Join {
	private $table;
	private $fk1;
	private $fk2;

	public function __construct($params)
	{
		$params = explode(";", $params);
		$this->table = $params[0];
		$this->fk1   = $params[1];
		$this->fk2   = $params[2];
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getFk1()
	{
		return $this->fk1;
	}

	public function getFk2()
	{
		return $this->fk2;
	}

	public function setParams($_table, $_fk1, $_fk2)
	{
		if($_table != null)
			$this->table = $_table;
		if($_fk1 != null)
			$this->fk1   = $_fk1;
		if($_fk2 != null)
			$this->fk2   = $_fk2;
	}

	public function getSQL() {
		return "join $this->table on $this->fk1 = $this->fk2";
	}
}

