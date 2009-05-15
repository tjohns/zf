<?php

include_once 'include/bootstrap.php';

$dbAdapter->query('DROP TABLE IF EXISTS author');
$dbAdapter->query('DROP TABLE IF EXISTS book');

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


