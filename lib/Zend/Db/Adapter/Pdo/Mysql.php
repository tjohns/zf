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
 * Zend_Db_Adapter_Pdo
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
class Zend_Db_Adapter_Pdo_Mysql extends Zend_Db_Adapter_Pdo_Abstract
{

    /**
     * PDO type.
     *
     * @var string
     */
    protected $_pdoType = 'mysql';


    /**
     * Quotes an identifier.
     *
     * @param string $ident The identifier.
     * @return string The quoted identifier.
     */
    public function quoteIdentifier($ident)
    {
        $ident = str_replace('`', '\`', $ident);
        return "`$ident`";
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
     * @return array
     */
    public function describeTable($table)
    {
        $sql = "DESCRIBE $table";
        $result = $this->fetchAll($sql);
        $descr = array();
        foreach ($result as $key => $val) {
            $descr[$val['field']] = array(
                'name'    => $val['field'],
                'type'    => $val['type'],
                'notnull' => (bool) ($val['null'] === ''), // not null is empty, null is yes
                'default' => $val['default'],
                'primary' => (strtolower($val['key']) == 'pri'),
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
