<?php
require_once 'ZDBAdapter/ZDBAdapterMySQL.php';
require_once 'ZActiveRecord/ZActiveRecord.php';

/**
 * ZActiveRecord - Basic Usage Example
 * 
 * ZActiveRecord is an implementation of Object Relation Mapping
 * for PHP, named after Martin Fowler's "ActiveRecord" pattern.  It
 * is inspired by the ORM classes in Ruby-on-Rails and Django but
 * has a uniquely PHP5 flavor.
 */

// Connect to the database.
$db = new ZDBAdapterMySQL(array('host'     => 'localhost',
                                'username' => 'root',
                                'password' => 'mysql!root',
                                'database' => 'test'));

ZActiveRecord::setDatabaseAdapter($db);

/**
 * ZActiveRecords are object representations of table rows.
 * The class name (Person) automatically maps to the database
 * table of the same name, pluralized: "persons".  This behavior
 * is optional and can be overrided.
 */
class Person extends ZActiveRecord {}

/**
 * Create a new person.  The "persons" table will be introspected
 * and its fields will be mapped to object properties automatically.
 */
$person = new Person();

/**
 * Set "name_first" and "name_last" fields.
 */
$person->nameFirst = 'Andi';
$person->nameLast = 'Gutmans';

/**
 * Insert a new row into the database.
 */
$person->save();