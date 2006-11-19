<?php

if (! isset($_GET['redirection'])) $_GET['redirection'] = 0;
$_GET['redirection']++;

if ($_GET['redirection'] < 4) {
	$target = 'http' . ($_SERVER['HTTPS'] ? 's://' : '://')  . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
	header('Location: ' . $target . '?redirection=' . $_GET['redirection']);
} else {
	var_dump($_GET);
	var_dump($_POST);
}