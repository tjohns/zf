<?php

$libraryPath = realpath(dirname(__FILE__) . '/../../library/');

if (isset($_ENV['ZF_STANDARD_TRUNK'])) {
    $libraryPath .= PATH_SEPARATOR . $_ENV['ZF_STANDARD_TRUNK'];
}

set_include_path($libraryPath . PATH_SEPARATOR . get_include_path());

$ip = get_include_path();

$loaded = @include 'Zend/Loader/Autoloader.php';

if (!$loaded) {
    echo 'ZF Standard Trunk was not found in your include path, please set env var ZF_STANDARD_TRUNK to point to the library within it.';
    exit(1);
}

Zend_Loader_Autoloader::getInstance();

$dbAdapter = Zend_Db::factory('Pdo_Sqlite', array('dbname' => ':memory:'));
$dbAdapter->getConnection();

Zend_Db_Table::setDefaultAdapter($dbAdapter);

// create table
$schema = <<<EOS
CREATE TABLE author (
    id INTEGER PRIMARY KEY,
    first_name CHAR(25),
    last_name CHAR(35)
);
EOS;

$dbAdapter->query($schema);

$schema = <<<EOS
CREATE TABLE book (
    id INTEGER PRIMARY KEY,
    author_id INTEGER,
    title CHAR(50)
);
EOS;

$dbAdapter->query($schema);

$schema = <<<EOS
CREATE TABLE genre (
    id INTEGER PRIMARY KEY,
    name CHAR(50)
);
EOS;

$dbAdapter->query($schema);

$schema = <<<EOS
CREATE TABLE book_to_genre (
    id INTEGER PRIMARY KEY,
    book_id INTEGER,
    genre_id INTEGER
);
EOS;

$dbAdapter->query($schema);

// insert data
$genreTable = new Zend_Db_Table('genre');
$genreTable->insert(array('name' => 'Zend'));
$genreTable->insert(array('name' => 'PHP'));

$bookToGenreTable = new Zend_Db_Table('book_to_genre');

$authorTable = new Zend_Db_Table('author');
$authorTable->insert(array('first_name' => 'Ralph', 'last_name' => 'Schindler'));
$authorTable->insert(array('first_name' => 'Matthew', 'last_name' => 'Weier O\'Phinney'));

$bookTable = new Zend_Db_Table('book');
$bookTable->insert(array('author_id' => 1, 'title' => 'Guide To ZF'));
$bookToGenreTable->insert(array('book_id' => 1, 'genre_id' => 1));

$bookTable->insert(array('author_id' => 2,  'title' => 'Definitive Guide To ZF'));
$bookToGenreTable->insert(array('book_id' => 2, 'genre_id' => 1));

$bookTable->insert(array('author_id' => 1, 'title' => 'PHP In A Nutshell'));
$bookToGenreTable->insert(array('book_id' => 3, 'genre_id' => 2));

$bookTable->insert(array('author_id' => 1, 'title' => 'PHP Patterns General'));
$bookToGenreTable->insert(array('book_id' => 4, 'genre_id' => 2));

$bookTable->insert(array('author_id' => 2,  'title' => 'Zend_Form the Encyclepdia'));
$bookToGenreTable->insert(array('book_id' => 5, 'genre_id' => 1));

$bookTable->insert(array('author_id' => 1, 'title' => 'PHP Patterns IN ZF'));
$bookToGenreTable->insert(array('book_id' => 6, 'genre_id' => 1));
$bookToGenreTable->insert(array('book_id' => 6, 'genre_id' => 2));

unset($genreTable, $bookToGenreTable, $authorTable, $bookTable);




