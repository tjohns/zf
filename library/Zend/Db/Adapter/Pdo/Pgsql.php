<?php
/**
 *  +----------------------------------------------------------------------+
 *  | Zend Framework                                                       |
 *  +----------------------------------------------------------------------+
 *  | Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com) |
 *  +----------------------------------------------------------------------+
 *  | This source file is subject to version 1.0 of the Zend Framework     |
 *  | license, that is bundled with this package in the file LICENSE, and  |
 *  | is available through the world-wide-web at the following url:        |
 *  | http://www.zend.com/license/framework/1_0.txt.                       |
 *  | If you did not receive a copy of the Zend license and are unable to  |
 *  | obtain it through the world-wide-web, please send a note to          |
 *  | license@zend.com so we can mail you a copy immediately.              |
 *  +----------------------------------------------------------------------+
 *
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */


/**
 * Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';


/**
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
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
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        return '"' . $this->quote($ident) . '"';
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
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
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "SELECT * FROM $table LIMIT 1";
        return $this->_describeTable($sql);
    }



    /**
     * Adds an adapter-specific LIMIT clause to the SELECT statement.
     *
     * @return string
     */
    public function limit($sql, $count, $offset)
    {
        if ($count > 0) {
            $sql .= "LIMIT $count";
            if ($offset > 0) {
                $sql .= " OFFSET $offset";
            }
        }
        return $sql;
    }
}
