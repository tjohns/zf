<?php

namespace Zend\Doctrine\Driver;

use Doctrine\ORM;

set_include_path(
    '/home/benny/code/php/wsnetbeans/Doctrine/trunk/lib/:'.
    '/home/benny/code/php/wsnetbeans/Doctrine/trunk/tests/:'.
    get_include_path()
);

require_once '/home/benny/code/php/wsnetbeans/Doctrine/trunk/lib/Doctrine/Common/IsolatedClassLoader.php';
$classLoader = new \Doctrine\Common\IsolatedClassLoader('Doctrine');
$classLoader->register();

require_once "Zend/Loader/Autoloader.php";
$autoloader = \Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace("Zend");

class ZendDriver implements \Doctrine\DBAL\Driver
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db = null;

    /**
     * @var string
     */
    private $_databaseName = null;

    public function __construct($databaseName)
    {
        $this->_databaseName = $databaseName;
    }

    /**
     * Attempts to create a connection with the database.
     *
     * @param array $params All connection parameters passed by the user.
     * @param string $username The username to use when connecting.
     * @param string $password The password to use when connecting.
     * @param array $driverOptions The driver options to use when connecting.
     * @return Doctrine\DBAL\Driver\Connection The database connection.
     */
    public function connect(array $params, $username = null, $password = null, array $driverOptions = array())
    {
        $options = array_merge($params, array(
            'user' => $username,
            'username' => $username,
            'password' => $password,
            'options' => $driverOptions)
        );
        
        if(isset($params['driver'])) {
            $driverName = $params['driver'];
        } else {
            throw new \Exception("No driver was given!");
        }
        var_dump($options);
        $this->_db = \Zend_Db::factory($driverName, $options);
        return new Connection($this->_db);
    }

    private function _getDatabaseName()
    {
        if($this->_databaseName !== null) {
            return $this->_databaseName;
        }

        $adapters = array(
            'Zend_Db_Adapter_Mysqli'        => 'mysql',
            'Zend_Db_Adapter_Pdo_Mysql'     => 'mysql',
            'Zend_Db_Adapter_Pdo_Mssql'     => 'mssql',
            'Zend_Db_Adapter_Sqlsrv'        => 'mssql',
            'Zend_Db_Adapter_Pdo_Sqlite'    => 'sqlite',
            'Zend_Db_Adapter_Pdo_Oci'       => 'oci',
            'Zend_Db_Adapter_Pdo_Pgsql'     => 'pgsql',
            'Zend_Db_Adapter_Oracle'        => 'oracle',
            'Zend_Db_Adapter_Db2'           => 'db2',
            'Zend_Db_Adapter_Pdo_Ibm'       => 'db2',
        );

        foreach($adapters AS $adapterClassName => $databaseName) {
            if($this->_db instanceof $adapterClassName) {
                $this->_databaseName = $databaseName;
                return $databaseName;
            }
        }

        throw new \Exception("No valid Database Name was found for ".get_class($this->_db));
    }

    /**
     * Gets the DatabasePlatform instance that provides all the metadata about
     * the platform this driver connects to.
     *
     * @return Doctrine\DBAL\Platforms\AbstractPlatform The database platform.
     */
    public function getDatabasePlatform()
    {
        switch($this->_getDatabaseName()) {
            case 'mysql':
                return new \Doctrine\DBAL\Platforms\MySqlPlatform();
            case 'mssql':
                return new \Doctrine\DBAL\Platforms\MsSqlPlatform();
            case 'pgsql':
                return new \Doctrine\DBAL\Platforms\PostgreSqlPlatform();
            case 'oracle':
                return new \Doctrine\DBAL\Platforms\OraclePlatform();
            case 'sqlite':
                return new \Doctrine\DBAL\Platforms\SqlitePlatform();
            default:
                throw new \Exception();
        }
    }

    /**
     * Gets the SchemaManager that can be used to inspect and change the underlying
     * database schema of the platform this driver connects to.
     *
     * @param  Doctrine\DBAL\Connection $conn
     * @return Doctrine\DBAL\SchemaManager
     */
    public function getSchemaManager(\Doctrine\DBAL\Connection $conn)
    {
        switch($this->_getDatabaseName()) {
            case 'mysql':
                return new \Doctrine\DBAL\Schema\MySqlSchemaManager($conn);
            case 'mssql':
                return new \Doctrine\DBAL\Schema\MsSqlSchemaManager($conn);
            case 'pgsql':
                return new \Doctrine\DBAL\Schema\PostgreSqlSchemaManager($conn);
            case 'oracle':
                return new \Doctrine\DBAL\Schema\OracleSchemaManager($conn);
            case 'sqlite':
                return new \Doctrine\DBAL\Schema\SqliteSchemaManager($conn);
            default:
                throw new \Exception();
        }
    }

    /**
     * Gets the name of the driver.
     *
     * @return string The name of the driver.
     */
    public function getName()
    {
        return "zend_db";
    }

    /**
     * Get the name of the database connected to for this driver instance
     *
     * @param  Doctrine\DBAL\Connection $conn
     * @return string $database
     */
    public function getDatabase(\Doctrine\DBAL\Connection $conn)
    {
        $params = $conn->getParams();
        return $params['dbname'];
    }
}

