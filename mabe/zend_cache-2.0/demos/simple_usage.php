<?php

namespace zend;

function autoload($class)
{
    $libdir    = __DIR__ . '/../library';
    $classfile = str_replace('\\', '/', $class) . '.php';
    include_once $libdir . '/' . $classfile;
}
spl_autoload_register('zend\autoload');


// create an instance of zend\cache\adapter\Variable
$options = array(
    'ttl' => 10
);
$cacheAdapter = new cache\adapter\Variable($options); // Cache::adapterFactory('Variable', $options);


$set = $cacheAdapter->set('MyData', 'MyId', array('tags' => array('tag1', 3 => 'tag2', 5, 5, 0)));
echo '->set("MyData", "MyId") : ' . var_export($set, true) . PHP_EOL;

$exist = $cacheAdapter->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cacheAdapter->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cacheAdapter->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

$remove = $cacheAdapter->remove('MyId');
echo '->remove("MyId") : ' . var_export($remove, true) . PHP_EOL;

$exist = $cacheAdapter->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cacheAdapter->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cacheAdapter->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

// enable write control
$cacheAdapter = new cache\plugin\WriteControl(array(
    'adapter' => $cacheAdapter
));

$set = $cacheAdapter->set('MyData', 'MyId', array('tags' => array('tag1', 3 => 'tag2', 5, 5, 0)));
echo '->set("MyData", "MyId") : ' . var_export($set, true) . PHP_EOL;

$exist = $cacheAdapter->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cacheAdapter->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cacheAdapter->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

$remove = $cacheAdapter->remove('MyId');
echo '->remove("MyId") : ' . var_export($remove, true) . PHP_EOL;

$exist = $cacheAdapter->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cacheAdapter->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cacheAdapter->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;
