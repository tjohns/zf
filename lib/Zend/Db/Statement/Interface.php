<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Emulates a PDOStatement for native database adapters.
 *
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
interface Zend_Db_Statement_Interface {

    /**
     * binds a PHP variable to an output column in a result set
     */
    public function bindColumn($column, &$param, $type = null);

    /**
     * binds a PHP variable to a parameter in the prepared statement
     */
    public function bindParam($parameter, &$variable, $type = null,
        $length = null, $options = null);

    /**
     * binds a value to a parameter in the prepared statement
     */
    public function bindValue($parameter, $value, $type = null);

    /**
     * closes the cursor, allowing the statement to be executed again
     */
    public function closeCursor();

    /**
     * returns the number of columns in the result set
     */
    public function columnCount();

    /**
     * retrieves an error code, if any, from the statement
     */
    public function errorCode();

    /**
     * retrieves an array of error information, if any, from the statement
     */
    public function errorInfo();

    /**
     * executes a prepared statement
     */
    public function execute($params = null);

    /**
     * fetches a row from a result set
     */
    public function fetch($style = null, $cursor = null, $offset = null);

    /**
     * fetches an array containing all of the rows from a result set
     */
    public function fetchAll($style = null, $col = null);

    /**
     * returns the data from a single column in a result set
     */
    public function fetchColumn($col = 0);

    /**
     * fetches the next row and returns it as an object
     */
    public function fetchObject($class = 'stdClass', $config = null);

    /**
     * retrieves a Zend_Db_Statement attribute
     */
    public function getAttribute($key);

    /**
     * retrieves the next rowset (result set)
     */
    public function nextRowset();

    /**
     * returns the number of rows that were affected by the execution of an SQL statement
     */
    public function rowCount();

    /**
     * sets a Zend_Db_Statement attribute
     */
    public function setAttribute($key, $val);

    /**
     * sets the fetch mode for a Zend_Db_Statement
     */
    public function setFetchMode($mode);
}
