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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Db/Table/Row/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Table_Row_Db2Test extends Zend_Db_Table_Row_TestCommon
{

    public function testTableRowSaveInsert()
    {
        $this->markTestIncomplete($this->getDriver() . ' having troubles with auto-increment insert');
    }

    public function testTableRowSaveInsertSequence()
    {
        $table = $this->_getTable('Zend_Db_Table_TableProducts',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'products_seq'));

        $data = array (
            'product_name' => 'Solaris'
        );
        $row3 = $table->createRow($data);
        $row3->save();
        try {
            $this->assertEquals(4, $row3->product_id);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function getDriver()
    {
        return 'Db2';
    }

}
