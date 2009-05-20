<?php

include 'include/bootstrap.php';

$definition = new Zend_Db_Table_Definition(array(
    'author' => array(
        'name' => 'author',
        'dependentTables' => array('book')
        ),
    'book' => array(
        'name' => 'book',
        'referenceMap' => array(
            'author' => array(
                'columns' => 'author_id',
                'refTableClass' => 'author',
                'refColumns' => 'id'
                )
            )
        )
    ));

$bookTable = new Zend_Db_Table('book', $definition);
$book = $bookTable->find(1)->current();

echo $book->title . ' by ';
$author = $book->findParentRow('author');

echo $author->first_name . ' ' . $author->last_name . PHP_EOL;

echo PHP_EOL;
