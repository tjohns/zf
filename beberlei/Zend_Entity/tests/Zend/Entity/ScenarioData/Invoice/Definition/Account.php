<?php
/*
 * Mapping definition for entity Account
 */

$def = new Zend_Entity_Mapper_Definition_Entity;

$def->setClass('Account');
$def->setTable('accounts');

$def->addPrimaryKey('id', array('columnName'=>'account_id'));

$def->addProperty('accountNo');

return $def;
