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
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Firebird.php 5906 2007-07-28 02:58:20Z bkarwin $
 */


/**
 * @see Zend_Db_Statement
 */
require_once 'Zend/Db/Statement.php';


/**
 * Extends for Firebird
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Statement_Firebird extends Zend_Db_Statement
{

    /**
     * The firebird_stmt_prepared resource.
     *
     * @var firebird_stmt_prepared
     */
    protected $_stmt_prepared;

    /**
     * The firebird_stmt_result resource.
     *
     * @var firebird_result
     */
    protected $_stmt_result;

    /**
     * Column names.
     *
     * @var array
     */
    protected $_keys;

    /**
     * Fetched result values.
     *
     * @var array
     */
    protected $_values;

    /**
     * @var array
     */
    protected $_meta = null;

    /**
     * @param  string $sql
     * @return void
     * @throws Zend_Db_Statement_Firebird_Exception
     */
    public function _prepare($sql)
    {
        $connection = $this->_adapter->getConnection();

		if ($trans = $this->_adapter->getTransaction())
			$this->_stmt_prepared = @ibase_prepare($connection, $trans, $sql);
		else
			$this->_stmt_prepared = @ibase_prepare($connection, $sql);

        if ($this->_stmt_prepared === false || ibase_errcode()) {
            /**
             * @see Zend_Db_Statement_Firebird_Exception
             */
            require_once 'Zend/Db/Statement/Firebird/Exception.php';
            throw new Zend_Db_Statement_Firebird_Exception("Firebird prepare error: " . ibase_errmsg());
        }
    }

    /**
     * Binds a parameter to the specified variable name.
     *
     * @param mixed $parameter Name the parameter, either integer or string.
     * @param mixed $variable  Reference to PHP variable containing the value.
     * @param mixed $type      OPTIONAL Datatype of SQL parameter.
     * @param mixed $length    OPTIONAL Length of SQL parameter.
     * @param mixed $options   OPTIONAL Other options.
     * @return bool
     * @throws Zend_Db_Statement_Db2_Exception
     */
    protected function _bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        return true;
    }

    /**
     * Closes the cursor and the statement.
     *
     * @return bool
     */
    public function close()
    {
        if ($stmt = $this->_stmt_result) {
            @ibase_free_result($this->_stmt_result);
            $this->_stmt_result = null;
        }

        if ($this->_stmt_prepared) {
            $r = @ibase_free_query($this->_stmt_prepared);
            $this->_stmt_prepared = null;
            return $r;
        }
        return false;
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     */
    public function closeCursor()
    {
        if ($stmt = $this->_stmt_result) {
            return @ibase_free_result($this->_stmt_result);
        }
        return false;
    }

    /**
     * Returns the number of columns in the result set.
     * Returns null if the statement has no result set metadata.
     *
     * @return int The number of columns.
     */
    public function columnCount()
    {
        if ($this->_stmt_result) {
            return ibase_num_fields($this->_stmt_result);
        }
        if ($this->_stmt_prepared) {
            return ibase_num_fields($this->_stmt_prepared);
        }		
        return 0;
    }

    /**
     * Retrieves the error code, if any, associated with the last operation on
     * the statement handle.
     *
     * @return string error code.
     */
    public function errorCode()
    {
        if (!$this->_stmt_prepared) {
            return false;
        }
        return ibase_errcode();
    }

    /**
     * Retrieves an array of error information, if any, associated with the
     * last operation on the statement handle.
     *
     * @return array
     */
    public function errorInfo()
    {
        if (!$this->_stmt_prepared) {
            return false;
        }
        return array(
            ibase_errcode(),
            ibase_errmsg()
        );
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL Values to bind to parameter placeholders.
     * @return bool
     * @throws Zend_Db_Statement_Firebird_Exception
     */
    public function _execute(array $params = null)
    {
        if (!$this->_stmt_prepared) {
            return false;
        }

        // if no params were given as an argument to execute(),
        // then default to the _bindParam array
        if ($params === null) {
            $params = $this->_bindParam;
        }
        // send $params as input parameters to the statement
        if ($params) {
            array_unshift($params, $this->_stmt_prepared);
            $retval = @call_user_func_array(
                'ibase_execute',
                $params
            );
        } else
			// execute the statement
			$retval = ibase_execute($this->_stmt_prepared);
        $this->_stmt_result = $retval;


        // statements that have no result set do not return metadata
        if (is_resource($this->_stmt_result)) {

            // get the column names that will result
            $this->_keys = array();
            $coln = ibase_num_fields($this->_stmt_result);
            for ($i = 0; $i < $coln; $i++) {
                $col_info = ibase_field_info($this->_stmt_result, $i);
                $this->_keys[] = $this->_adapter->foldCase($col_info['name']);
            }

            // set up a binding space for result variables
            $this->_values = array_fill(0, count($this->_keys), null);

            // set up references to the result binding space.
            // just passing $this->_values in the call_user_func_array()
            // below won't work, you need references.
            $refs = array();
            foreach ($this->_values as $i => &$f) {
                $refs[$i] = &$f;
            }
        }

        if ($retval === false) {
            /**
             * @see Zend_Db_Statement_Firebird_Exception
             */
            require_once 'Zend/Db/Statement/Firebird/Exception.php';
            throw new Zend_Db_Statement_Firebird_Exception("Firebird statement execute error : " . ibase_errmsg());
        }
		
		if ($trans = $this->_adapter->getTransaction())		
			return ibase_affected_rows($trans);
		else
			return ibase_affected_rows($this->_adapter->getConnection());		
    }

    /**
     * Fetches a row from the result set.
     *
     * @param int $style  OPTIONAL Fetch mode for this fetch operation.
     * @param int $cursor OPTIONAL Absolute, relative, or other.
     * @param int $offset OPTIONAL Number for absolute or relative cursors.
     * @return mixed Array, object, or scalar depending on fetch mode.
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {

        if (!$this->_stmt_result) {
            return false;
        }

        if ($style === null) {
            $style = $this->_fetchMode;
        }

        // @todo, respect the foldCase for column names
        switch ($style) {
            case Zend_Db::FETCH_NUM:
                $row = ibase_fetch_row($this->_stmt_result, IBASE_TEXT);
                break;
            case Zend_Db::FETCH_ASSOC:
                $row = ibase_fetch_assoc($this->_stmt_result, IBASE_TEXT);
                break;
            case Zend_Db::FETCH_BOTH:
                $row = ibase_fetch_assoc($this->_stmt_result, IBASE_TEXT);
                $values = array_values($row);
                foreach ($values as $val) {
                  $row[] = $val;
                }
                break;
            case Zend_Db::FETCH_OBJ:
                $row = ibase_fetch_object($this->_stmt_result, IBASE_TEXT);
                break;
            case Zend_Db::FETCH_BOUND:
                $row = ibase_fetch_assoc($this->_stmt_result, IBASE_TEXT);
                $values = array_values($row);
                foreach ($values as $val) {
                  $row[] = $val;
                }

                if ($row !== false) {
                    return $this->_fetchBound($row);
                }
                break;
            default:
                /**
                 * @see Zend_Db_Adapter_Firebird_Exception
                 */
                require_once 'Zend/Db/Statement/Firebird/Exception.php';
                throw new Zend_Db_Statement_Firebird_Exception(
                    "Invalid fetch mode '$style' specified"
                );
                break;
        }

        if (! $row && $error = ibase_errcode()) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Statement/Firebird/Exception.php';
            throw new Zend_Db_Statement_Firebird_Exception($error);
        }
