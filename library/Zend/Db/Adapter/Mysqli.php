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
 *
 *
 * @todo XXX - Needs to be updated to use Zend_Db_Adapter_Abstract
 *             Also, most fetch..() methods are broken (no fetch_array method of stmt)
 *
 */


/**
 * Zend
 */
require_once 'Zend.php';

/**
 * Zend_Db_Adapter_Interface
 */
require_once 'Zend/Db/Adapter/Interface.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';

/**
 * Zend_Db_Profiler
 */
require_once 'Zend/Db/Profiler.php';

/**
 * Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * Zend_Db_Statement_Mysqli
 */
require_once 'Zend/Db/Statement/Mysqli.php';

/**
 * @package    Zend_Db
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_Db_Adapter_Mysqli implements Zend_Db_Adapter_Interface {

    /**
     * User-provided configuration.
     *
     * Basic keys are:
     *
     * username => (string) Connect to the database as this username.
     * password => (string) Password associated with the username.
     * host     => (string) What host to connect to (default 127.0.0.1)
     * dbname   => (string) The name of the database to user
     *
     * Additional keys are processed as key-value pairs for a PDO DSN string.
     *
     * @var array
     */
    protected $_config = array(
        'host'     => '127.0.0.1',
        'port'     => null,
        'socket'   => null,
        'database' => null,
        'username' => null,
        'password' => null,
    );

    /**
     * Query profiler.
     */
    protected $_profiler;

    /**
     * Fetch mode.
     *
     * @var int
     */
    protected $_fetchMode = Zend_Db::FETCH_ASSOC;


    /**
     * Creates a connection resource.
     *
     * @return void
     */
    protected function _connect()
    {
        // if we already have a PDO object, no need to re-connect.
        if ($this->_connection) {
            return;
        }

        // create mysqli connection
        $q = $this->_profiler->queryStart('connect', Zend_Db_Profiler::CONNECT);
        $this->_connection = new mysqli(
            $this->_config['host'],
            $this->_config['username'],
            $this->_config['password'],
            null, // select the database later
            $this->_config['port'],
            $this->_config['socket']
        );
        $this->_profiler->queryEnd($q);

        // check the connection
        if (mysqli_connect_errno()) {
            throw new Zend_Db_Adapter_Exception(
                'Connect failed: ' . mysqli_connect_error()
            );
        }

        // check the database selection
        $result = $this->_connection->select_db(
            $this->_config['database']
        );
        if ($result === false) {
            throw new Zend_Db_Adapter_Exception(
                'Database selection failed: ' . $this->_connection->error
            );
        }
    }


    /**
     * Executes an SQL statement with bound data.
     *
     * @param string $sql The SQL statement with placeholders.
     * @param array $bind An array of data to bind to the placeholders.
     * @return PDOStatement
     */
    public function query($sql, $bind = array())
    {
        // connect to the database if needed
        $this->_connect();

        // is the $sql a Zend_Db_Select object?
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->__toString();
        }

        // prepare the statement and bind param values
        $stmt = $this->prepare($sql);
        foreach ((array) $bind as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        // execute with profiling
        $q = $this->_profiler->queryStart($sql);
        $stmt->execute();
        $this->_profiler->queryEnd($q);
        return $stmt;
    }


    /**
     * Returns an SQL statement for preparation.
     *
     * @param string $sql The SQL statement with placeholders.
     * @return Zend_Db_Statement_Mysqli
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmt = new Zend_Db_Statement_Mysqli($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }


    /**
     * Gets the last inserted ID.
     *
     * @return int
     */
    public function lastInsertId($name = null)
    {
        $this->_connect();
        return $this->_connection->insert_id;
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        return $this->fetchCol('SHOW TABLES');
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "DESCRIBE $table";
        $result = $this->fetchAll($sql);
        foreach ($result as $key => $val) {
            $descr[$val['Field']] = array(
                'name'    => $val['Field'],
                'type'    => $val['Type'],
                'notnull' => (bool) ($val['Null'] === ''), // not null is empty, null is yes
                'default' => $val['Default'],
                'primary' => (strtolower($val['Key']) == 'pri'),
            );
        }
        return $descr;
    }


    /**
     * Leave autocommit mode and begin a transaction.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->_connect();
        $q = $this->_profiler->queryStart('begin', Zend_Db_Profiler::TRANSACTION);
        $this->_connection->autocommit(false);
        $this->_profiler->queryEnd($q);
        return true;
    }


    /**
     * Commit a transaction and return to autocommit mode.
     *
     */
    public function commit()
    {
        $this->_connect();
        $q = $this->_profiler->queryStart('commit', Zend_Db_Profiler::TRANSACTION);
        $this->_connection->autocommit(true);
        $this->_connection->commit();
        $this->_profiler->queryEnd($q);
        return true;
    }


    /**
     * Roll back a transaction and return to autocommit mode.
     *
     * @return void
     */
    public function rollBack()
    {
        $this->_connect();
        $q = $this->_profiler->queryStart('rollback', Zend_Db_Profiler::TRANSACTION);
        $this->_connection->rollback();
        $this->_profiler->queryEnd($q);
        return true;
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
             . 'VALUES (:' . implode(', :', $cols) . ')';

        // execute the statement and return the number of affected rows
        $this->query($sql, $bind);
        return $this->_connection->affected_rows;
    }


    /**
     * Updates table rows with specified data based on a WHERE clause.
     *
     * @param string $table The table to udpate.
     * @param array $bind Column-value pairs.
     * @param string $where UPDATE WHERE clause.
     * @return int The number of affected rows.
     */
    public function update($table, $bind, $where)
    {
        // build "col = :col" pairs for the statement
        $set = array();
        foreach ($bind as $col => $val) {
            $set[] = "$col = :$col";
        }

        // build the statement
        $sql = "UPDATE $table "
             . 'SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');

        // execute the statement and return the number of affected rows
        $this->query($sql, $bind);
        return $this->_connection->affected_rows;
    }


    /**
     * Deletes table rows based on a WHERE clause.
     *
     * @param string $table The table to udpate.
     * @param string $where DELETE WHERE clause.
     * @return int The number of affected rows.
     */
    public function delete($table, $where)
    {
        // build the statement
        $sql = "DELETE FROM $table"
             . (($where) ? " WHERE $where" : '');

        // execute the statement and return the number of affected rows
        $this->query($sql);
        return $this->_connection->affected_rows;
    }


    /**
     * Creates and returns a new Zend_Db_Select object for this adapter.
     *
     * @return Zend_Db_Select
     */
    public function select()
    {
        $class = "Zend_Db_Select_Mysqli";
        require_once "Zend/Db/Select/Mysqli.php";
        return new $class($this);
    }


    /**
     * Set the fetch mode.
     *
     * @param int $mode A fetch mode.
     * @return void
     * @todo Support FETCH_CLASS and FETCH_INTO.
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Zend_Db::FETCH_NUM:   // seq array
            case Zend_Db::FETCH_ASSOC: // assoc array
            case Zend_Db::FETCH_BOTH:  // seq+assoc array
            case Zend_Db::FETCH_OBJ:   // object
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Zend_Db_Adapter_Exception('Invalid fetch mode specified');
                break;
        }
    }


    /**
     * Get the fetch mode.
     *
     * @return int
     */
    public function getFetchMode()
    {
        return $this->_fetchMode;
    }


    /**
     * Fetches all SQL result rows as a sequential array.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchAll($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $this->_fetch($result)) {
            $data[] = $row;
        }
        $result->close();
        return $data;
    }


    /**
     * Fetches all SQL result rows as an associative array.
     *
     * The first column is the key, the entire row array is the
     * value.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchAssoc($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $this->_fetch($result)) {
            $tmp = array_values(array_slice($row, 0, 1));
            $data[$tmp[0]] = $row;
        }
        $result->close();
        return $data;
    }


    /**
     * Fetches the first column of all SQL result rows as an array.
     *
     * The first column in each row is used as the array key.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchCol($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $data[] = $row[0];
        }
        $result->close();
        return $data;
    }


    /**
     * Fetches all SQL result rows as an array of key-value pairs.
     *
     * The first column is the key, the second column is the
     * value.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchPairs($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $data = array();
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $data[$row[0]] = $row[1];
        }
        $result->close();
        return $data;
    }


    /**
     * Fetches the first column of the first row of the SQL result.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return string
     */
    public function fetchOne($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $row = $result->fetch_array(MYSQLI_NUM);
        $result->close();
        return $row[0];
    }


    /**
     * Fetches the first row of the SQL result.
     *
     * @param string $sql An SQL SELECT statement.
     * @param array $bind Data to bind into SELECT placeholders.
     * @return array
     */
    public function fetchRow($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        $row = $this->_fetch($result);
        $result->close();
        return $row;
    }


    /**
     * Safely quotes a value for an SQL statement.
     *
     * If an array is passed as the value, the array values are quoted
     * and then returned as a comma-separated string.
     *
     * @param mixed $value The value to quote.
     * @return mixed An SQL-safe quoted value (or string of separated values).
     */
    public function quote($value)
    {
        $this->_connect();
        if (is_array($value)) {
            foreach ($value as &$val) {
                $val = $this->quote($val);
            }
            return implode(', ', $value);
        } else {
            return '"' . $this->_connection->real_escape_string($value) . '"';
        }
    }


    /**
     * Quotes a value and places into a piece of text at a placeholder.
     *
     * The placeholder is a question-mark; all placeholders will be replaced
     * with the quoted value.   For example:
     *
     * <code>
     * $text = "WHERE date < ?";
     * $date = "2005-01-02";
     * $safe = $sql->quoteInto($text, $date);
     * // $safe = "WHERE date < '2005-01-02'"
     * </code>
     *
     * @param string $txt The text with a placeholder.
     * @param mixed $val The value to quote.
     * @return mixed An SQL-safe quoted value placed into the orignal text.
     */
    public function quoteInto($text, $value)
    {
        return str_replace('?', $this->quote($value), $text);
    }


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        $ident = str_replace('`', '\`', $ident);
        return "`$ident`";
    }


    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
     public function limit($sql, $count, $offset)
     {
        if ($count > 0) {
            $offset = ($offset > 0) ? $offset : 0;
            $sql .= "LIMIT $offset, $count";
        }
        return $sql;
    }
}
