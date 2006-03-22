<?php
require_once 'ZDBAdapter/ZDBAdapterMySQL.php';
require_once 'ZActiveRecord/ZActiveRecord.php';
/**
 * ZActiveRecord - Has-Many Relationship Example
 * 
 * ZActiveRecord supports nesting tables through relationships.
 * Relationships supported are has-one, belong-to-one, has-many,
 * and belongs-to-many.
 */

$db = new ZDBAdapterMySQL(array('host'     => 'localhost',
                                'username' => 'root',
                                'password' => 'mysql!root',
                                'database' => 'test'));

ZActiveRecord::setDatabaseAdapter($db);

/**
 * Two tables exist in the database, "persons" and "cars".
 * A row in the "persons" table can have many associated
 * rows in the "cars" table.
 */
class Car extends ZActiveRecord {}
class Person extends ZActiveRecord {
	protected $_hasMany = 'car';
}


/**
 * Find the first person in the "persons" table.
 */
$person = new Person();
$person = $person->findFirst(array('nameFirst'=>'Mike'));

/**
 * Fetch all of the rows in the "cars" table that belong
 * to this person.
 */
foreach ($person->cars as $car) {
	var_dump($car->make);
	var_dump($car->model);
}