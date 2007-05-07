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

class Zend_Db_Table_Row_OracleTest extends Zend_Db_Table_Row_TestCommon
{

    public function testTableRowSaveInsert()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support auto-increment keys.');
    }

    public function getDriver()
    {
        return 'Oracle';
    }

    public function testTableRowSetFromArray()
    {
        $table = $this->_table['bugs'];

        $data = array(
            'BUG_DESCRIPTION' => 'New Description',
            'BUG_STATUS'      => 'INVALID'
        );

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $result = $row1->setFromArray($data);

        $this->assertSame($result, $row1);

        try {
            $this->assertEquals($data['BUG_DESCRIPTION'], $row1->BUG_DESCRIPTION);
            $this->assertEquals($data['BUG_STATUS'], $row1->BUG_STATUS);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveInsertSequence()
    {
        $table = $this->_getTable('Zend_Db_Table_TableProducts',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zfproducts_seq'));
        $data = array (
            'PRODUCT_NAME' => 'Solaris'
        );
        $row3 = $table->createRow($data);
        $row3->save();
        try {
            $this->assertEquals(4, $row3->PRODUCT_ID);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveUpdate()
    {
        $table = $this->_table['bugs'];

        $data = array(
            'BUG_DESCRIPTION' => 'New Description',
            'BUG_STATUS'      => 'INVALID'
        );

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $row1->setFromArray($data);
        $row1->save();

        try {
            $this->assertEquals(1, $row1->BUG_ID);
            $this->assertequals($data['BUG_DESCRIPTION'], $row1->BUG_DESCRIPTION);
            $this->assertEquals($data['BUG_STATUS'], $row1->BUG_STATUS);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSerializeExceptionNotConnected()
    {
        $table = $this->_table['bugs'];

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1New,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1New));
        $row1New->BUG_DESCRIPTION = 'New description';

        try {
            $row1New->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Cannot save a Row unless it is connected", $e->getMessage());
        }
    }

    public function testTableRowSerializeReconnectedUpdate()
    {
        $table = $this->_table['bugs'];

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1New,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1New));

        try {
            $connected = $row1New->setTable($table);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertTrue($connected);

        $data = array(
            'BUG_DESCRIPTION' => 'New Description',
            'BUG_STATUS'      => 'INVALID'
        );
        $row1New->setFromArray($data);

        try {
            $rowsAffected = $row1New->save();
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertEquals(1, $rowsAffected);
    }

    public function testTableRowToArray()
    {
        $table = $this->_table['bugs'];

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $a = $row1->toArray();

        $this->assertTrue(is_array($a));
        $cols = array(
            'BUG_ID',
            'BUG_DESCRIPTION',
            'BUG_STATUS',
            'CREATED_ON',
            'UPDATED_ON',
            'REPORTED_BY',
            'ASSIGNED_TO',
            'VERIFIED_BY',
        );
        $this->assertEquals($cols, array_keys($a));
    }

    public function testTableRowMagicGet()
    {
        $table = $this->_table['bugs'];

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $this->assertEquals(1, $row1->BUG_ID);
            $this->assertEquals('System needs electricity to run', $row1->BUG_DESCRIPTION);
            $this->assertEquals('NEW', $row1->BUG_STATUS);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }

        if (!isset($row1->BUG_ID)) {
            $this->fail('Column "id" is set but isset() returns false');
        }
    }

    public function testTableRowMagicSet()
    {
        $table = $this->_table['bugs'];

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $row1->BUG_DESCRIPTION = 'foo';
            $this->assertEquals('foo', $row1->BUG_DESCRIPTION);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSetPrimaryKey()
    {
        $table = $this->_table['bugs'];

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $row1->BUG_ID = 6;
            $row1->save();
            $this->assertEquals(6, $row1->BUG_ID);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

}
