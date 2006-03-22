<?php
require_once 'ZDBAdapter/ZDBAdapterMySQL.php';
require_once 'ZActiveRecord/ZActiveRecord.php';
/**
 * ZActiveRecord - SQL Profiler Example
 * 
 * The ZDBAdapter database adapter includes a built-in
 * profiler for logging SQL queries.  It can log all queries,
 * queries of a certain type (INSERT, SELECT, etc), or queries
 * that take longer than a specified elapsed time.
 * 
 * The profiler is disabled by default but is easily turned
 * on and can be used to monitor the operations of
 * ZActiveRecord both for debugging and auditing.
 */

// Connect to the database.
$db = new ZDBAdapterMySQL(array('host'     => 'localhost',
                                'username' => 'root',
                                'password' => 'mysql!root',
                                'database' => 'test'));

ZActiveRecord::setDatabaseAdapter($db);

/**
 * Get the profiler object and enable it.
 */
$db->getProfiler()->setEnabled(true);

/**
 * Perform some operations with ZActiveRecord.
 */
class Person extends ZActiveRecord {}
$person = new Person();
$people = $person->findFirst(array('nameFirst'=>'Mike',
							  'nameLast' =>'Naberezny'));

/**
 * Get the profiles for all of the SELECT statments that
 * were recorded by the profiler.  Each profile contains
 * the original SQL statement, that starting time, and
 * the execution time.
 */
var_dump($db->getProfiler()->getQueryProfiles());
