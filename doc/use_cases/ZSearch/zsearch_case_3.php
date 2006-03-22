<?php

/**
 * ZSearch Advanced Search Example
 *
 * ZSearch also includes a query parser that allows a single
 * string to specify multiple fields to search.
 */


/**
 * Open the search index for reading or searching.
 */
$bookIndex = new ZSearch('/path/to/movie_index');


/**
 * Query the index for movies that contain the term "Fun" in the
 * title and also have a rating of PG-13.
 */
$hits = $index->find('-title:Fun rating:PG13');


/**
 * List titles of each matching document.
 *
 * All three methods shown below are equivalent, you only need to
 * get the objects if you really need them.
 */
foreach ($hits as $hit) {
    // get the value of the document's "title" field directly from the hit (shortcut)
    // this should be the most common usage
    echo $hit->title;

    // get the document object and the "title" field's value from it (shortcut)
    echo $hit->getDocument()->title . "\n";

    // get the document object and then the "title" object and its value.
    echo $hit->getDocument()->getField('title')->getFieldValue() . "\n";

