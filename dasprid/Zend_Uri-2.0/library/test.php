<?php
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', '1');

require_once 'Zend/Uri/Uri.php';

$uri = Zend\Uri\Uri::factory('http://www.example.com/foo bar?alpha=beta+and omega');
assert('(string) $uri === "http://www.example.com/foo%20bar?alpha=beta+and+omega"');

$uri = Zend\Uri\Uri::factory('http://www.example.com');
assert('(string) $uri === "http://www.example.com/"');

$uri = Zend\Uri\Uri::factory('http://www.example.com/foo;bar');
assert('(string) $uri === "http://www.example.com/foo;bar"');

$uri = Zend\Uri\Uri::factory('http://www.example.com?foo=bar&baz=bat');
assert('(string) $uri === "http://www.example.com/?foo=bar&baz=bat"');

$uri = Zend\Uri\Uri::factory('http://www.example.com#foo bar');
assert('(string) $uri === "http://www.example.com/#foo%20bar"');

$uri = Zend\Uri\Uri::factory('http://foo@www.example.com');
assert('(string) $uri === "http://foo@www.example.com/"');

$uri = Zend\Uri\Uri::factory('http://foo:bar@www.example.com');
assert('(string) $uri === "http://foo:bar@www.example.com/"');

$uri = Zend\Uri\Uri::factory('http://www.example.com:90');
assert('(string) $uri === "http://www.example.com:90/"');

$uri = Zend\Uri\Uri::factory('http://www.example.com/füttern');
assert('(string) $uri === "http://www.example.com/f%C3%BCttern"');

$uri = Zend\Uri\Uri::factory('https://www.example.com');
assert('(string) $uri === "https://www.example.com/"');
assert('$uri instanceof \\Zend\\Uri\\Scheme\\Http');
assert('$uri instanceof \\Zend\\Uri\\Scheme\\Https');

$uri = Zend\Uri\Uri::factory('ftp://www.example.com/füttern');
assert('(string) $uri === "ftp://www.example.com/f%C3%BCttern"');