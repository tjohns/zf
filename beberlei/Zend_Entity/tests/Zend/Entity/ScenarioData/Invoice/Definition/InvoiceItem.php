<?php
/*
 * Mapping definition for entity InvoiceItem
 */

$def = new Zend_Entity_Mapper_Definition_Entity;

$def->setClass('InvoiceItem');
$def->setTable('invoice_items');

// The underlying table has a composite primary key
$def->addCompositeKey(array('invoice_id', 'line_no'));

$def->addProperty('lineNo');
$def->addProperty('taxable');
$def->addProperty('description');
$def->addProperty('amount');

// Directly loads the related Item object into the InvoiceItem on the "item" 
// property.
$def->addManyToOne('item', array( 
    'columnName' => 'item_id', 
    'class' => 'Item', 
    'foreignKey' => 'item_id', 
    'load' => 'directly',
));

// Lazy loads the reference to the parent Invoice
$def->addManyToOne('invoice', array( 
    'columnName' => 'invoice_id', 
    'class' => 'Invoice', 
    'foreignKey' => 'invoice_id', 
    'load' => 'lazy',
));

return $def;
