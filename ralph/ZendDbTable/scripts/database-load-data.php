<?php

include 'include/bootstrap.php';

$authorTable = new Zend_Db_Table('author');
$ralphId = $authorTable->insert(array('first_name' => 'Ralph', 'last_name' => 'Schindler'));
$mattId  = $authorTable->insert(array('first_name' => 'Matthew', 'last_name' => 'Weier O\'Phinney'));

$bookTable = new Zend_Db_Table('book');
$bookTable->insert(array('author_id' => $ralphId, 'title' => 'Guide To ZF'));
$bookTable->insert(array('author_id' => $mattId,  'title' => 'Definitive Guide To ZF'));
$bookTable->insert(array('author_id' => $ralphId, 'title' => 'PHP In A Nutshell'));
$bookTable->insert(array('author_id' => $ralphId, 'title' => 'PHP Patterns'));
$bookTable->insert(array('author_id' => $mattId, 'title' => 'Zend_Form the Encyclepdia'));
