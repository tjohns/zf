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
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';


/**
 * Zend_Db_Table_Rowset_Abstract
 */
require_once 'Zend/Db/Table/Rowset/Abstract.php';


/**
 * Zend_Db_Table_Row_Abstract
 */
require_once 'Zend/Db/Table/Row/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Db_Table_Plugin_Interface
{
    public function __construct($options = null);

    public function preInitTable(Zend_Db_Table_Abstract $table);
    public function postInitTable(Zend_Db_Table_Abstract $table);

    public function preInitRowset(Zend_Db_Table_Rowset_Abstract $rowset);
    public function postInitRowset(Zend_Db_Table_Rowset_Abstract $rowset);

    public function preInitRow(Zend_Db_Table_Row_Abstract $row);
    public function postInitRow(Zend_Db_Table_Row_Abstract $row);

    public function preFetchTable(Zend_Db_Table_Abstract $table, Zend_Db_Table_Select $select);
    public function postFetchTable(Zend_Db_Table_Abstract $table, array $data);

    public function preSaveTable(Zend_Db_Table_Abstract $table, array $data);
    public function postSaveTable(Zend_Db_Table_Abstract $table);

    public function preInsertTable(Zend_Db_Table_Abstract $table, array $data);
    public function postInsertTable(Zend_Db_Table_Abstract $table, array $data);

    public function preUpdateTable(Zend_Db_Table_Abstract $table, array $data, $where = null);
    public function postUpdateTable(Zend_Db_Table_Abstract $table, array $data, $where = null);

    public function preDeleteTable(Zend_Db_Table_Abstract $table, $where = null);
    public function postDeleteTable(Zend_Db_Table_Abstract $table);

    public function preSaveRow(Zend_Db_Table_Row_Abstract $row);
    public function postSaveRow(Zend_Db_Table_Row_Abstract $row);

    public function preInsertRow(Zend_Db_Table_Row_Abstract $row);
    public function postInsertRow(Zend_Db_Table_Row_Abstract $row);

    public function preUpdateRow(Zend_Db_Table_Row_Abstract $row);
    public function postUpdateRow(Zend_Db_Table_Row_Abstract $row);

    public function preDeleteRow(Zend_Db_Table_Row_Abstract $row);
    public function postDeleteRow(Zend_Db_Table_Row_Abstract $row);

    public function getColumn(Zend_Db_Table_Row_Abstract $row, $columnName, $value);
    public function setColumn(Zend_Db_Table_Row_Abstract $row, $columnName, $value);
}