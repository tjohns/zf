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
$outputCache = Cache\Pattern::factory('OutputCache', array(
    'storage' => $cache,
));


// for this demo we clear all cached items before
$cache->clear();

$runs = 5;

echo 'This demo displays the current timestamp '.$runs.' times' . PHP_EOL
   . 'within an output cache of the same key.' . PHP_EOL
   . '-> You should see the same time displayed as long as your configured ttl (5s)' . PHP_EOL . PHP_EOL;

for ($i=0; $i < $runs; $i++) {
    echo 'Starting display current time ('.($i+1).' call)' . PHP_EOL;
    echo '------------------------------------------' . PHP_EOL;
    if (!$outputCache->start('key')) {
        echo 'time: '.time() . PHP_EOL;;
        $outputCache->end();
    }
    echo '------------------------------------------' . PHP_EOL;

    echo 'run '.($i+1).' finished' . PHP_EOL;

    if ($i+1 < $runs) {
        echo 'sleep(2) before next run .';
        sleep(1); echo '.';
        sleep(1); echo '.';
        echo ' OK' . PHP_EOL . PHP_EOL;
    }
}


