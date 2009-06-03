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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: TestCommon.php 15668 2009-05-21 22:04:13Z ralph $
 */


/**
 * @see Zend_Db_Table_TestSetup
 */
require_once 'Zend/Db/TestSuite/AbstractTestCase.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

require_once 'Zend/Db/Table/Row.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Row_AbstractTestCase extends Zend_Db_TestSuite_AbstractTestCase
{

    protected function _testTableRow()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $bug_id          = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status      = $this->sharedFixture->dbAdapter->foldCase('bug_status');
        $created_on      = $this->sharedFixture->dbAdapter->foldCase('created_on');
        $updated_on      = $this->sharedFixture->dbAdapter->foldCase('updated_on');
        $reported_by     = $this->sharedFixture->dbAdapter->foldCase('reported_by');
        $assigned_to     = $this->sharedFixture->dbAdapter->foldCase('assigned_to');
        $verified_by     = $this->sharedFixture->dbAdapter->foldCase('verified_by');

        $data = array(
            $bug_id          => '1',
            $bug_description => 'System needs electricity to run',
            $bug_status      => 'NEW',
            $created_on      => '2007-04-01',
            $updated_on      => '2007-04-01',
            $reported_by     => 'goofy',
            $assigned_to     => 'mmouse',
            $verified_by     => 'dduck'
        );

        $config = array(
            'table'  => $table,
            'stored' => true,
            'readOnly' => false,
            'data'   => $data,
        );

        Zend_Loader::loadClass('My_ZendDbTable_Row_TestTableRow');
        return new My_ZendDbTable_Row_TestTableRow($config);
    }

    public function testTableFindRow()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(1, count($rowset));
    }

    public function testTableRowConstructor()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $config = array(
                'db'    => $this->sharedFixture->dbAdapter,
                'table' => $table,
                'data'  => array(),
                'readOnly' => true
            );

        $row1 = new Zend_Db_Table_Row($config);

        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $bug_description = $row1->bug_description;
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"bug_description\" is not in the row", $e->getMessage());
        }

        $config['data'] = 'Invalid';

        try {
            $row1 = new Zend_Db_Table_Row($config);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Data must be an array", $e->getMessage());
        }
    }

    /**
     * @group ZF-1144
     */ 
    public function testTableRowContructorWithTableNameSpecifiedInSubclass()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        /**
         * @see Zend_Db_Table_Row_TestStandaloneRow
         */
        require_once 'My/ZendDbTable/Row/TestStandaloneRow.php';

        Zend_Db_Table_Abstract::setDefaultAdapter($this->sharedFixture->dbAdapter);

        $row = new My_ZendDbTable_Row_TestStandaloneRow();
        $this->assertType('Zend_Db_Table_Abstract', $row->getTable());

        Zend_Db_Table_Abstract::setDefaultAdapter();
    }

    public function testTableRowReadOnly()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $row1 = new Zend_Db_Table_Row(
            array(
                'db'    => $this->sharedFixture->dbAdapter,
                'table' => $table,
                'readOnly' => true
            )
        );

        $this->assertTrue($row1->isReadOnly());
    }

    public function testTableRowToArray()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $a = $row1->toArray();

        $this->assertTrue(is_array($a));

        // fix for #ZF-1898
        $arrayObject = new ArrayObject($row1->toArray(),ArrayObject::ARRAY_AS_PROPS);
        $arrayObject->bug_status = 'foobar';
        $this->assertNotEquals('foobar',$row1->bug_status);

        $cols = array(
            'bug_id',
            'bug_description',
            'bug_status',
            'created_on',
            'updated_on',
            'reported_by',
            'assigned_to',
            'verified_by',
        );
        $this->assertEquals($cols, array_keys($a));
    }

    public function testTableRowSelect()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $select = $row1->select();
        $this->assertType('Zend_Db_Table_Select', $select,
            'Expecting object of type Zend_Db_Table_Select, got '.get_class($select));
    }

    public function testTableRowMagicGet()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $this->assertEquals(1, $row1->$bug_id);
            $this->assertEquals('System needs electricity to run', $row1->$bug_description);
            $this->assertEquals('NEW', $row1->$bug_status);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }

        if (!isset($row1->$bug_id)) {
            $this->fail('Column "id" is set but isset() returns false');
        }
    }
    
    public function testTableRowMagicUnset()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $row   = $table->find(1)->current();
        
        unset($row->assigned_to);
        $this->assertFalse(isset($row->assigned_to));
        $diff = array_diff_key(array('assigned_to'=>''), $row->toArray());
        $this->assertEquals(array('assigned_to'),array_keys($diff));
    }
    
    public function testTableRowMagicUnsetWhenUnsettingPkValueThrowsException()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $row   = $table->find(1)->current();
        $this->setExpectedException('Zend_Db_Table_Row_Exception');
        unset($row->bug_id);
    }

    public function testTableRowMagicSet()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $row1->$bug_description = 'foo';
            $this->assertEquals('foo', $row1->$bug_description);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    // ZF-2013
	public function testTableRowOffsetGet()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $this->assertEquals(1, $row1->offsetGet($bug_id));
            $this->assertEquals('System needs electricity to run', $row1->offsetGet($bug_description));
            $this->assertEquals('NEW', $row1->offsetGet($bug_status));
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    // ZF-2013
    public function testTableRowOffsetSet()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $row1->offsetSet($bug_description, 'foo');
            $this->assertEquals('foo', $row1->offsetGet($bug_description));
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSetFromArray()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        $data = array(
            $bug_description => 'New Description',
            $bug_status      => 'INVALID'
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
            $this->assertEquals($data[$bug_description], $row1->$bug_description);
            $this->assertEquals($data[$bug_status], $row1->$bug_status);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    /**
     * ZF-2243: Zend_Db_Table::createRow and Zend_Db_Table_Row::setFromArray have same behaviour
     */
    public function testTableRowSetFromBigArray()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        // Data issued from form object
        $data = array(
            $bug_description => 'New Description',
            $bug_status      => 'INVALID',
            'btnAccept'      => 1           // Button value
        );

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $row1->setFromArray($data);

        try {
            $button = $row1->btnAccept;
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"btnAccept\" is not in the row", $e->getMessage());
        }
    }

    public function testTableRowSaveInsert()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );
        try {
            $row3 = $table->createRow($data);
            $this->assertNull($row3->bug_id);
            $row3->save();
            $this->assertEquals(5, $row3->bug_id);
            $this->assertEquals($data['bug_description'], $row3->bug_description);
            $this->assertEquals($data['bug_status'], $row3->bug_status);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveInsertSequence()
    {
        $table = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableProducts',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zf_products_seq'));
        $product_id   = $this->sharedFixture->dbAdapter->foldCase('product_id');
        $product_name = $this->sharedFixture->dbAdapter->foldCase('product_name');

        $data = array (
            $product_name => 'Solaris'
        );
        $row3 = $table->createRow($data);
        $row3->save();
        try {
            $this->assertEquals(4, $row3->$product_id);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveUpdate()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id          = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status      = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        $data = array(
            $bug_description => 'New Description',
            $bug_status      => 'INVALID'
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
            $this->assertEquals(1, $row1->$bug_id);
            $this->assertEquals($data[$bug_description], $row1->$bug_description);
            $this->assertEquals($data[$bug_status], $row1->$bug_status);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveInvalidTable()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $row = $this->_testTableRow();

        try {
            $row->setTableColsToFail($table);
            $row->setTable($table);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for incorrect parent table');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('The specified Table does not have the same columns as the Row', $e->getMessage());
        }

        $row = $this->_testTableRow();

        try {
            $row->setPrimaryKeyToFail1($table);
            $row->setTable($table);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for incorrect parent table');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('The specified Table \'My_ZendDbTable_TableBugs\' does not have the same primary key as the Row', $e->getMessage());
        }
    }

    public function testTableRowSaveUpdateInvalidInfo()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $row = $this->_testTableRow();

        $bug_status      = $this->sharedFixture->dbAdapter->foldCase('bug_status');
        $row->$bug_status = 'VALID';

        try {
            $row->setTable($table);
            $row->setPrimaryKeyToFail1();
            $row->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for incorrect parent table');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('The primary key must be set as an array', $e->getMessage());
        }

        try {
            $row->setTable($table);
            $row->setPrimaryKeyToFail2();
            $row->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for incorrect parent table');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('The specified Table \'My_ZendDbTable_TableBugs\' does not have the same primary key as the Row', $e->getMessage());
        }
    }

    public function testTableRowSaveUpdateRefresh()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $bug_status = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        $row = $this->_testTableRow();
        $row->$bug_status = 'VALID';

        try {
            $row->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for missing parent');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('Cannot refresh row as parent is missing', $e->getMessage());
        }
    }

    public function testTableRowSetTable()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $table2 = $this->sharedFixture->tableUtility->getTableById('Products');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $row1->setTable(null);
        $this->assertFalse($row1->isConnected());

        try {
            $row1->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Exception for incorrect parent table');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('The specified Table is of class My_ZendDbTable_TableProducts, expecting class to be instance of My_ZendDbTable_TableBugs', $e->getMessage());
        }
    }

    public function testTableRowSetInvalidTable()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $row = $this->_testTableRow();

        try {
            $row->setTableToFail();
            $row->setTable($table);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for incorrect parent table');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('The specified Table is of class My_ZendDbTable_TableBugs, expecting class to be instance of foo', $e->getMessage());
        }
    }

    public function testTableRowExceptionGetColumnNotInRow()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $column = 'doesNotExist';

        try {
            $dummy = $row1->$column;
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"$column\" is not in the row", $e->getMessage());
        }
    }

    public function testTableRowExceptionSetColumnNotInRow()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        $column = 'doesNotExist';

        try {
            $row1->$column = 'dummy value';
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"$column\" is not in the row", $e->getMessage());
        }
    }

    public function testTableRowExceptionBogusPrimaryKey()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('BugsProducts');
        $bogusData = array(
            'bug_id' => 3,
            'foo'    => 'bar'
        );
        $row = new Zend_Db_Table_Row(array('table' => $table, 'data' => $bogusData));
        try {
            $rowsAffected = $row->delete();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("The specified Table 'My_ZendDbTable_TableBugsProducts' does not have the same primary key as the Row", $e->getMessage());
        }
    }

    public function testTableRowSetPrimaryKey()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');

        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));

        try {
            $row1->$bug_id = 6;
            $row1->save();
            $this->assertEquals(6, $row1->$bug_id);
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSerialize()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1New,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1New));
        $this->assertEquals($row1->toArray(), $row1New->toArray());
    }

    public function testTableRowSerializeExceptionNotConnected()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1New,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1New));
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $row1New->$bug_description = 'New description';

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
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

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

        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');
        $bug_status      = $this->sharedFixture->dbAdapter->foldCase('bug_status');
        $data = array(
            $bug_description => 'New Description',
            $bug_status      => 'INVALID'
        );
        $row1New->setFromArray($data);

        try {
            $rowsAffected = $row1New->save();
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertEquals(1, $rowsAffected);
    }

    public function testTableRowSerializeReconnectedDelete()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

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

        try {
            $rowsAffected = $row1New->delete();
        } catch (Zend_Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertEquals(1, $rowsAffected);
    }

    public function testTableRowSerializeExceptionWrongTable()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1New,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1New));

        $table2 = $this->sharedFixture->tableUtility->getTableById('Products');
        $connected = false;
        try {
            $connected = $row1New->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('The specified Table is of class My_ZendDbTable_TableProducts, expecting class to be instance of My_ZendDbTable_TableBugs', $e->getMessage());
        }
        $this->assertFalse($connected);
    }

    public function testTableRowSetReadOnly()
    {
        $table = $this->_testTableRowSetReadOnlyGetTableBugs();
        $bug_status = $this->sharedFixture->dbAdapter->foldCase('bug_status');

        $rowset = $table->find(1);
        $row1 = $rowset->current();

        $row1->setReadOnly(true);
        $this->assertTrue($row1->isReadOnly());

        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );

        $row2 = $table->createRow($data);
        $row2->setReadOnly(true);
        try {
            $row2->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('This row has been marked read-only', $e->getMessage());
        }

        $row2->setReadOnly(false);
        $row2->save();

        $row2->$bug_status = 'VALID';
        $row2->setReadOnly(true);

        try {
            $row2->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('This row has been marked read-only', $e->getMessage());
        }

        $row2->setReadOnly(false);
        $row2->save();
    }

    public function testTableRowInvalidTransformColumn()
    {
        $row = $this->_testTableRow();

        try {
            $row->setInvalidColumn();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for invalid column type');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('Specified column is not a string', $e->getMessage());
        }
    }
    
    
    
    /**
     * Utility methods below
     */

    
    
    
    /**
     * Allow adapters with sequences to declare them
     * @return Zend_Db_Table_Abstract
     */
    protected function _testTableRowSetReadOnlyGetTableBugs()
    {
        return $this->sharedFixture->tableUtility->getTableById('Bugs');
    }
    
}