class Statement implements \Doctrine\DBAL\Driver\Statement
{
    /**
     * @var Zend_Db_Statement_Interface
     */
    private $_stmt = null;

    private $_sql;

    /**
     * @param \Zend_Db_Statement_Interface
     */
    public function __construct(\Zend_Db_Statement_Interface $stmt, $sql)
    {
        $this->_stmt = $stmt;
        $this->_sql = $sql;
    }

    /**
     * Bind a column to a PHP variable
     *
     * @param mixed $column         Number of the column (1-indexed) or name of the column in the result set.
     *                              If using the column name, be aware that the name should match
     *                              the case of the column, as returned by the driver.
     * @param string $param         Name of the PHP variable to which the column will be bound.
     * @param integer $type         Data type of the parameter, specified by the PDO::PARAM_* constants.
     * @return boolean              Returns TRUE on success or FALSE on failure
     */
    function bindColumn($column, &$param, $type = null)
    {
        $args = \func_get_args();
        var_dump($args);
        var_dump("bindColumn");
        return $this->_stmt->bindColumn($column, $param, $type);
    }

    /**
     * Binds a value to a corresponding named or positional
     * placeholder in the SQL statement that was used to prepare the statement.
     *
     * @param mixed $param          Parameter identifier. For a prepared statement using named placeholders,
     *                              this will be a parameter name of the form :name. For a prepared statement
     *                              using question mark placeholders, this will be the 1-indexed position of the parameter
     *
     * @param mixed $value          The value to bind to the parameter.
     * @param integer $type         Explicit data type for the parameter using the PDO::PARAM_* constants.
     *
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    function bindValue($param, $value, $type = null)
    {
        $args = \func_get_args();
        var_dump($args);
        var_dump("bindValue");
        echo $this->_sql;
        return $this->_stmt->bindValue($param, $value, $type);
    }

    /**
     * Binds a PHP variable to a corresponding named or question mark placeholder in the
     * SQL statement that was use to prepare the statement. Unlike PDOStatement->bindValue(),
     * the variable is bound as a reference and will only be evaluated at the time
     * that PDOStatement->execute() is called.
     *
     * Most parameters are input parameters, that is, parameters that are
     * used in a read-only fashion to build up the query. Some drivers support the invocation
     * of stored procedures that return data as output parameters, and some also as input/output
     * parameters that both send in data and are updated to receive it.
     *
     * @param mixed $param          Parameter identifier. For a prepared statement using named placeholders,
     *                              this will be a parameter name of the form :name. For a prepared statement
     *                              using question mark placeholders, this will be the 1-indexed position of the parameter
     *
     * @param mixed $variable       Name of the PHP variable to bind to the SQL statement parameter.
     *
     * @param integer $type         Explicit data type for the parameter using the PDO::PARAM_* constants. To return
     *                              an INOUT parameter from a stored procedure, use the bitwise OR operator to set the
     *                              PDO::PARAM_INPUT_OUTPUT bits for the data_type parameter.
     *
     * @param integer $length       Length of the data type. To indicate that a parameter is an OUT parameter
     *                              from a stored procedure, you must explicitly set the length.
     * @param mixed $driverOptions
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    function bindParam($column, &$variable, $type = null, $length = null, $driverOptions = array())
    {
        $args = \func_get_args();
        var_dump($args);
        var_dump("bindParam");
        return $this->_stmt->bindParam($column, $variable, $type, $length, $driverOptions);
    }

    /**
     * closeCursor
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return boolean              Returns TRUE on success or FALSE on failure.
     */
    function closeCursor()
    {
        return $this->_stmt->closeCursor();
    }

