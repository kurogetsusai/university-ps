<?php

namespace PS;

class Database {

	private $loader;

	public $base;

	public function __construct($loader)
	{
		$this->loader = $loader;
	}

	public function connect($host, $base, $login, $password, $engine = 'mysql', $charset = 'utf8')
	{
		try {
			$this->base = new \PDO($engine .
			            ':host=' . $host   .
			          ';dbname=' . $base   .
			         ';charset=' . $charset, $login, $password);
			$this->base->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_WARNING);
		} catch (\PDOException $e) {
			$this->loader->loadModule('inc/database-connection-error', true);
			die();
		}
	}

}

