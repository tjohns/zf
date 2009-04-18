<?php
/*
 * Mapping definition for entity Address
 */

$def = new Zend_Entity_Mapper_Definition_Entity;

$def->setClass('Address');
$def->setTable('addresses');

$def->addPrimaryKey('id', array('columnName'=>'address_id'));

$def->addProperty('streetName');
$def->addProperty('unitNo');
$def->addProperty('addrLine2');
$def->addProperty('city');
$def->addProperty('state');
$def->addProperty('zipcode');

return $def;
