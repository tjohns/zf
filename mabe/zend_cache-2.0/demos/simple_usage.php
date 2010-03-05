<?php

namespace zend;

function autoload($class)
{
    $libdir    = __DIR__ . '/../library';
    $classfile = str_replace('\\', '/', $class) . '.php';
    include_once $libdir . '/' . $classfile;
}
spl_autoload_register('zend\autoload');


// create an instance of zend\cache\storageAdapter\Variable
$options = array(
    'ttl' => 10
);
$cache = Cache::storageAdapterFactory('Variable', $options);


$set = $cache->set('MyData', 'MyId', array('tags' => array('tag1', 3 => 'tag2', 5, 5, 0)));
echo '->set("MyData", "MyId") : ' . var_export($set, true) . PHP_EOL;

$exist = $cache->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cache->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cache->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

$remove = $cache->remove('MyId');
echo '->remove("MyId") : ' . var_export($remove, true) . PHP_EOL;

$exist = $cache->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cache->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cache->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

// enable write control & IgnoreUserAbort
$cache = new Cache(array(
    'storage' => $cache,
    'plugins' => array('WriteControl', 'IgnoreUserAbort'),
    'options' => array(
        'removeOnFailure' => true,
        'exitOnAbort'     => true,
        'ttl'             => 100
    )
));

$set = $cache->set('MyData', 'MyId', array('tags' => array('tag1', 3 => 'tag2', 5, 5, 0)));
echo '->set("MyData", "MyId") : ' . var_export($set, true) . PHP_EOL;

$exist = $cache->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cache->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cache->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

$remove = $cache->remove('MyId');
echo '->remove("MyId") : ' . var_export($remove, true) . PHP_EOL;

$exist = $cache->exists('MyId');
echo '->exists("MyId") : ' . var_export($exist, true) . PHP_EOL;

$data  = $cache->get('MyId');
echo '->get("MyId") : ' . var_export($data, true) . PHP_EOL;

$info = $cache->info('MyId');
echo '->info("MyId") : ' . var_export($info, true) . PHP_EOL;