    /**
     * columnCount
     * Returns the number of columns in the result set
     *
     * @return integer              Returns the number of columns in the result set represented
     *                              by the PDOStatement object. If there is no result set,
     *                              this method should return 0.
     */
    function columnCount()
    {
        return $this->_stmt->columnCount();
    }

    /**
     * errorCode
     * Fetch the SQLSTATE associated with the last operation on the statement handle
     *
     * @see Doctrine_Adapter_Interface::errorCode()
     * @return string       error code string
     */
    function errorCode()
    {
        return "";
    }

    /**
     * errorInfo
     * Fetch extended error information associated with the last operation on the statement handle
     *
     * @see Doctrine_Adapter_Interface::errorInfo()
     * @return array        error info array
     */
    function errorInfo()
    {
        return "";
    }

    /**
     * Executes a prepared statement
     *
     * If the prepared statement included parameter markers, you must either:
     * call PDOStatement->bindParam() to bind PHP variables to the parameter markers:
     * bound variables pass their value as input and receive the output value,
     * if any, of their associated parameter markers or pass an array of input-only
     * parameter values
     *
     *
     * @param array $params             An array of values with as many elements as there are
     *                                  bound parameters in the SQL statement being executed.
     * @return boolean                  Returns TRUE on success or FALSE on failure.
     */
    function execute($params = array())
    {
        var_dump($params);
        return $this->_stmt->execute($params);
    }

    /**
     * fetch
     *
     * @see Query::HYDRATE_* constants
     * @param integer $fetchStyle           Controls how the next row will be returned to the caller.
     *                                      This value must be one of the Query::HYDRATE_* constants,
     *                                      defaulting to Query::HYDRATE_BOTH
     *
     * @param integer $cursorOrientation    For a PDOStatement object representing a scrollable cursor,
     *                                      this value determines which row will be returned to the caller.
     *                                      This value must be one of the Query::HYDRATE_ORI_* constants, defaulting to
     *                                      Query::HYDRATE_ORI_NEXT. To request a scrollable cursor for your
     *                                      PDOStatement object,
     *                                      you must set the PDO::ATTR_CURSOR attribute to Doctrine::CURSOR_SCROLL when you
     *                                      prepare the SQL statement with Doctrine_Adapter_Interface->prepare().
     *
     * @param integer $cursorOffset         For a PDOStatement object representing a scrollable cursor for which the
     *                                      $cursorOrientation parameter is set to Query::HYDRATE_ORI_ABS, this value specifies
     *                                      the absolute number of the row in the result set that shall be fetched.
     *
     *                                      For a PDOStatement object representing a scrollable cursor for
     *                                      which the $cursorOrientation parameter is set to Query::HYDRATE_ORI_REL, this value
     *                                      specifies the row to fetch relative to the cursor position before
     *                                      PDOStatement->fetch() was called.
     *
     * @return mixed
     */
    function fetch($fetchStyle = null,
                          $cursorOrientation = null,
                          $cursorOffset = null)
    {
        return $this->_stmt->fetch($fetchStyle, $cursorOrientation, $cursorOffset);
    }

