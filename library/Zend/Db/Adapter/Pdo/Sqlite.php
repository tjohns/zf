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
 * Zend_Db_Adapter_Pdo_Abstract
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
class Zend_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * DSN builder
     */
    protected function _dsn()
    {
        $name = $this->_config['dbname'];
        return "sqlite:$name";
    }


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        return $this->quote($ident);
    }


    /**
     * Returns a list of the tables in the database.
     *
     * @return array
     */
    public function listTables()
    {
        $sql = "SELECT name FROM sqlite_master WHERE type='table' "
             . "UNION ALL SELECT name FROM sqlite_temp_master "
             . "WHERE type='table' ORDER BY name";

        return $this->fetchCol($sql);
    }


    /**
     * Returns the column descriptions for a table.
     *
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "PRAGMA table_info($table)";
        $result = $this->fetchAll($sql);
        $descr = array();
        foreach ($result as $key => $val) {
            $descr[$val['name']] = array(
                'name'    => $val['name'],
                'type'    => $val['type'],
                'notnull' => (bool) $val['notnull'],
                'default' => $val['dflt_value'],
                'primary' => (bool) $val['pk'],
            );
        }
        return $descr;
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
