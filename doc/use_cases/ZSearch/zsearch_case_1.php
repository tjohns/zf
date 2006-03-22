<?php

/**
 * ZSearch Indexing Example - Create a new index and add a
 * document to search.  [Under Construction]
 *
 * ZSearch is an implementation of the Java Lucene engine
 * written entirely in PHP 5.  It is fully compatible with the
 * binary format of the Lucene index files.
 *
 * Base installation of ZSearch requires no database server or
 * even installing a PHP extension, although added performance and
 * features are available if you can use one or both.
 */


/**
 * The second argument tells ZSearch to create a new index
 * instead of opening an existing one.
 */
$index = new ZSearch('/tmp/my_index', true);


/**
 * Create a new document with two fields: title and body.  Each
 * field will be independently searchable.
 */
$myDoc = new ZSearchDocument();
$myDoc->setField('title', 'ZSearch Example Title');
$myDoc->setField('body', 'The body of your document goes here.');


/**
 * Add the document to the index.  It is now searchable.
 */
$index->addDocument($myDoc);


