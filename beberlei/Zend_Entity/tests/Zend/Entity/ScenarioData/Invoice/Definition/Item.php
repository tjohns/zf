<?php
/*
 * Mapping definition for entity Item
 */

$def = new Zend_Entity_Mapper_Definition_Entity;

$def->setClass('Item');
$def->setTable('items');

$def->addPrimaryKey('id', array('columnName'=>'item_id'));

$def->addProperty('name');
$def->addProperty('sku');

return $def;
