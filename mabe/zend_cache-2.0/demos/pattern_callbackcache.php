<?php

namespace Zend;

function autoload($class)
{
    $libdir    = __DIR__ . '/../library';
    $classfile = str_replace('\\', '/', $class) . '.php';
    include_once $libdir . '/' . $classfile;
}
spl_autoload_register('zend\autoload');

$cache = new Cache\Storage(array(
    'storage' => 'Memory',
    // 'plugins' => array(),
    'options' => array('ttl' => 5),
));
$callbackCache = Cache\Pattern::factory('CallbackCache', array(
    'storage' => $cache,
));


// for this demo we clear all cached items before
$cache->clear();

$runs = 5;

function retAndOutTime() {
    echo time();
    return time();
}

echo 'This demo call a function returning and displaying the current timestamp '.$runs.' times' . PHP_EOL
   . 'using the CallbackCache pattern.' . PHP_EOL
   . '-> You should see the same time returned and displayed as long as your configured ttl (5s)' . PHP_EOL . PHP_EOL;

for ($i=0; $i < $runs; $i++) {
    echo 'Starting call function ('.($i+1).'. call)' . PHP_EOL;
    echo '------------------------------------------' . PHP_EOL;
    echo 'Display: ';
    $ret = $callbackCache->call('\Zend\retAndOutTime'); 
    echo PHP_EOL . 'Return:  ' . $ret . PHP_EOL;
    echo '------------------------------------------' . PHP_EOL;

    echo 'run '.($i+1).' finished' . PHP_EOL;

    if ($i+1 < $runs) {
        echo 'sleep(2) before next run .';
        sleep(1); echo '.';
        sleep(1); echo '.';
        echo ' OK' . PHP_EOL . PHP_EOL;
    }
}


