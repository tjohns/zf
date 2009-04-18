<?php
/*
 * Mapping definition for entity Customer
 */

$def = new Zend_Entity_Mapper_Definition_Entity;

$def->setClass('Customer');
$def->setTable('customers');

$def->addPrimaryKey('id', array('columnName'=>'customer_id'));

$def->addProperty('name');

// Each Customer should have a reference to their Account object
$def->addManyToOne(
    'account',
    array( 
        'columnName' => 'account_id', // Primary key in the accounts table?
        'class' => 'Account',  // Class this property should be an object of?
        'foreignKey' => 'account_id', // FK field in customers table?
        'load' => 'lazy',
    )
);

// Customers have two Addresses: billing and physical. It's a many-to-many 
// relationship in the DB, but we want specific properties of class Address to 
// represent the customer's addresses.
$def->addCollection('billingAddr', array(
    'key' => 'customer_id',
    'relation' => new Zend_Entity_Mapper_Definition_Relation_ManyToMany('address_id', array(
        'table' => 'customer_addresses', // Name of the join table
        'class' => 'Address',
    )),
    'where' => "(address_type = 'billing')",
));

$def->addCollection('physicalAddr', array(
    'key' => 'customer_id',
    'relation' => new Zend_Entity_Mapper_Definition_Relation_ManyToMany('address_id', array(
        'table' => 'customer_addresses', // Name of the join table
        'class' => 'Address',
    )),
    'where' => "(address_type = 'physical')",
));

return $def;
