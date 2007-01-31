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
 * Zend_Db_Adapter_Pdo
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
class Zend_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'mysql';

    /**
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        return "`";
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
        $sql = "DESCRIBE $tableName";
        $result = $this->fetchAll($sql);
        $desc = array();
        foreach ($result as $key => $row) {
            $desc[$row['field']] = array(
                'SCHEMA_NAME' => null,
                'TABLE_NAME'  => $tableName,
                'COLUMN_NAME' => $row['field'],
                'DATA_TYPE'   => $row['type'],
                'DEFAULT'     => $row['default'],
                'NULLABLE'    => (bool) ($row['null'] == 'YES'),
                'LENGTH'      => null,
                'SCALE'       => null,
                'PRECISION'   => null,
                'PRIMARY'     => (bool) (strtoupper($row['key']) == 'PRI')
            );
        }
        return $desc;
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
            $offset = ($offset > 0) ? $offset : 0;
            $sql .= " LIMIT $offset, $count";
        }
        return $sql;
    }

}
