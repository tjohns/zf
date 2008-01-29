<?php

set_include_path(
    '../library' . PATH_SEPARATOR .
    '../../../repo-trunk/library/'
    );

$target = './Zend/r';


require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

//var_dump();

$target = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $target);
if (($foundTarget = realpath($target)) === false) {
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $includePath) {
        if (($includePath = realpath($includePath)) === false) {
            continue;
        }
        $test = $includePath . DIRECTORY_SEPARATOR . $target;
        if (($foundTarget = realpath($test)) !== false) {
            break;
        }
        $foundTarget = false;
    }

    echo 'Derived = ' . $foundTarget;
} else {
    echo 'Provided = ' . $foundTarget;
}


