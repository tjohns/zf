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
 * @version    $Id: Mysqli.php 4874 2007-05-19 01:26:32Z bkarwin $
 */

/**
 * @see Zend_Db_Statement_Interface
 */
require_once 'Zend/Db/Statement/Interface.php';

/**
 * Proxy class to wrap a PDOStatement object.
 * Matches the interface of PDOStatement.  All methods simply proxy to the 
 * matching method in PDOStatement.  PDOExceptions thrown by PDOStatement
 * are re-thrown as Zend_Db_Statement_Exception.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Statement
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Statement_Pdo implements Zend_Db_Statement_Interface
{

    /**
     * The mysqli_stmt object.
     *
     * @var PDOStatement
     */
    protected $_stmt;

    /**
     * @var int
     */
    protected $_fetchMode = PDO::FETCH_ASSOC;

    /**
     * Constructor.
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     * @param string or Zend_Db_Select $sql
     * @return void
     */
    public function __construct($adapter, $sql)
    {
        $this->_adapter = $adapter;
        $this->_prepSql($sql);
    }

    /**
     * @param mixed string or Zend_Db_Select
     * @return void
     * @throws Zend_Db_Statement_Exception
     */
    protected function _prepSql($sql)
    {
        if ($sql instanceof Zend_Db_Select) {
            $sql = $sql->__toString();
        }
        try {
            $this->_stmt = $this->_adapter->getConnection()->prepare($sql);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     *
     * @param string $column
     * @param mixed  $param
     * @param mixed  $type   OPTIONAL
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function bindColumn($column, &$param, $type = null)
    {
        try {
            return $this->_stmt->bindColumn($column, $param, $type);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     *
     * @param mixed $parameter
     * @param mixed $variable
     * @param mixed $type      OPTIONAL
     * @param mixed $length    OPTIONAL
     * @param mixed $options   OPTIONAL
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function bindParam($parameter, &$variable, $type = null, $length = null, $options = null)
    {
        if (is_string($parameter) && $parameter[0] != ':') {
            $parameter = ":$parameter";
        }
        try {
            return $this->_stmt->bindParam($parameter, $variable, $type, $length, $options);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     *
     * @param mixed $parameter
     * @param mixed $value
     * @param mixed $type      OPTIONAL
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function bindValue($parameter, $value, $type = null)
    {
        try {
            return $this->_stmt->bindValue($parameter, $value, $type);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Closes the cursor, allowing the statement to be executed again.
     *
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function closeCursor()
    {
        try {
            return $this->_stmt->closeCursor();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Returns the number of columns in the result set.
     * Returns null if the statement has no result set metadata.
     *
     * @return int Field count.
     * @throws Zend_Db_Statement_Exception
     */
    public function columnCount()
    {
        try {
            return $this->_stmt->columnCount();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Retrieves an error code, if any, from the statement.
     *
     * @return string error code.
     * @throws Zend_Db_Statement_Exception
     */
    public function errorCode()
    {
        try {
            return $this->_stmt->errorCode();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Retrieves an array of error information, if any, from the statement.
     *
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    public function errorInfo()
    {
        try {
            return $this->_stmt->errorInfo();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Executes a prepared statement.
     *
     * @param array $params OPTIONAL values to supply as input to statement parameters
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function execute(array $params = array())
    {
        try {
            return $this->_stmt->execute($params);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Fetches a row from the result set.
     *
     * @param mixed   $style  OPTIONAL
     * @param mixed   $cursor OPTIONAL
     * @param int     $offset OPTIONAL
     * @return mixed
     * @throws Zend_Db_Statement_Exception
     */
    public function fetch($style = null, $cursor = null, $offset = null)
    {
        if ($style === null) {
            $style = $this->_fetchMode;
        }
        try {
            return $this->_stmt->fetch($style, $cursor, $offset);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     *
     * @param mixed   $style OPTIONAL
     * @param int     $col   OPTIONAL
     * @return array
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchAll($style = null, $col = null)
    {
        if ($style === null) {
            $style = $this->_fetchMode;
        }
        try {
            if ($style == PDO::FETCH_COLUMN) {
                if ($col === null) {
                    $col = 0;
                }
                return $this->_stmt->fetchAll($style, $col);
            } else {
                return $this->_stmt->fetchAll($style);
            }
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * @param int     $col OPTIONAL
     * @return string
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchColumn($col = 0)
    {
        try {
            return $this->_stmt->fetchColumn($col);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * @param string $class  OPTIONAL
     * @param array  $config OPTIONAL
     * @return mixed
     * @throws Zend_Db_Statement_Exception
     */
    public function fetchObject($class = 'stdClass', array $config = array())
    {
        try {
            return $this->_stmt->fetchObject($class, $config);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * @param string $key
     * @return mixed
     * @throws Zend_Db_Statement_Exception
     */
    public function getAttribute($key)
    {
        try {
            return $this->_stmt->getAttribute();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * @param int $column
     * @return mixed
     * @throws Zend_Db_Statement_Exception
     */
    public function getColumnMeta($column)
    {
        try {
            return $this->_stmt->getColumnMeta();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Retrieves the next rowset (result set)
     * for a SQL statement that has multiple result sets.
     * An example is a stored procedure that returns
     * the results of multiple queries.
     *
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function nextRowset()
    {
        try {
            return $this->_stmt->nextRowset();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * Returns the number of rows that were affected by the execution of the
     * last INSERT, DELETE, or UPDATE SQL statement.
     *
     * @return int     Number of rows affected.
     * @throws Zend_Db_Statement_Exception
     */
    public function rowCount()
    {
        try {
            return $this->_stmt->rowCount();
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * @param string $key
     * @param mixed  $val
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function setAttribute($key, $val)
    {
        try {
            return $this->_stmt->setAttribute($key, $val);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

    /**
     * @param int     $mode
     * @return bool
     * @throws Zend_Db_Statement_Exception
     */
    public function setFetchMode($mode)
    {
        $this->_fetchMode = $mode;
        try {
            return $this->_stmt->setFetchMode($mode);
        } catch (PDOException $e) {
            require_once 'Zend/Db/Statement/Exception.php';
            throw new Zend_Db_Statement_Exception($e->getMessage());
        }
    }

}
