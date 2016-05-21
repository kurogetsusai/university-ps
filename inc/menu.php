<?php
global $user;
?>
<div style="margin: 0px auto; margin-right: 10%; text-align: right;">Jesteś zalogowany jako <?= $user->getFullName() ?></div>
<img src="<?= GLOBAL_ROOT ?>/gfx/books.png" class="logo-small" />
<div class="main-menu">
<?php if ($user->getPermission() === 1) { ?>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/users">użytkownicy</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/writers">autorzy</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/publishers">wydawnictwa</a>
<?php } ?>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/books">książki</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/home">zamówienia</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/password">zmień hasło</a>
	<a class="main-menu-button" href="<?= GLOBAL_ROOT ?>/logout" style="margin-right: 10%;">wyloguj</a>
</div>
<script src="<?= GLOBAL_ROOT ?>/js/buttons.js"></script>