    /**
     * fetchAll
     * Returns an array containing all of the result set rows
     *
     * @param integer $fetchStyle           Controls how the next row will be returned to the caller.
     *                                      This value must be one of the Query::HYDRATE_* constants,
     *                                      defaulting to Query::HYDRATE_BOTH
     *
     * @param integer $columnIndex          Returns the indicated 0-indexed column when the value of $fetchStyle is
     *                                      Query::HYDRATE_COLUMN. Defaults to 0.
     *
     * @return array
     */
    function fetchAll($fetchStyle = null)
    {
        return $this->_stmt->fetchAll($fetchStyle);
    }

    /**
     * fetchColumn
     * Returns a single column from the next row of a
     * result set or FALSE if there are no more rows.
     *
     * @param integer $columnIndex          0-indexed number of the column you wish to retrieve from the row. If no
     *                                      value is supplied, PDOStatement->fetchColumn()
     *                                      fetches the first column.
     *
     * @return string                       returns a single column in the next row of a result set.
     */
    function fetchColumn($columnIndex = 0)
    {
        return $this->_stmt->fetchColumn($columnIndex);
    }

    /**
     * fetchObject
     * Fetches the next row and returns it as an object.
     *
     * Fetches the next row and returns it as an object. This function is an alternative to
     * PDOStatement->fetch() with Query::HYDRATE_CLASS or Query::HYDRATE_OBJ style.
     *
     * @param string $className             Name of the created class, defaults to stdClass.
     * @param array $args                   Elements of this array are passed to the constructor.
     *
     * @return mixed                        an instance of the required class with property names that correspond
     *                                      to the column names or FALSE in case of an error.
     */
    function fetchObject($className = 'stdClass', $args = array())
    {
        return $this->_stmt->fetchObject($className, $args);
    }

    /**
     * getAttribute
     * Retrieve a statement attribute
     *
     * @param integer $attribute
     * @see Doctrine::ATTR_* constants
     * @return mixed                        the attribute value
     */
    function getAttribute($attribute)
    {
        return $this->_stmt->getAttribute($attribute);
    }

    /**
     * getColumnMeta
     * Returns metadata for a column in a result set
     *
     * @param integer $column               The 0-indexed column in the result set.
     *
     * @return array                        Associative meta data array with the following structure:
     *
     *          native_type                 The PHP native type used to represent the column value.
     *          driver:decl_                type The SQL type used to represent the column value in the database. If the column in the result set is the result of a function, this value is not returned by PDOStatement->getColumnMeta().
     *          flags                       Any flags set for this column.
     *          name                        The name of this column as returned by the database.
     *          len                         The length of this column. Normally -1 for types other than floating point decimals.
     *          precision                   The numeric precision of this column. Normally 0 for types other than floating point decimals.
     *          pdo_type                    The type of this column as represented by the PDO::PARAM_* constants.
     */
    function getColumnMeta($column)
    {
        // Not required for ORM
        return "";
    }

    /**
     * nextRowset
     * Advances to the next rowset in a multi-rowset statement handle
     *
     * Some database servers support stored procedures that return more than one rowset
     * (also known as a result set). The nextRowset() method enables you to access the second
     * and subsequent rowsets associated with a PDOStatement object. Each rowset can have a
     * different set of columns from the preceding rowset.
     *
     * @return boolean                      Returns TRUE on success or FALSE on failure.
     */
    function nextRowset()
    {
        return $this->_stmt->nextRowset();
    }

