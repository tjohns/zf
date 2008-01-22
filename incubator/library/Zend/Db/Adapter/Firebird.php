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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Firebird.php 5906 2007-07-28 02:58:20Z bkarwin $
 */


/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * @see Zend_Db_Profiler
 */
require_once 'Zend/Db/Profiler.php';

/**
 * @see Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';

/**
 * @see Zend_Db_Statement_Firebird
 */
require_once 'Zend/Db/Statement/Firebird.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Firebird extends Zend_Db_Adapter_Abstract
{

    /**
     * The transaction resource.
     *
     * @var transaction
     */
    protected $_transResource;

    /**
     * Return the status of current transaction.
     * @return bool
     */

    public function getTransaction()
	{
		return (is_resource($this->_transResource) ? $this->_transResource : null);
	}

    /**
     * Keys are UPPERCASE SQL datatypes or the constants
     * Zend_Db::INT_TYPE, Zend_Db::BIGINT_TYPE, or Zend_Db::FLOAT_TYPE.
     *
     * Values are:
     * 0 = 32-bit integer
     * 1 = 64-bit integer
     * 2 = float or decimal
     *
     * @var array Associative array of datatypes to values 0, 1, or 2.
     */
    protected $_numericDataTypes = array(
        Zend_Db::INT_TYPE    => Zend_Db::INT_TYPE,
        Zend_Db::BIGINT_TYPE => Zend_Db::BIGINT_TYPE,
        Zend_Db::FLOAT_TYPE  => Zend_Db::FLOAT_TYPE,
        'INT'                => Zend_Db::INT_TYPE,
        'INTEGER'            => Zend_Db::INT_TYPE,
        'SMALLINT'           => Zend_Db::INT_TYPE,
        'BIGINT'             => Zend_Db::BIGINT_TYPE,
        'INT64'              => Zend_Db::BIGINT_TYPE,
        'DECIMAL'            => Zend_Db::FLOAT_TYPE,
        'DOUBLE'             => Zend_Db::FLOAT_TYPE,
        'DOUBLE PRECISION'   => Zend_Db::FLOAT_TYPE,
        'NUMERIC'            => Zend_Db::FLOAT_TYPE,
        'FLOAT'              => Zend_Db::FLOAT_TYPE
    );

    /**
     * Quote a raw string.
     *
     * @param string $value     Raw string
     * @return string           Quoted string
     */
    protected function _quote($value)
    {
        if (is_int($value) || is_float($value)) {
            return $value;
        }
        $value = str_replace("'", "''", $value);
        return "'" . $value . "'";
    }

    /**
     * Return the most recent value from the specified sequence in the database.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2, Firebird).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function lastSequenceId($sequenceName)
    {
        $this->_connect();
        $sql = 'SELECT GEN_ID('.$this->quoteIdentifier($sequenceName, true).', 0) FROM RDB$DATABASE';
        $value = $this->fetchOne($sql);
        return $value;
    }

    /**
     * Generate a new value from the specified sequence in the database, and return it.
     * This is supported only on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2, Firebird).  Other RDBMS brands return null.
     *
     * @param string $sequenceName
     * @return string
     */
    public function nextSequenceId($sequenceName)
    {
        $this->_connect();
        $sql = 'SELECT GEN_ID('.$this->quoteIdentifier($sequenceName, true).', 1) FROM RDB$DATABASE';
        $value = $this->fetchOne($sql);
        return $value;
    }

    /**
     * Returns the symbol the adapter uses for delimiting identifiers.
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return '"';
    }

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $data = $this->fetchCol('SELECT RDB$RELATION_NAME FROM RDB$RELATIONS WHERE RDB$SYSTEM_FLAG = 0');
        foreach($data as &$v)
            $v = trim($v);
        return $data;
    }

    /**
     * Returns the column descriptions for a table.
     *
     * The return value is an associative array keyed by the column name,
     * as returned by the RDBMS.
     *
     * The value of each array element is an associative array
     * with the following keys:
     *
     * SCHEMA_NAME      => string; name of database or schema
     * TABLE_NAME       => string;
     * COLUMN_NAME      => string; column name
     * COLUMN_POSITION  => number; ordinal position of column in table
     * DATA_TYPE        => string; SQL datatype name of column
     * DEFAULT          => string; default expression of column, null if none
     * NULLABLE         => boolean; true if column can have nulls
     * LENGTH           => number; length of CHAR/VARCHAR
     * SCALE            => number; scale of NUMERIC/DECIMAL
     * PRECISION        => number; precision of NUMERIC/DECIMAL
     * UNSIGNED         => boolean; unsigned property of an integer type
     * PRIMARY          => boolean; true if column is part of the primary key
     * PRIMARY_POSITION => integer; position of column in primary key
     * IDENTITY         => integer; true if column is auto-generated with unique values
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $fieldMaps = array(
            'TEXT'      => 'CHAR',
            'VARYING'   => 'VARCHAR',
            'SHORT'     => 'SMALLINT',
            'LONG'      => 'INTEGER',
            'D_FLOAT'   => 'DOUBLE',
            'INT64'     => 'BIGINT',
            'TYPE_DATE' => 'DATE',
            'TYPE_TIME' => 'TIME',
            'NUMERIC'   => 'NUMERIC',
            'DATE'      => 'DATE',
            'BLOB'      => 'BLOB',
            'TIMESTAMP' => 'TIMESTAMP'
        );

        $sql = 'select
                    RF.RDB$RELATION_NAME, \'\', RF.RDB$FIELD_NAME, T.RDB$TYPE_NAME,
                    RF.RDB$DEFAULT_VALUE, RF.RDB$NULL_FLAG, RF.RDB$FIELD_POSITION,
                    F.RDB$CHARACTER_LENGTH, F.RDB$FIELD_SCALE, F.RDB$FIELD_PRECISION,
                    IXS.RDB$FIELD_POSITION, IXS.RDB$FIELD_POSITION
                from RDB$RELATION_FIELDS RF
                left join RDB$RELATION_CONSTRAINTS RC
                    on (RF.RDB$RELATION_NAME = RC.RDB$RELATION_NAME and RC.RDB$CONSTRAINT_TYPE = \'PRIMARY KEY\')
                left join RDB$INDEX_SEGMENTS IXS
                    on (IXS.RDB$FIELD_NAME = RF.RDB$FIELD_NAME and RC.RDB$INDEX_NAME = IXS.RDB$INDEX_NAME)
                inner join RDB$FIELDS F on (RF.RDB$FIELD_SOURCE = F.RDB$FIELD_NAME)
                inner join RDB$TYPES T on (T.RDB$TYPE = F.RDB$FIELD_TYPE and T.RDB$FIELD_NAME = \'RDB$FIELD_TYPE\')
                where ' . $this->quoteInto('(UPPER(RF.RDB$RELATION_NAME) = UPPER(?)) ', $tableName) . '
                order by RF.RDB$FIELD_POSITION';

        $stmt = $this->query($sql);

        /**
         * Use FETCH_NUM so we are not dependent on the CASE attribute of the PDO connection
         */
        $result = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        $table_name      = 0;
        $owner           = 1;
        $column_name     = 2;
        $data_type       = 3;
        $data_default    = 4;
        $nullable        = 5;
        $column_id       = 6;
        $data_length     = 7;
        $data_scale      = 8;
        $data_precision  = 9;
        $constraint_type = 10;
        $position        = 11;

        $desc = array();
        foreach ($result as $key => $row) {
            list ($primary, $primaryPosition, $identity) = array(false, null, false);
            if (strlen($row[$constraint_type])) {
                $primary = true;
                $primaryPosition = $row[$position];
                /**
                 * Firebird does not support auto-increment keys.
                 */
                $identity = false;
            }

            //Mapping Numerics thats stored as INTEGER or INT64 internally in db
            if ($row[$data_scale] < 0){
                if ($row[$data_type] == 'SHORT') $row[$data_precision] = 4;
                if ($row[$data_type] == 'LONG' ) $row[$data_precision] = 9;
                if ($row[$data_type] == 'INT64') $row[$data_precision] = 18;
                $row[$data_type] = 'NUMERIC';
                $row[$data_scale] = -$row[$data_scale];
            }

            $row[$data_type] = trim($row[$data_type]);
            $row[$data_type] = ($fieldMaps[$row[$data_type]]) ? $fieldMaps[$row[$data_type]] : $row[$data_type];

            $desc[trim($row[$column_name])] = array(
                'SCHEMA_NAME'      => '',
                'TABLE_NAME'       => trim($row[$table_name]),
                'COLUMN_NAME'      => trim($row[$column_name]),
                'COLUMN_POSITION'  => $row[$column_id] +1,
                'DATA_TYPE'        => $row[$data_type],
                'DEFAULT'          => $row[$data_default],
                'NULLABLE'         => (bool) ($row[$nullable] != '1'),
                'LENGTH'           => $row[$data_length],
                'SCALE'            => ($row[$data_scale] == 0 ? null : $row[$data_scale]),
                'PRECISION'        => ($row[$data_precision] == 0 ? null : $row[$data_precision]),
                'UNSIGNED'         => false,
                'PRIMARY'          => $primary,
                'PRIMARY_POSITION' => ($primary ? $primaryPosition+1 : null),
                'IDENTITY'         => $identity
            );
        }
        return $desc;
    }

    /**
     * Creates a connection to the database.
     *
     * @return void
     * @throws Zend_Db_Adapter_Firebird_Exception
     */
    protected function _connect()
    {
        if (is_resource($this->_connection)) {
            return;
        }

        if (isset($this->_config['port'])) {
            $port = '/' . (integer) $this->_config['port'];
        } else {
            $port = '';
        }

        // Suppress connection warnings here.
        // Throw an exception instead.
        @$this->_connection = ibase_connect(
                                $this->_config['host'] .$port. ':' . $this->_config['dbname'],
                                $this->_config['username'],
                                $this->_config['password'],
                                $this->_config['charset'],
                                $this->_config['buffers'],
                                $this->_config['dialect'],
                                $this->_config['role']
                              );

        if ($this->_connection === false || ibase_errcode()) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Adapter/Firebird/Exception.php';
            throw new Zend_Db_Adapter_Firebird_Exception(ibase_errmsg());
        }
    }

    /**
     * Force the connection to close.
     *
     * @return void
     */
    public function closeConnection()
    {
        if (is_resource($this->_transResource)) {
            ibase_rollback($this->_transResource);
        }
        $this->_transResource = null;

        if (is_resource($this->_connection)) {
            ibase_close($this->_connection);
        }
        $this->_connection = null;
    }

    /**
     * Prepare a statement and return a PDOStatement-like object.
     *
     * @param  string  $sql  SQL query
     * @return Zend_Db_Statement_Firebird
     */
    public function prepare($sql)
    {
        $this->_connect();

        $stmt = new Zend_Db_Statement_Firebird($this, $sql);
        if ($stmt === false) {
            return false;
        }
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT column.
     *
     * As a convention, on RDBMS brands that support sequences
     * (e.g. Oracle, PostgreSQL, DB2, Firebird), this method forms the name of a sequence
     * from the arguments and returns the last id generated by that sequence.
     * On RDBMS brands that support IDENTITY/AUTOINCREMENT columns, this method
     * returns the last value generated for such a column, and the table name
     * argument is disregarded.
     *
     * Firebird does not support IDENTITY columns, so if the sequence is not
     * specified, this method returns null.
     *
     * @param string $tableName   OPTIONAL Name of table.
     * @param string $primaryKey  OPTIONAL Name of primary key column.
     * @return string
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_seq';
            return $this->lastSequenceId($sequenceName);
        }

        // No support for IDENTITY columns; return null
        return null;
    }

    /**
     * Begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        $this->_connect();
		if (is_resource($this->_transResource)){
			return;
		}

        $this->_transResource = ibase_trans(IBASE_DEFAULT, $this->_connection);
    }

    /**
     * Commit a transaction.
     *
     * @return void
     */
    protected function _commit()
    {
        if (!ibase_commit(is_resource($this->_transResource) ? $this->_transResource : $this->_connection)) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Adapter/Firebird/Exception.php';
            throw new Zend_Db_Adapter_Firebird_Exception(ibase_errmsg());
        }
		$this->_transResource = null;
    }

    /**
     * Roll-back a transaction.
     *
     * @return void
     */
    protected function _rollBack()
    {
        if (!ibase_rollback(is_resource($this->_transResource) ? $this->_transResource : $this->_connection)) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Adapter/Firebird/Exception.php';
            throw new Zend_Db_Adapter_Firebird_Exception(ibase_errmsg());
        }
		$this->_transResource = null;
    }

    /**
     * Set the fetch mode.
     *
     * @param int $mode
     * @return void
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Zend_Db::FETCH_LAZY:
            case Zend_Db::FETCH_ASSOC:
            case Zend_Db::FETCH_NUM:
            case Zend_Db::FETCH_BOTH:
            case Zend_Db::FETCH_NAMED:
            case Zend_Db::FETCH_OBJ:
                $this->_fetchMode = $mode;
                break;
            case Zend_Db::FETCH_BOUND: // bound to PHP variable
                /**
                 * @see Zend_Db_Adapter_Firebird_Exception
                 */
                require_once 'Zend/Db/Adapter/Firebird/Exception.php';
                throw new Zend_Db_Adapter_Firebird_Exception('FETCH_BOUND is not supported yet');
                break;
            default:
                /**
                 * @see Zend_Db_Adapter_Firebird_Exception
                 */
                require_once 'Zend/Db/Adapter/Firebird/Exception.php';
                throw new Zend_Db_Adapter_Firebird_Exception("Invalid fetch mode '$mode' specified");
        }
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param int $count
     * @param int $offset OPTIONAL
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Adapter/Firebird/Exception.php';
            throw new Zend_Db_Adapter_Firebird_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            /**
             * @see Zend_Db_Adapter_Firebird_Exception
             */
            require_once 'Zend/Db/Adapter/Firebird/Exception.php';
            throw new Zend_Db_Adapter_Firebird_Exception("LIMIT argument offset=$offset is not valid");
        }

        $sql = substr_replace($sql, "select first $count skip $offset ", stripos($sql, 'select'), 6);

        /* compatible with FB2
        $sql .= " rows $count";
        if ($offset > 0) {
            $sql .= " to $offset";
        }*/

        return $sql;
    }

    /**
     * Check if the adapter supports real SQL parameters.
     *
     * @param string $type 'positional' or 'named'
     * @return bool
     */
    public function supportsParameters($type)
    {
        switch ($type) {
            case 'positional':
                return true;
            case 'named':
            default:
                return false;
        }
    }

        /**
     * Quote a table identifier and alias.
     *
     * @param string|array|Zend_Db_Expr $ident The identifier or expression.
     * @param string $alias An alias for the table.
     * @param boolean $auto If true, heed the AUTO_QUOTE_IDENTIFIERS config option.
     * @return string The quoted identifier and alias.
     */
    public function quoteTableAs($ident, $alias, $auto=false)
    {
        return $this->_quoteIdentifierAs($ident, $alias, $auto, ' ');
    }

}