/*
        switch ($this->_adapter->caseFolding) {
            case Zend_Db::CASE_LOWER:
                $r = array_change_key_case($row, CASE_LOWER);
                break;
            case Zend_Db::CASE_UPPER:
                $r = array_change_key_case($row, CASE_UPPER);
                break;
            case default:
                $r = $row;
                break;
        }*/
        return $row;
    }

    /**
     * Retrieves the next rowset (result set) for a SQL statement that has
     * multiple result sets.  An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     * @throws Zend_Db_Statement_Firebird_Exception
     */
    public function nextRowset()
    {
        /**
         * @see Zend_Db_Statement_Firebird_Exception
         */
        require_once 'Zend/Db/Statement/Firebird/Exception.php';
        throw new Zend_Db_Statement_Firebird_Exception(__FUNCTION__.'() is not implemented');
    }

    /**
     * Returns the number of rows affected by the execution of the
     * last INSERT, DELETE, or UPDATE statement executed by this
     * statement object.
     *
     * @return int     The number of rows affected.
     * @throws Zend_Db_Statement_Exception
     */
    public function rowCount()
    {
		if ($trans = $this->_adapter->getTransaction())		
			$num_rows = ibase_affected_rows($trans);
		else
			$num_rows = ibase_affected_rows($this->_adapter->getConnection());

        if ($num_rows === false) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Statement/Frebird/Exception.php';
            throw new Zend_Db_Statement_Firebird_Exception(ibase_errmsg());
        }

        return $num_rows;
    }	

}
