<?php

namespace PS;

class User {
	# constructor data
	private $loader;
	private $db;
	private $password_cost;

	# state
	private $logged_in = false;
	private $request_data_result = null;

	# temp
	private $plain_password;

	# data from db
	private $id;
	private $pesel;
	private $password;
	private $name;
	private $surname;
	private $town;
	private $street;
	private $houseNumber;
	private $permission;

	public function __construct($loader, $db, $password_cost = 11)
	{
		$this->loader        = $loader;
		$this->db            = $db;
		$this->password_cost = $password_cost;
	}

	private function calcPasswordHash()
	{
		$this->password = password_hash(
			$this->plain_password,
			PASSWORD_DEFAULT,
			[
				'cost' => $this->password_cost
			]
		);
	}

	private function setSession()
	{
		$_SESSION['user'] = array(
			'id'          => $this->id,
			'pesel'       => $this->pesel,
			'password'    => $this->password,
			'name'        => $this->name,
			'surname'     => $this->surname,
			'town'        => $this->town,
			'street'      => $this->street,
			'houseNumber' => $this->houseNumber,
			'permission'  => $this->permission
		);
	}

	private function respawnFromSession()
	{
		$this->id          = $_SESSION['user']['id'];
		$this->pesel       = $_SESSION['user']['pesel'];
		$this->password    = $_SESSION['user']['password'];
		$this->name        = $_SESSION['user']['name'];
		$this->surname     = $_SESSION['user']['surname'];
		$this->town        = $_SESSION['user']['town'];
		$this->street      = $_SESSION['user']['street'];
		$this->houseNumber = $_SESSION['user']['houseNumber'];
		$this->permission  = $_SESSION['user']['permission'];
	}

	public function isLoggedIn()
	{
		return $this->logged_in;
	}

	public function getRequestDataResult()
	{
		return $this->request_data_result;
	}

	public function clearUserData()
	{
		$this->logged_in           = false;
		$this->request_data_result = null;
		$this->plain_password      = null;
		$this->id                  = null;
		$this->pesel               = null;
		$this->password            = null;
		$this->name                = null;
		$this->surname             = null;
		$this->town                = null;
		$this->street              = null;
		$this->houseNumber         = null;
		$this->permission          = null;
	}

	public function processRequestData()
	{
		$login_status = false;
		$result = 0;

		# try to log in using session
		if (
			!$login_status &&
			isset($_SESSION['user'])
		) {
			$this->respawnFromSession();
			$this->logged_in = true;
			$login_status = true;
		}

		# try to log in using password
		if (
			!$login_status &&
			isset($_POST['login_pesel']) &&
			isset($_POST['login_password'])
		) {
			switch ($this->logInUsingPassword($_POST['login_pesel'], $_POST['login_password'])) {
			case 0:
				$login_status = true;
				break;
			case 1:
				$result = 101;
				break;
			case 2:
				$result = 102;
				break;
			default:
				$result = 100;
			}
		}

		$this->request_data_result = $login_status ? 0 : $result;
	}

	public function getUserDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM user WHERE ';
		switch($mode) {
		case 'pesel':
			$query .= 'pesel = :pesel';
			break;
		case 'id':
			$query .= 'id = :id';
			break;
		default:
			return false;	# invalid mode = abort
		}
		$query .= ' LIMIT 1';

		# get user data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute(array(':' . $mode => $input));
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		# does the user exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id          = $row['id'];
		$this->pesel       = $row['pesel'];
		$this->password    = $row['password'];
		$this->name        = $row['name'];
		$this->surname     = $row['surname'];
		$this->town        = $row['town'];
		$this->street      = $row['street'];
		$this->houseNumber = $row['houseNumber'];
		$this->permission  = $row['permission'];

		return true;
	}

	public function logInUsingPassword($pesel, $password)
	{
		# Return codes:
		# 0 - OK
		# 1 - wrong pesel
		# 2 - wrong password

		# get user data (and check if he exists)
		if (!$this->getUserDataFromDb('pesel', $pesel)) {
			return 1;
		}

		$this->plain_password = $password;

		# check login data
		if (!password_verify($this->plain_password, $this->password)) {
			$this->clearUserData();
			return 2;
		}

		$this->logged_in = true;

		# check if password hash needs to be rehashed
		if (password_needs_rehash(
			$this->password,
			PASSWORD_DEFAULT,
			[
				'cost' => $this->password_cost
			]
		)) {
			$this->calcPasswordHash();
			# TODO	$this->saveUserToDb();
		}

		# set session data
		$this->setSession();

		return 0;
	}

	public function logOut()
	{
		unset($_SESSION['user']);
	}
}

