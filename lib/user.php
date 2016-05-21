<?php

namespace PS;

class User {
	# constructor data
	private $db;
	private $password_cost;

	# state
	private $logged_in = false;
	private $request_data_result = null;
	private $request_data_strings = array(
		  0 => '',
		100 => 'Nieznany błąd (kod 100).',
		101 => 'Nie istnieje użytkownik o takim loginie.',
		102 => 'Złe hasło.'
	);

	# temp
	private $plain_password;

	# data from db
	private $id;
	private $pesel;
	private $password;
	private $name;
	private $surname;
	private $email;
	private $phone;
	private $town;
	private $postCode;
	private $street;
	private $houseNumber;
	private $permission;

	public function __construct($db, $password_cost = 11)
	{
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
			'email'       => $this->email,
			'phone'       => $this->phone,
			'town'        => $this->town,
			'postCode'    => $this->postCode,
			'street'      => $this->street,
			'houseNumber' => $this->houseNumber,
			'permission'  => $this->permission
		);
	}

	private function respawnFromSession()
	{
		$this->id          = (int)$_SESSION['user']['id'];
		$this->pesel       = $_SESSION['user']['pesel'];
		$this->password    = $_SESSION['user']['password'];
		$this->name        = $_SESSION['user']['name'];
		$this->surname     = $_SESSION['user']['surname'];
		$this->email       = $_SESSION['user']['email'];
		$this->phone       = $_SESSION['user']['phone'];
		$this->town        = $_SESSION['user']['town'];
		$this->postCode    = $_SESSION['user']['postCode'];
		$this->street      = $_SESSION['user']['street'];
		$this->houseNumber = $_SESSION['user']['houseNumber'];
		$this->permission  = (int)$_SESSION['user']['permission'];
	}

	public function isLoggedIn()
	{
		return $this->logged_in;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getPesel()
	{
		return $this->pesel;
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

	public function getEmail()
	{
		return $this->email;
	}

	public function getPhone()
	{
		return $this->phone;
	}

	public function getTown()
	{
		return $this->town;
	}

	public function getPostCode()
	{
		return $this->postCode;
	}

	public function getStreet()
	{
		return $this->street;
	}

	public function getHouseNumber()
	{
		return $this->houseNumber;
	}

	public function getPermission()
	{
		return $this->permission;
	}

	public function getRequestDataResult()
	{
		return $this->request_data_result;
	}

	public function getRequestDataString($code = null)
	{
		return ($code === null ? $this->request_data_strings[$this->request_data_result] : $this->request_data_strings[$code]);
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
		$this->email               = null;
		$this->phone               = null;
		$this->town                = null;
		$this->postCode            = null;
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

	public function getDataFromDb($mode, $input)
	{
		# missing parameters = abort
		if ($mode == null or $input == null)
			return false;

		# build the query
		$query = 'SELECT * FROM user WHERE ';
		switch($mode) {
		case 'id':
			$query .= 'id = :id';
			break;
		case 'pesel':
			$query .= 'pesel = :pesel';
			break;
		default:
			return false;	# invalid mode = abort
		}
		$query .= ' LIMIT 1';

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute(array(':' . $mode => $input));
		$row = $stmt->fetch(\PDO::FETCH_ASSOC);

		# does the user exist?

		# nope
		if (!isset($row['id'])) {
			return false;
		}

		# yep
		$this->id          = (int)$row['id'];
		$this->pesel       = $row['pesel'];
		$this->password    = $row['password'];
		$this->name        = $row['name'];
		$this->surname     = $row['surname'];
		$this->email       = $row['email'];
		$this->phone       = $row['phone'];
		$this->town        = $row['town'];
		$this->postCode    = $row['postCode'];
		$this->street      = $row['street'];
		$this->houseNumber = $row['houseNumber'];
		$this->permission  = (int)$row['permission'];

		return true;
	}

	public function setData($data)
	{
		foreach ($data as $key => $item)
			switch ($key) {
			case 'id':
				$this->id = $item;
				break;
			case 'pesel':
				$this->pesel = $item;
				break;
			case 'plain_password':
				$this->plain_password = $item;
				break;
			case 'name':
				$this->name = $item;
				break;
			case 'surname':
				$this->surname = $item;
				break;
			case 'email':
				$this->email = $item;
				break;
			case 'phone':
				$this->phone = $item;
				break;
			case 'town':
				$this->town = $item;
				break;
			case 'postCode':
				$this->postCode = $item;
				break;
			case 'street':
				$this->street = $item;
				break;
			case 'houseNumber':
				$this->houseNumber = $item;
				break;
			case 'permission':
				$this->permission = (int)$item;
				break;
			}
	}

	public function saveDataToDb($mode, $input)
	{
		switch ($mode) {
		case 'password':
			# save to db
			$stmt = $this->db->base->prepare('UPDATE user SET password = :password WHERE id = :id');
			$stmt->execute(array(
				':id'       => $this->id,
				':password' => $this->password
			));

			# check if that worked
			if ($stmt->rowCount() !== 1) {
				return false;
			}

			break;
		case 'new':
			# generate password
			$this->calcPasswordHash();

			# save to db
			$stmt = $this->db->base->prepare('INSERT INTO user (pesel, password, name, surname, email, phone, town, postCode, street, houseNumber, permission) ' .
			'VALUES (:pesel, :password, :name, :surname, :email, :phone, :town, :postCode, :street, :houseNumber, :permission)');
			$stmt->execute(array(
				':pesel'       => $this->pesel,
				':password'    => $this->password,
				':name'        => $this->name,
				':surname'     => $this->surname,
				':email'       => $this->email,
				':phone'       => $this->phone,
				':town'        => $this->town,
				':postCode'    => $this->postCode,
				':street'      => $this->street,
				':houseNumber' => $this->houseNumber,
				':permission'  => $this->permission
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
					$key == 'pesel' or
					$key == 'name' or
					$key == 'surname' or
					$key == 'email' or
					$key == 'phone' or
					$key == 'town' or
					$key == 'postCode' or
					$key == 'street' or
					$key == 'houseNumber' or
					$key == 'permission'
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
			$stmt = $this->db->base->prepare('UPDATE user SET ' . $data . ' WHERE id = :id');
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

	public function logInUsingPassword($pesel, $password)
	{
		# Return codes:
		# 0 - OK
		# 1 - wrong pesel
		# 2 - wrong password
		# 3 - cannot save to the db

		# get user data (and check if he exists)
		if (!$this->getDataFromDb('pesel', $pesel)) {
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
			if (!$this->saveDataToDb('password'))
				return 3;
		}

		# set session data
		$this->setSession();

		return 0;
	}

	public function logOut()
	{
		unset($_SESSION['user']);
	}

	public function changePassword($old, $new, $godMode = false)
	{
		# Return codes:
		# 0 - OK
		# 1 - user is not logged in
		# 2 - new password is empty
		# 3 - wrong old password
		# 4 - can't save new password to the db

		if (!$this->isLoggedIn() and !$godMode)
			return 1;

		if ($new == '')
			return 2;

		# check old password
		if (!$godMode and !password_verify($old, $this->password)) {
			return 3;
		}

		$this->plain_password = $new;
		$this->calcPasswordHash();
		if (!$this->saveDataToDb('password'))
			return 4;

		# update session data
		if (!$godMode)
			$this->setSession();

		return 0;
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
				case 'pesel':
				case 'town':
				case 'street':
				case 'permission':
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
			$orders = 'pesel';
			break;
		case 1:
			$orders = 'name, surname';
			break;
		case 2:
			$orders = 'surname, name';
			break;
		case 3:
			$orders = 'permission';
			break;
		}

		# get query
		switch ($mode) {
		case 'plain':
			$query = 'SELECT * ' .
			         'FROM user' . $filters . ' ' .
			         'ORDER BY ' . $orders;
			break;
		}

		# get data
		$stmt = $this->db->base->prepare($query);
		$stmt->execute($placeholders);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}
}

