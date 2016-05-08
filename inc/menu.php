<?php
global $user;
?>
<img src="<?= GLOBAL_ROOT ?>/gfx/books.png" class="logo-small" />
<div class="main-menu">
<?php if ($user->getPermission() === 1) { ?>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/">użytkownicy</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/">autorzy</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/">wydawnictwa</a>
<?php } ?>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/">książki</a>
<<<<<<< HEAD
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/home">zamówienia</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/password">zmień hasło</a>
=======
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/">zamówienia</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/">zmień hasło</a>
>>>>>>> origin/master
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/logout" style="margin-right: 20%;">wyloguj</a>
</div>
<script src="<?= GLOBAL_ROOT ?>/js/buttons.js"></script>
