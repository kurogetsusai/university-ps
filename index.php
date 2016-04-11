<?php
# Version     : 0.0.0.1
# Dependencies:
# - PHP 5.4
# - PHP mod: rewrite
# - the .htaccess file, make sure that your server allows overrides
#   (check /etc/apache2/apache2.conf if <Directory /var/www/> has 'AllowOverride All')

# load config
require 'etc/config.php';
define('CURRENT_PATH'  , $_GET['cmd'] == '' ? GLOBAL_ROOT : GLOBAL_ROOT . '/' . $_GET['cmd']);

# load the loader
# TODO

# load modules
# TODO

# load page
require 'page/home.php';	# TODO replace with loader

