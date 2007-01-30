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
 */

/**
 * Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';

/**
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Pgsql extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'pgsql';

    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        // @todo use a better query with joins instead of subqueries
        $sql = "SELECT c.relname AS table_name "
             . "FROM pg_class c, pg_user u "
             . "WHERE c.relowner = u.usesysid AND c.relkind = 'r' "
             . "AND NOT EXISTS (SELECT 1 FROM pg_views WHERE viewname = c.relname) "
             . "AND c.relname !~ '^(pg_|sql_)' "
             . "UNION "
             . "SELECT c.relname AS table_name "
             . "FROM pg_class c "
             . "WHERE c.relkind = 'r' "
             . "AND NOT EXISTS (SELECT 1 FROM pg_views WHERE viewname = c.relname) "
             . "AND NOT EXISTS (SELECT 1 FROM pg_user WHERE usesysid = c.relowner) "
             . "AND c.relname !~ '^pg_'";

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
     * SCHEMA_NAME => string; name of database or schema
     * TABLE_NAME  => string;
     * COLUMN_NAME => string; column name
     * DATATYPE    => string; SQL datatype name of column
     * DEFAULT     => default value of column, null if none
     * NULLABLE    => boolean; true if column can have nulls
     * LENGTH      => length of CHAR/VARCHAR
     * SCALE       => scale of NUMERIC/DECIMAL
     * PRECISION   => precision of NUMERIC/DECIMAL
     * PRIMARY     => boolean; true if column is part of the primary key
     *
     * @param string $tableName
     * @param string $schemaName OPTIONAL
     * @return array
     */
    public function describeTable($tableName, $schemaName = null)
    {
        $sql = "SELECT a.attnum, a.attname AS field, t.typname AS type, format_type(a.atttypid, a.atttypmod) AS complete_type, "
             . "a.attnotnull AS isnotnull, "
             . "( SELECT 't' "
             . "FROM pg_index "
             . "WHERE c.oid = pg_index.indrelid "
             . "AND pg_index.indkey[0] = a.attnum "
             . "AND pg_index.indisprimary = 't') AS pri, "
             . "(SELECT pg_attrdef.adsrc "
             . "FROM pg_attrdef "
             . "WHERE c.oid = pg_attrdef.adrelid "
             . "AND pg_attrdef.adnum=a.attnum) AS default "
             . "FROM pg_attribute a, pg_class c, pg_type t "
             . "WHERE c.relname = '$table' "
             . "AND a.attnum > 0 "
             . "AND a.attrelid = c.oid "
             . "AND a.atttypid = t.oid "
             . "ORDER BY a.attnum ";

        /*
         * @todo use a better query with joins instead of subqueries
         *
        $sql = "SELECT a.attnum, a.attname AS field, t.typname AS type,
                FORMAT_TYPE(a.atttypid, a.atttypmod) AS complete_type,
                a.attnotnull AS nullable, COALESCE(i.indrelid, 0) AS pri
            FROM pg_attribute AS a
                JOIN pg_class AS c ON a.attrelid = c.oid
                JOIN pg_type AS t ON a.atttypid = t.oid
                LEFT OUTER JOIN pg_index AS i ON (i.indrelid = c.oid AND i.indkey[0] = a.attnum AND i.indisprimary = 't')
            WHERE c.relname = '$tableName' AND a.attnum > 0"
         */

        $result = $this->query($sql);
        $descr = array();
        while ($row = $result->fetch()) {
            if ($val['type'] === 'varchar') {
                // need to add length to the type so we are compatible with
                // Zend_Db_Adapter_Pdo_Pgsql!
                $length = preg_replace('~.*\(([0-9]*)\).*~', '$1', $val['complete_type']);
                $val['type'] .= '(' . $length . ')';
            }
            $descr[$val['field']] = array(
                'name'    => $val['field'],
                'type'    => $val['type'],
                'notnull' => ($val['isnotnull'] == ''),
                'default' => $val['default'],
                'primary' => ($val['pri'] == 't'),
            );

            /*
             * @todo conform to standard format
             *
            $desc[$row['field']] = array(
                'SCHEMA_NAME' => '',
                'TABLE_NAME'  => $tableName,
                'COLUMN_NAME' => $row['field'],
                'DATA_TYPE'   => $row['type'],
                'DEFAULT'     => $row['default'],
                'NULLABLE'    => (bool) ($row['null'] == 'YES'),
                'LENGTH'      => ''
                'SCALE'       => ''
                'PRECISION'   => ''
                'PRIMARY'     => (bool) (strtoupper($val['key']) == 'PRI')
            );
             */
        }
        return $descr;
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
        if ($count > 0) {
            $sql .= " LIMIT $count";
            if ($offset > 0) {
                $sql .= " OFFSET $offset";
            }
        }
        return $sql;
    }

    /**
     * Gets the last inserted ID.
     *
     * @param  string $tableName   OPTIONAL table or sequence name needed for some PDO drivers
     * @param  string $primaryKey  OPTIONAL primary key in $tableName need for some PDO drivers
     * @return integer
     */
    public function lastInsertId($tableName = null, $primaryKey = null)
    {
        $this->_connect();
        return $this->_connection->lastInsertId($tableName .'_'. $primaryKey .'_seq');
    }

}
