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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 


/**
 * Zend_Db_Adapter_Pdo_Abstract
 */
require_once 'Zend/Db/Adapter/Pdo/Abstract.php';


/**
 * Class for connecting to MySQL databases and performing common operations.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Adapter
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Adapter_Pdo_Sqlite extends Zend_Db_Adapter_Pdo_Abstract
{
    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration
     * options.  These options are common to most adapters:
     *
     * dbname   => (string) The name of the database to user (required,
     *                      use :memory: for memory-based database)
     * dsnprefix => (string) The PDO driver's DSN prefix (optional).
     *
     * @param array $config An array of configuration keys.
     */
    public function __construct($config)
    {
        if (!isset($config['dsnprefix'])) // allows use of sqlite2 DSN prefix
            $config['dsnprefix'] = 'sqlite';

        return parent::__construct($config);
    }


    /**
     * DSN builder
     */
    protected function _dsn()
    {
        return $this->_config['dsnprefix'].':'.$this->_config['dbname'];
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
