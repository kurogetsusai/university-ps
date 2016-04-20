<?php
# global
define('DEBUG_MODE'    , true);         # show errors
define('DEFAULT_LANG'  , 'pl');         # only for '<html lang="*">'
define('DEFAULT_PAGE'  , 'home');       # has to match 'page/*.php'
define('DEFAULT_TITLE' , 'Biblioteka'); # HTML's <title>
define('SESSION_TIME'  , 14515200);     # 60 * 60 * 24 * 7 * 2 * 24 = 24 weeks (~6 months)
define('COOKIE_TIME'   , 14515200);     # 60 * 60 * 24 * 7 * 2 * 24 = 24 weeks (~6 months)
define('GLOBAL_ROOT'   , '');           # root path (usually empty string)

# lib/database
define('DATABASE_HOST', 'localhost');
define('DATABASE_BASE', 'library');
define('DATABASE_USER', 'library');
define('DATABASE_PASS', 'V!OSvw^4QMY:Q4F+G');

# lib/user
define('USER_PASSWORD_COST', 11);
