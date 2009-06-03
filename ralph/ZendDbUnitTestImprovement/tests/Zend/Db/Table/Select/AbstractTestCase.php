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
 * @see Zend_Db_Select_TestCommon
 */
require_once 'Zend/Db/Select/AbstractTestCase.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Select_AbstractTestCase extends Zend_Db_Select_AbstractTestCase
{
    
    
    /**
     * _getTableById()
     *
     * @param string $tableId
     * @return Zend_Db_Table_Abstract
     */
    protected function _getTableById($tableId)
    {
        return $this->sharedFixture->tableUtility->getTableById($tableId);
    }

    /**
     * Test adding a FOR UPDATE clause to a Zend_Db_Select object.
     */
    protected function _selectForReadOnly($fields)
    {
        $table = $this->_getTableById('Products');

        $select = $table->select()
            ->from($table, $fields);
        return $select;
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     *
     */
    public function testSelectForReadOnly()
    {
        $select = $this->_selectForReadOnly(array('count' => 'COUNT(*)'));
        $this->assertTrue($select->isReadOnly());

        $select = $this->_selectForReadOnly(array());
        $this->assertFalse($select->isReadOnly());

        $select = $this->_selectForReadOnly(array('*'));
        $this->assertFalse($select->isReadOnly());
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */
    protected function _selectForJoinZendDbTable()
    {
        $table = $this->_getTableById('Products');

        $select = $table->select()
            ->join(array('p' => 'zf_bugs_products'), 'p.product_id = zfproduct.id', 'p.bug_id');
        return $select;
    }

    /**
     * Test adding a join to the select object without setting integrity check to false.
     *
     */
    public function testSelectForJoinZendDbTable()
    {
        $select = $this->_selectForJoinZendDbTable();

        try {
            $query = $select->assemble();
            $this->fail('Expected to catch Zend_Db_Table_Select_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Select_Exception', $e);
            $this->assertEquals('Select query cannot join with another table', $e->getMessage());
        }
    }

    /**
     * Test adding a FOR UPDATE clause to a Zend_Db_Select object.
     */
    protected function _selectForToString1($tableName = null, $fields = array('*'), $useTable = true)
    {
        $table = $this->_getTableById($tableName);

        $select = $table->select();

        if ($useTable) {
            $select->from($table, $fields);
        }

        return $select;
    }

    /**
     * Test adding a FOR UPDATE clause to a Zend_Db_Select object.
     */
    protected function _selectForToString2($tableName, $fields = array('*'))
    {
        $select = $this->sharedFixture->dbAdapter->select()
            ->from($tableName, $fields);
        return $select;
    }

    /**
     * Test string conversion to ensure Zend_Db_Table_Select is identical
     * to that of Zend_Db_Select.
     *
     */
    public function testSelectForToString()
    {
        // Test for all fields and no default table name on select
        $select1 = $this->_selectForToString1('Products', null, false);
        $select2 = $this->_selectForToString2('zf_products');
        $this->assertEquals($select1->assemble(), $select2->assemble());

        // Test for all fields by default
        $select1 = $this->_selectForToString1('Products');
        $select2 = $this->_selectForToString2('zf_products');
        $this->assertEquals($select1->assemble(), $select2->assemble());

        // Test for selected fields
        $select1 = $this->_selectForToString1('Products', array('product_id', 'DISTINCT(product_name)'));
        $select2 = $this->_selectForToString2('zf_products', array('product_id', 'DISTINCT(product_name)'));
        $this->assertEquals($select1->assemble(), $select2->assemble());
    }

    /**
     * Test to see if a Zend_Db_Table_Select object returns the table it's been
     * instantiated from.
     *
     */
    public function testDbSelectHasTableInstance()
    {
        $table = $this->_getTableById('Products');
        $select = $table->select();

        $this->assertType('My_ZendDbTable_TableProducts', $select->getTable());
    }
    
    /**
     * @group ZF-2798
     */
    public function testTableWillReturnSelectObjectWithFromPart()
    {
        $table = $this->_getTableById('Accounts');
        $select1 = $table->select();
        $this->assertEquals(0, count($select1->getPart(Zend_Db_Table_Select::FROM)));
        $this->assertEquals(0, count($select1->getPart(Zend_Db_Table_Select::COLUMNS)));
        
        $select2 = $table->select(true);
        $this->assertEquals(1, count($select2->getPart(Zend_Db_Table_Select::FROM)));
        $this->assertEquals(1, count($select2->getPart(Zend_Db_Table_Select::COLUMNS)));
        
        $this->assertEquals($select1->__toString(), $select2->__toString());
        
        $select3 = $table->select();
        $select3->setIntegrityCheck(false);
        $select3->joinLeft('tableB', 'tableA.id=tableB.id');
        $select3Text = $select3->__toString();
        $this->assertNotContains('zf_accounts', $select3Text);
        
        $select4 = $table->select(Zend_Db_Table_Abstract::SELECT_WITH_FROM_PART);
        $select4->setIntegrityCheck(false);
        $select4->joinLeft('tableB', 'tableA.id=tableB.id');
        $select4Text = $select4->__toString();
        $this->assertContains('zf_accounts', $select4Text);
        $this->assertContains('tableA', $select4Text);
        $this->assertContains('tableB', $select4Text);
    }

    // ZF-3239
//    public function testFromPartIsAvailableRightAfterInstantiation()
//    {
//        $table = $this->_getTableById('Products');
//        $select = $table->select();
//
//        $keys = array_keys($select->getPart(Zend_Db_Select::FROM));
//
//        $this->assertEquals('zf_products', array_pop($keys));
//    }

    // ZF-3239 (from comments)
//    public function testColumnsMethodDoesntThrowExceptionRightAfterInstantiation()
//    {
//        $table = $this->_getTableById('Products');
//
//        try {
//            $select = $table->select()->columns('product_id');
//
//            $this->assertType('Zend_Db_Table_Select', $select);
//        } catch (Zend_Db_Table_Select_Exception $e) {
//            $this->fail('Exception thrown: ' . $e->getMessage());
//        }
//    }

    // ZF-5424
//    public function testColumnsPartDoesntContainWildcardAfterSettingColumns()
//    {
//        $table = $this->_getTableById('Products');
//
//        $select = $table->select()->columns('product_id');
//
//        $columns = $select->getPart(Zend_Db_Select::COLUMNS);
//
//        $this->assertEquals(1, count($columns));
//        $this->assertEquals('product_id', $columns[0][1]);
//    }
}