<?php

include_once 'include/bootstrap.php';

$authorTable = new Zend_Db_Table('author');
$authors = $authorTable->fetchAll();
foreach ($authors as $author) {
    echo $author->id . ': ' . $author->first_name . ' ' . $author->last_name . PHP_EOL;
}

