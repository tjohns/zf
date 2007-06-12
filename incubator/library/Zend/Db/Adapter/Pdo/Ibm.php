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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc.
 * (http://www.zend.com)
 * @license  http://www.zend.com/license/framework/1_0.txt
 * Zend Framework License version 1.0
 *
 */

/** Zend_Db_Adapter_Pdo_Abstract */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';

/** Zend_Db_Statement_Pdo_Ibm */
require_once 'Zend/Db/Statement/Pdo/Ibm.php';

/**
 * Zend_Db_Adapter_Exception
 */
require_once 'Zend/Db/Adapter/Exception.php';


/**
 * @package    Zend_Db
 * @copyright  Copyright (c) 2005-2007 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 * @author     Manas Dadarkar <manas@us.ibm.com>
 * @author     Kellen Bombardier <kfbombar@us.ibm.com>
 * @author     Salvador Ledezma <ledezma@us.ibm.com>
 */

class Zend_Db_Adapter_Pdo_Ibm extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'ibm';

    /**
     * Creates a PDO DSN for the adapter from $this->_config settings.
     *
     * @return string
     */
    protected function _dsn()
    {
        $this->_checkRequiredOptions($this->_config);
        // baseline of DSN parts
        $dsn = $this->_config;

        // check if using full connection string
        if (array_key_exists('host', $this->_config)) {
            // if the host is specified, use extended connection params
            $dsn = ';DATABASE=' . $this->_config['dbname']
            . ';HOSTNAME=' . $this->_config['host']
            . ';PORT='     . $this->_config['port']
            // PDO_IBM supports only DB2 TCPIP protocol
            . ';PROTOCOL=' . 'TCPIP'
            . ';UID='      . $this->_config['username']
            . ';PWD='      . $this->_config['password']
            . ';';
            // don't pass the username and password in the DSN
            // unset($dsn['username']);
            // unset($dsn['password']);
        } else {
            // catalogued connection
            $dsn = $this->_config['dbname'];
        }
        return $this->_pdoType . ': ' . $dsn;
    }

    protected function _checkRequiredOptions(array $config)
    {
        parent::_checkRequiredOptions($config);

        if (array_key_exists('host', $this->_config) &&
        !array_key_exists('port', $config)) {
            throw new Zend_Db_Adapter_Exception("Configuration must have a key for 'port' when 'host' is specified");
        }
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
        $stmt = new Zend_Db_Statement_Pdo_Ibm($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Returns a list of the tables in the database.
     * NOTE: Currently only availabe on DB2 LUW.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT tabname "
        . "FROM SYSCAT.TABLES ";
        return $this->fetchCol($sql);
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
     *
     * @todo Discover integer unsigned property.
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $sql = "SELECT DISTINCT c.tabschema, c.tabname, c.colname, c.colno,
              c.typename, c.default, c.nulls, c.length, c.scale,
              c.identity, tc.type AS tabconsttype, k.colseq
            FROM syscat.columns c
              LEFT JOIN (syscat.keycoluse k JOIN syscat.tabconst tc
                ON (k.tabschema = tc.tabschema
                  AND k.tabname = tc.tabname
                  AND tc.type = 'P'))
              ON (c.tabschema = k.tabschema
                AND c.tabname = k.tabname
                AND c.colname = k.colname)
            WHERE c.tabname = ".$this->quote($tableName);
        if ($schemaName) {
            $sql .= " AND c.tabschema = ".$this->quote($schemaName);
        }
        $sql .= " ORDER BY c.colno";

        $desc = array();
        $stmt = $this->query($sql);

        /**
         * To avoid case issues, fetch using FETCH_NUM
         */
        $result = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        /**
         * The ordering of columns is defined by the query so we can map
         * to variables to improve readability
         */
        $tabschema      = 0;
        $tabname        = 1;
        $colname        = 2;
        $colno          = 3;
        $typename       = 4;
        $default        = 5;
        $nulls          = 6;
        $length         = 7;
        $scale          = 8;
        $identityCol    = 9;
        $tabconstype    = 10;
        $colseq         = 11;

        foreach ($result as $key => $row) {
            list ($primary, $primaryPosition, $identity) = array(false, null, false);
            if ($row[$tabconstype] == 'P') {
                $primary = true;
                $primaryPosition = $row[$colseq];
            }
            /**
             * In IBM DB2, an column can be IDENTITY
             * even if it is not part of the PRIMARY KEY.
             */
            if ($row[$identityCol] == 'Y') {
                $identity = true;
            }


            // only colname needs to be case adjusted
            $desc[$this->foldCase($row[$colname])] = array(
            'SCHEMA_NAME'      => $row[$tabschema],
            'TABLE_NAME'       => $row[$tabname],
            'COLUMN_NAME'      => $row[$colname],
            'COLUMN_POSITION'  => $row[$colno]+1,
            'DATA_TYPE'        => $row[$typename],
            'DEFAULT'          => $row[$default],
            'NULLABLE'         => (bool) ($row[$nulls] == 'Y'),
            'LENGTH'           => $row[$length],
            'SCALE'            => $row[$scale],
            'PRECISION'        => ($row[$typename] == 'DECIMAL' ? $row[$length] : 0),
            'UNSIGNED'         => null, // @todo
            'PRIMARY'          => $primary,
            'PRIMARY_POSITION' => $primaryPosition,
            'IDENTITY'         => $identity
            );
        }

        return $desc;
    }

    /**
     *  Inserts a table row with specified data.
     *	Special handling for PDO_IBM
     * remove empty slots
     *
     * @param mixed $table The table to insert data into.
     * @param array $bind Column-value pairs.
     * @return int The number of affected rows.
     */
    public function insert($table, array $bind)
    {
        $newbind = array();
        if (is_array($bind)) {
            foreach ($bind as $name => $value) {
                if(!is_null($value)) {
                    $newbind[$name] = $value;
                }
            }
        }
        return parent::insert($table, $newbind);
    }

    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @param string $sql
     * @param integer $count
     * @param integer $offset OPTIONAL
     * @return string
     */
    public function limit($sql, $count, $offset = 0)
    {
        $count = intval($count);
        if ($count <= 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument count=$count is not valid");
        }

        $offset = intval($offset);
        if ($offset < 0) {
            throw new Zend_Db_Adapter_Exception("LIMIT argument offset=$offset is not valid");
        }

        if ($offset == 0) {
            $limit_sql = $sql . " FETCH FIRST $count ROWS ONLY";
            return $limit_sql;
        }

        /**
         * DB2 does not implement the LIMIT clause as some RDBMS do.
         * We have to simulate it with subqueries and ROWNUM.
         * Unfortunately because we use the column wildcard "*",
         * this puts an extra column into the query result set.
         */
        $limit_sql = "SELECT z2.*
            FROM (
                SELECT ROW_NUMBER() OVER() AS \"ZEND_DB_ROWNUM\", z1.*
                FROM (
                    " . $sql . "
                ) z1
            ) z2
            WHERE z2.zend_db_rownum BETWEEN " . ($offset+1) . " AND " . ($offset+$count);
        return $limit_sql;
    }


    /**
     * Gets the last ID generated automatically by an IDENTITY/AUTOINCREMENT
     * column.
     * The IDENTITY_VAL_LOCAL() function gives the last generated identity value
     * in the current process, even if it was for a GENERATED column.
     *
     * @param string $tableName OPTIONAL
     * @param string $primaryKey OPTIONAL
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $this->_connect();

        if ($tableName !== null) {
            $sequenceName = $tableName;
            if ($primaryKey) {
                $sequenceName .= "_$primaryKey";
            }
            $sequenceName .= '_seq';
            return $this->lastSequenceId($sequenceName);
        }
        # PDO_IBM doesn't support lastInsertId function.
        # We use IDENTITY_VAL_LOCAL() to get the same information

        $sql = 'SELECT IDENTITY_VAL_LOCAL() AS VAL FROM SYSIBM.SYSDUMMY1';
        $value = $this->fetchOne($sql);
        return $value;
    }


    /**
     * Return the most recent value from the specified sequence in the database.
     *
     * @param string $sequenceName
     * @return integer
     */
    public function lastSequenceId($sequenceName)
    {
        $this->_connect();
        $sql = 'SELECT PREVVAL FOR '.$this->quoteIdentifier($sequenceName).' AS VAL FROM SYSIBM.SYSDUMMY1';
        $value = $this->fetchOne($sql);
        return $value;
    }

    /**
     * Generate a new value from the specified sequence in the database,
     * and return it.
     *
     * @param string $sequenceName
     * @return integer
     */
    public function nextSequenceId($sequenceName)
    {
        $this->_connect();
        $sql = 'SELECT NEXTVAL FOR '.$this->quoteIdentifier($sequenceName).' AS VAL FROM SYSIBM.SYSDUMMY1';
        $value = $this->fetchOne($sql);
        return $value;
    }
}
