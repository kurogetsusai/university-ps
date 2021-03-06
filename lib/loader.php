<?php

namespace PS;

class Loader {
	private $debugMode;
	private $defaultPage;
	private $page;
	private $params;

	public function __construct($cmd, $defaultPage = 'home', $debugMode = false)
	{
		$this->params      = explode('/', $cmd);
		$this->debugMode   = $debugMode;
		$this->defaultPage = $defaultPage;

		if ($debugMode) {
			error_reporting(E_ALL);
			ini_set('display_errors', 1);
			ini_set('track_errors'  , 1);
			ini_set('html_errors'   , 1);
		} else {
			error_reporting(E_NONE);
			ini_set('display_errors', 0);
			ini_set('track_errors'  , 0);
			ini_set('html_errors'   , 0);
		}
	}

	public function loadModule($module, $require = true)
	{
		$file = $module . '.php';

		if ($require)
			require $file;
		else
			include $file;
	}

	public function getParams()
	{
		return $this->params;
	}

	public function getPage()
	{
		if ($this->page == '') {
			if ($this->getParams()[0] == '') {
				$this->page = $this->defaultPage;
			} elseif (is_readable('page/' . $this->params[0] . '.php')) {
				$this->page = $this->params[0];
			} else {
				$this->page = $this->defaultPage;
			}
		}
		return $this->page;
	}

	public function redirect($destination)
	{
		$dest = explode('/', $destination);
		if ($destination == '/')
			$location = (GLOBAL_ROOT != '' ? GLOBAL_ROOT : '/');
		elseif (is_readable('page' . $dest[1] . '.php'))
			$location = GLOBAL_ROOT . $destination;
		else
			$location = $destination;

		header('Location: ' . $location);
		exit();
	}
}

