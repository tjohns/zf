<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * 
 */ 


/**
 * Zend
 */
require_once 'Zend.php';

/** 
 * Zend_Db_Adapter_Abstract 
 */
require_once 'Zend/Db/Adapter/Abstract.php';

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


class Zend_Db_Adapter_MySQLi extends Zend_Db_Adapter_Abstract {
	
	
	
	/**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($string) {
    	
    	$ident = str_replace('`', '``', $ident);
        return "`$ident`"; 
    	
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables() {
    	
    	return $this->fetchCol('SHOW TABLES'); 
    	
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table) {
    	
    	$sql = "DESCRIBE $table";
    	
    	$this->_connect();
    	
    	$_result = $this->_connection->query($sql);
    	
    	$result = array();
    	
    	while ($_row = $_result->fetch_assoc()) {
    		
    		$row = array();
    		
    		foreach ($_row as $key => $value) {
    			
    			$row[strtolower($key)] = $value;
    			
    		}
    		
    		$result[] = $row;
    		
    	}
    	
    	$descr = array();
        foreach ($result as $key => $val) {
            $descr[$val['field']] = array(
                'name'    => $val['field'],
                'type'    => $val['type'],
                'notnull' => (bool) ($val['null'] != 'YES'), // not null is NO or empty, null is YES
                'default' => $val['default'],
                'primary' => (strtolower($val['key']) == 'pri'),
            );
        }
        
        return $descr; 
    	
    }
    
    public function query($sql, $bind = array()) {
    	
    	//print $sql;
    	
    	$this->_connect();
    	
    	// is the $sql a Zend_Db_Select object?
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->__toString();
        }
    	
    	$_result = $this->_connection->query($sql);
    	
    	print $this->_connection->error;
    	
    	return $_result;
    	
    }
    
    public function fetchRow($sql, $bind = null)
    {
        $result = $this->query($sql, $bind);
        
        return $result->fetch_object();
    }
    
    public function fetchAll($sql) {
    	
    	$_result = $this->query($sql);
    	
    	while ($_row = $_result->fetch_assoc()) {
    		
    		$row = array();
    		
    		foreach ($_row as $key => $value) {
    			
    			$row[strtolower($key)] = $value;
    			
    		}
    		
    		$result[] = $row;
    		
    	}
    	
    	return $result;
    	
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
        $vals = array_values($bind);

        // build the statement
        $sql = "INSERT INTO $table "
             . '(' . implode(', ', $cols) . ') '
             . 'VALUES (' . $this->quote($vals) . ')';

        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $bind);
        return $result;
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
        	if ($col != "id")
            $set[] = "$col = ".$this->quote($val);
        }

        // build the statement
        $sql = "UPDATE $table "
             . 'SET ' . implode(', ', $set)
             . (($where) ? " WHERE $where" : '');

        // execute the statement and return the number of affected rows
        $result = $this->query($sql, $bind);
        return $result;
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
        $result = $this->query($sql);
        return $result;
    }
    
    
    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value) {
    	
    	return "'".str_replace("'", "''", $value)."'"; 
    	
    }


    /**
     * Creates a connection to the database.
     *
     * @return void
     */
    protected function _connect() {
    	
    	if ($this->_connection) {
    		return ;
    	}
    	
    	$this->_connection =& new mysqli($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);
    	
    }


    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param  string  $sql  SQL query
     * @return Zend_Db_Statment|PDOStatement
     */
    public function prepare($sql) {
    	
    	$this->_connect();
        return $this->_connection->prepare($sql);
    	
    }


    /**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   name of table (or sequence) associated with sequence
     * @param  string $primaryKey  primary key in $tableName
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null) {
    	
    	$this->_connect();
        return $this->_connection->insert_id; 
    	
    }


    /**
     * Begin a transaction.
     */
    protected function _beginTransaction() {
    	
    	$this->_connection->beginTransaction(); 
    	
    }


    /**
     * Commit a transaction.
     */
    protected function _commit() {
    	
    	$this->_connection->commit(); 
    	
    }


    /**
     * Roll-back a transaction.
     */
    protected function _rollBack() {
    	
    	$this->_connection->rollBack(); 
    	
    }


    /**
     * Set the fetch mode.
     *
     * @param integer $mode
     */
    public function setFetchMode($mode) {
    	
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



    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
    public function limit($sql, $count, $offset) {
    	
    	if ($count > 0) {
            $offset = ($offset > 0) ? $offset : 0;
            $sql .= "LIMIT $offset, $count";
        }
        return $sql; 
    	
    }
    
}

?>