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
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';


/**
 * Class for connecting to SQL databases and performing common operations using PDO.
 *
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
abstract class Zend_Db_Adapter_Pdo_Abstract extends Zend_Db_Adapter_Abstract
{
    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        // baseline of DSN parts
        $dsn = $this->_config;

        // don't pass the username and password in the DSN
        unset($dsn['username']);
        unset($dsn['password']);

        // use all remaining parts in the DSN
        foreach ($dsn as $key => $val) {
            $dsn[$key] = "$key=$val";
        }

        return $this->_pdoType . ':' . implode(';', $dsn);
    }


    /**
     * Creates a PDO object and connects to the database.
     *
     * @return void
     */
    protected function _connect()
    {
        // if we already have a PDO object, no need to re-connect.
        if ($this->_connection) {
            return;
        }

        // check for PDO extension
        if (!extension_loaded('pdo')) {
            throw new Zend_DB_Adapter_Exception('The PDO extension is required for this adapter but not loaded');
        }

        // create PDO connection
        $q = $this->_profiler->queryStart('connect', Zend_Db_Profiler::CONNECT);
        $this->_connection = new PDO(
            $this->_dsn(),
            $this->_config['username'],
            $this->_config['password']
        );
        $this->_profiler->queryEnd($q);

        // force names to lower case
        $this->_connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);

        // always use exceptions.
        $this->_connection->setAttribute(PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION);

        /** @todo Are there other portability attribs to consider? */
    }


    /**
     * Prepares an SQL statement.
     *
     * @param string $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return PDOStatement
     */
    public function prepare($sql)
    {
        $this->_connect();
        return $this->_connection->prepare($sql);
    }

    /**
     * Inserts a table row with specified data.
     *
     * @param string $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, $bind)
    {
        // col names come from the array keys
        $cols = array_keys($bind);

        // build the statement
        $sql = "INSERT INTO $table "
             . '(' . implode(', ', $cols) . ') '
             . 'VALUES (:' . str_replace("_", "", implode(', :', $cols)) . ')';

        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $bind);
        return $result->rowCount();
    }


    /**
     * Gets the last inserted ID.
     *
     * @param string $name The sequence name (needed for some PDO drivers).
     * @return int
     */
    public function lastInsertId($name = null)
    {
        $this->_connect();
        return $this->_connection->lastInsertId($name);
    }


    /**
     * Begin a transaction.
     */
    protected function _beginTransaction()
    {
        $this->_connection->beginTransaction();
    }


    /**
     * Commit a transaction.
     */
    protected function _commit()
    {
        $this->_connection->commit();
    }


    /**
     * Roll-back a transaction.
     */
    protected function _rollBack() {
        $this->_connection->rollBack();
    }


    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        return $this->_connection->quote($value);
    }


    /**
     * Set the PDO fetch mode.
     *
     * @param int $mode A PDO fetch mode.
     * @return void
     * @todo Support FETCH_CLASS and FETCH_INTO.
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case PDO::FETCH_LAZY:
            case PDO::FETCH_ASSOC:
            case PDO::FETCH_NUM:
            case PDO::FETCH_BOTH:
            case PDO::FETCH_NAMED:
            case PDO::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Exception('Invalid fetch mode specified');
                break;
        }
    }

}

