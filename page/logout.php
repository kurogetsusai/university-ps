<?php
global $user;

# entry only for logged in
if (!$user->isLoggedIn()) {
	header('Location: ' . GLOBAL_ROOT . '/login');
	exit();
}

$user->logOut();
header('Location: ' . (GLOBAL_ROOT != '' ? GLOBAL_ROOT : '/'));
exit();

