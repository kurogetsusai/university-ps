<?php
# Version     : 0.0.0.1
# Dependencies:
# - PHP 5.4
# - Apache mod: rewrite
# - the .htaccess file, make sure that your server allows overrides
#   (check /etc/apache2/apache2.conf if <Directory /var/www/> has 'AllowOverride All')
#
# TODO
# - secure page/*.php files

# init
session_start();
$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : '';

# load config
require 'etc/config.php';
define('CURRENT_PATH'  , $cmd == '' ? GLOBAL_ROOT : GLOBAL_ROOT . '/' . $cmd);

# load the loader
require 'lib/loader.php';
$loader = new \PS\Loader($cmd, DEFAULT_PAGE, DEBUG_MODE);

# load modules
$loader->loadModule('lib/database');
$loader->loadModule('lib/user');

# init database
$db = new \PS\Database($loader);
$db->connect(DATABASE_HOST, DATABASE_BASE, DATABASE_USER, DATABASE_PASS);

# init user
$user = new \PS\User($loader, $db, USER_PASSWORD_COST);
$user->processRequestData();

# load page
$loader->loadModule('page/' . $loader->getPage());

