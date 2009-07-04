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

/**
 * @see Zend_Db_Table_Rowset_Abstract
 */
require_once "Zend/Db/Table/Rowset/Abstract.php";

require_once "PHPUnit/Extensions/Database/DataSet/AbstractTable.php";

/**
 * Use a Zend_Db Rowset as a datatable for assertions with other PHPUnit Database extension tables.
 *
 * @uses       PHPUnit_Extensions_Database_DataSet_AbstractTable
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Test_PHPUnit_Database_DataSet_DbRowset extends PHPUnit_Extensions_Database_DataSet_AbstractTable
{
    /**
     * Construct Table object from a Zend_Db_Table_Rowset
     * 
     * @param Zend_Db_Table_Rowset_Abstract $rowset
     */
    public function __construct(Zend_Db_Table_Rowset_Abstract $rowset)
    {
        $this->data = $rowset->toArray();
    }
}