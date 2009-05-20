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
        ),
    'genre' => null,
    'book_to_genre' => array(
        'referenceMap' => array(
            'book' => array(
                'columns' => 'book_id',
                'refTableClass' => 'book',
                'refColumns' => 'id'
                ),
            'genre' => array(
                'columns' => 'genre_id',
                'refTableClass' => 'genre',
                'refColumns' => 'id'
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
        echo '    Book: ' . $book->title . PHP_EOL;
        $genreOutputArray = array();
        foreach ($book->findManyToManyRowset('genre', 'book_to_genre') as $genreRow) {
            $genreOutputArray[] = $genreRow->name;
        }
        echo '        Genre: ' . implode(', ', $genreOutputArray) . PHP_EOL;
    }
}
