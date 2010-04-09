<?php

namespace Zend;

function autoload($class)
{
    $libdir    = __DIR__ . '/../library';
    $classfile = str_replace('\\', '/', $class) . '.php';
    include_once $libdir . '/' . $classfile;
}
spl_autoload_register('zend\autoload');


// instanciate an cache storage adapter
$options = array(
    'ttl' => 10
);
$cache = Cache\Storage::adapterFactory('Variable', $options);


// read & write data
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


// enable WriteControl & IgnoreUserAbort
$cache = new Cache\Storage(array(
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


// use multiple interface
$set = $cache->setMulti(array(
    'MyId1' => 'MyData1',
    'MyId2' => 'MyData2',
    'MyId3' => 'MyData3',
), array('tags' => array('tag1', 3 => 'tag2', 5, 5, 0)));
echo '->setMulti(...) : ' . var_export($set, true) . PHP_EOL;

$exist = $cache->existsMulti(array(
    'MyId1', 'NotExist', 'MyId3'
));
echo '->existsMulti() : ' . var_export($exist, true) . PHP_EOL;

$data = $cache->getMulti(array(
    'MyId1', 'NotExist', 'MyId3'
));
echo '->getMulti() : ' . var_export($data, true) . PHP_EOL;

$info = $cache->infoMulti(array(
    'MyId1', 'NotExist', 'MyId3'
));
echo '->infoMulti() : ' . var_export($info, true) . PHP_EOL;

$remove = $cache->removeMulti(array(
    'MyId1', 'NotExist', 'MyId3'
));
echo '->removeMulti() : ' . var_export($remove, true) . PHP_EOL;


// use getDelayed
$set = $cache->setMulti(array(
    'MyId1' => 'MyData1',
    'MyId2' => 'MyData2',
    'MyId3' => 'MyData3',
), array('tags' => array('tag1', 3 => 'tag2', 5, 5, 0)));
echo '->setMulti(...) : ' . var_export($set, true) . PHP_EOL;

$delayed = $cache->getDelayed(array(
    'MyId1', 'NotExist', 'MyId3'
));
echo '->getDelayed(...) : ' . var_export($delayed, true) . PHP_EOL;

while ( ($item = $cache->fetch(Cache\Storage::FETCH_ASSOC)) !== false ) {
    echo '->fetch(FETCH_ASSOC) : ' . var_export($item, true) . PHP_EOL;
}

$cache->getDelayed(array(
    'MyId1', 'NotExist', 'MyId3'
), Cache\Storage::SELECT_KEY_VALUE | Cache\Storage::SELECT_TAGS | Cache\Storage::SELECT_MTIME);
echo '->getDelayed(..., SELECT_KEY_VALUE | SELECT_TAGS | SELECT_MTIME) : ' . var_export($delayed, true) . PHP_EOL;
$items = $cache->fetchAll(Cache\Storage::FETCH_OBJ);
echo '->fetchAll(FETCH_OBJ) : ' . var_export($items, true) . PHP_EOL;

