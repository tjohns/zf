<?php
/*
 * Mapping definition for entity Invoice
 */

$def = new Zend_Entity_Mapper_Definition_Entity;

$def->setClass('Invoice');
$def->setTable('invoice');

$def->addPrimaryKey('id', array('columnName'=>'invoice_id'));

$def->addProperty('invoice_date');
$def->addProperty('description');

$def->addManyToOne('customer', array('columnName' => 'customer_id', 'class' => 'Customer'));

// Invoices have a collection of InvoiceItems. The collection manages which 
// InvoiceItems are part of the Invoice.
$def->addCollection(
    'items',
    array( 
        'key' => 'invoice_id',
        'relation' => new Zend_Entity_Mapper_Definition_Relation_OneToMany(
            'invoice_id',
            array(
                'columnName'    => 'invoice_id',
                'class'         => 'InvoiceItem',
            )
        ),
    )
);

return $def;
