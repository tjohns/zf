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
                'columns' => array('author_id'),
                'refTableClass' => 'author',
                'refColumns' => array('id')
                )
            )
        )
    ));

$authorTable = new Zend_Db_Table('author', $definition);
$authors = $authorTable->fetchAll();

foreach ($authors as $author) {
    echo $author->id . ': ' . $author->first_name . ' ' . $author->last_name . PHP_EOL;
    $books = $author->findDependentRowset('book');
    foreach ($books as $book) {
        echo '    ' . $book->title . PHP_EOL;
    }
}