    /**
     * rowCount
     * rowCount() returns the number of rows affected by the last DELETE, INSERT, or UPDATE statement
     * executed by the corresponding object.
     *
     * If the last SQL statement executed by the associated Statement object was a SELECT statement,
     * some databases may return the number of rows returned by that statement. However,
     * this behaviour is not guaranteed for all databases and should not be
     * relied on for portable applications.
     *
     * @return integer                      Returns the number of rows.
     */
    function rowCount()
    {
        return $this->_stmt->rowCount();
    }

    /**
     * setAttribute
     * Set a statement attribute
     *
     * @param integer $attribute
     * @param mixed $value                  the value of given attribute
     * @return boolean                      Returns TRUE on success or FALSE on failure.
     */
    function setAttribute($attribute, $value)
    {
        return $this->_stmt->setAttribute($attribute, $value);
    }

    /**
     * setFetchMode
     * Set the default fetch mode for this statement
     *
     * @param integer $mode                 The fetch mode must be one of the Query::HYDRATE_* constants.
     * @return boolean                      Returns 1 on success or FALSE on failure.
     */
    function setFetchMode($mode, $arg1)
    {
        return $this->_stmt->setFetchMode($mode);
    }
}

class Connection implements \Doctrine\DBAL\Driver\Connection
{
    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db = null;

    /**
     * @param Zend_Db_Adapter_Abstract $db
     */
    public function __construct(\Zend_Db_Adapter_Abstract $db)
    {
        $this->_db = $db;
    }

    /**
     * @param string $sql
     * @return \Doctrine\DBAL\Driver\Statement
     */
    function prepare($sql)
    {
        return new Statement($this->_db->prepare($sql), $sql);
    }

    /**
     * @return \Doctrine\DBAL\Driver\Statement
     */
    function query()
    {
        $sql = \func_get_arg(0);

        return new Statement($this->_db->query($sql), $sql);
    }

    /**
     * @param string $input
     * @return string
     */
    function quote($input)
    {
        $type = null;
        if(\func_num_args() >= 2) {
            $type = \func_get_arg(1);
        }

        return $this->_db->quote($input, $type);
    }

    /**
     * @param string $statement
     */
    function exec($statement)
    {
        $this->_db->query($statement);
        return 1;
    }

    /**
     * @return mixed
     */
    function lastInsertId()
    {
        $sequenceName = null;
        if(\func_num_args() > 0) {
            $sequenceName = \func_get_arg(0);
        }

        return $this->_db->lastInsertId($sequenceName);
    }

    /**
     * @return void
     */
    function beginTransaction()
    {
        $this->_db->beginTransaction();
    }

    function commit()
    {
        $this->_db->commit();
    }

    function rollBack()
    {
        $this->_db->rollBack();
    }

    function errorCode()
    {
        return 0;
    }

    function errorInfo()
    {
        return "";
    }
}

class ZendTests
{
    static public function suite()
    {
        $fn = function() {
            $eventManager = new \Doctrine\Common\EventManager();
            $config = new \Doctrine\DBAL\Configuration;

            $dbname = "doctrine_tests";

            $params = array('host' => 'localhost', 'dbname' => $dbname, 'driver' => 'mysqli', 'user' => 'root', 'password' => 'tribal');

            $zendDriver = new \Zend\Doctrine\Driver\ZendDriver('mysql');
            $conn = new \Doctrine\DBAL\Connection($params, $zendDriver, $config, $eventManager);

            $conn->getSchemaManager()->dropDatabase($dbname);
            $conn->getSchemaManager()->createDatabase($dbname);
            
            return $conn;
        };
        \Doctrine\Tests\TestUtil::$factoryCallback = $fn;

        return \Doctrine\Tests\AllTests::suite();
    }
}

$app = new Zend_Application();
$app->getBootstrap()->getPluginLoader()->addPrefixPath("Zend_Doctrine2_Application_Resource", "Zend/Doctrine2/Application/Resource");