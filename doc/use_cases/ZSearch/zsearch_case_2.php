<?php

require_once 'ZSearch/ZSearch.php';

/**
 * ZSearch Simple Search Example
 *
 * ZSearch allows you to search all fields of all documents
 */


/**
 * Open the index for searching.
 */
$index = new ZSearch('/tmp/index');


/**
 * Query the index for documents that contain the term "zend studio"
 * but do not contain the term "install".
 */
$hits = $index->find('nntp');


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
}

