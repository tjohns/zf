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
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once "PHPUnit/Extensions/Database/DataSet/QueryDataSet.php";

require_once "PHPUnit/Extensions/Database/DB/IDatabaseConnection.php";

/**
 * @see Zend_Test_PHPUnit_Database_DataSet_DbTable
 */
require_once "Zend/Test/PHPUnit/Database/DataSet/DbTable.php";

/**
 * Aggregate several Zend_Db_Table instances into a dataset.
 *
 * @uses       Zend_Db_Table
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Test_PHPUnit_Database_DataSet_DbTableDataSet extends PHPUnit_Extensions_Database_DataSet_QueryDataSet
{
    /**
     * Creates a new dataset using the given database connection.
     *
     * @param PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection
     */
    public function __construct(PHPUnit_Extensions_Database_DB_IDatabaseConnection $databaseConnection)
    {
        if( !($databaseConnection instanceof Zend_Test_PHPUnit_Database_Connection) ) {
            require_once "Zend/Test/PHPUnit/Database/Exception.php";
            throw new Zend_Test_PHPUnit_Database_Exception("Zend_Test_PHPUnit_Database_DataSet_QueryDataSet only works with Zend_Test_PHPUnit_Database_Connection connections-");
        }
        $this->databaseConnection = $databaseConnection;
    }

    /**
     * Add a Table dataset representation by specifiying an arbitrary select query.
     *
     * By default a select * will be done on the given tablename.
     *
     * @param Zend_Db_Table_Abstract $table
     * @param string|Zend_Db_Select $query
     * @param string $where
     * @param string $order
     * @param string $count
     * @param string $offset
     */
    public function addTable($table, $where = null, $order = null, $count = null, $offset = null)
    {
        $this->tables[$table] = new Zend_Test_PHPUnit_Database_DataSet_DbTable($table, $where, $order, $count, $offset);
    }
}