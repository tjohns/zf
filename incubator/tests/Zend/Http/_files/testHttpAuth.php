<?php
$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
$method = $_GET['method'];
if (! $method) $method = 'Basic';

if (! $user || ! $pass || $user != $_GET['user'] || $pass != $_GET['pass']) {
	header('WWW-Authenticate: ' . $method . ' realm="ZendTest"');
    header('HTTP/1.0 401 Unauthorized');
}

echo serialize($_GET), "\n", $_SERVER['PHP_AUTH_USER'], "\n", $_SERVER['PHP_AUTH_PW'], "\n";