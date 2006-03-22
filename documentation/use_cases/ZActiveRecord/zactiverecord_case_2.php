<?php
require_once 'ZDBAdapter/ZDBAdapterMySQL.php';
require_once 'ZActiveRecord/ZActiveRecord.php';

/**
 * ZActiveRecord - Find() Example
 * 
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
 * Create a new person object.  The "persons" table will be introspected
 * and its fields will be mapped to object properties automatically.
 */
$person = new Person();

/**
 * Find all people with the first name "Andi" and last name "Gutmans",
 * with no SQL required.  However, if you need to query with SQL, 
 * just use it: findBySql().
 */
$people = $person->findAll(array('nameFirst'=>'Andi',
							  'nameLast' =>'Gutmans'));

/**
 * Iterate over all of the records that were found.
 */
foreach($people as $key=>$p) {
	// $key contains the primary key of the row.
	var_dump($p->nameFirst);
	var_dump($p->nameLast);
}