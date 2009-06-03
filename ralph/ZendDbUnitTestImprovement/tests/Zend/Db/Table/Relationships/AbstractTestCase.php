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

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Relationships_AbstractTestCase extends Zend_Db_TestSuite_AbstractTestCase
{

    public function testTableRelationshipFindParentRow()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);
        $account_name = $this->sharedFixture->dbAdapter->foldCase('account_name');

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('My_ZendDbTable_TableAccounts');
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipFindParentRowSelect()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);
        $account_name = $this->sharedFixture->dbAdapter->foldCase('account_name');
        $account_name_column = $this->sharedFixture->dbAdapter->quoteIdentifier('account_name', true);

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $select = $table->select()->where($account_name_column . ' = ?', 'goofy');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('My_ZendDbTable_TableAccounts', null, $select);
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicFindParentRow()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);
        $account_name = $this->sharedFixture->dbAdapter->foldCase('account_name');

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentMy_ZendDbTable_TableAccounts();
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicFindParentRowSelect()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);
        $account_name = $this->sharedFixture->dbAdapter->foldCase('account_name');
        $account_name_column = $this->sharedFixture->dbAdapter->quoteIdentifier('account_name', true);

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $select = $table->select()->where($account_name_column . ' = ?', 'goofy');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentMy_ZendDbTable_TableAccounts($select);
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);
    }

    public function testTableRelationshipMagicException()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        // Completely bogus method
        try {
            $result = $parentRow1->nonExistantMethod();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals("Unrecognized method 'nonExistantMethod()'", $e->getMessage());
        }
    }

    public function testTableRelationshipFindParentRowException()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $childRows = $table->fetchAll("$bug_id = 1");
        $childRow1 = $childRows->current();

        try {
            $parentRow = $childRow1->findParentRow('nonexistant_class');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('File "nonexistant' . DIRECTORY_SEPARATOR . 'class.php" does not exist or class "nonexistant_class" was not found in the file', $e->getMessage());
        }

        try {
            $parentRow = $childRow1->findParentRow(new stdClass());
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for wrong table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('Parent table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }
    }

    public function testTableRelationshipFindManyToManyRowset()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());
    }

    public function testTableRelationshipFindManyToManyRowsetSelect()
    {
        $product_name = $this->sharedFixture->dbAdapter->foldCase('product_name');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_id_column = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $select = $table->select()->where($bug_id_column . ' = ?', 1)
                                  ->limit(2)
                                  ->order($product_name . ' ASC');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts',
                                                      null, null, $select);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(2, $destRows->count());

        $childRow = $destRows->current();
        $this->assertEquals('Linux', $childRow->$product_name);
    }

    public function testTableRelationshipMagicFindManyToManyRowset()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findMy_ZendDbTable_TableProductsViaMy_ZendDbTable_TableBugsProducts();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());
    }

    public function testTableRelationshipMagicFindManyToManyRowsetSelect()
    {
        $product_name = $this->sharedFixture->dbAdapter->foldCase('product_name');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_id_column = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $select = $table->select()->where($bug_id_column . ' = ?', 1)
                                  ->limit(2)
                                  ->order($product_name . ' ASC');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findMy_ZendDbTable_TableProductsViaMy_ZendDbTable_TableBugsProducts($select);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(2, $destRows->count());

        $childRow = $destRows->current();
        $this->assertEquals('Linux', $childRow->$product_name);
    }

    public function testTableRelationshipFindManyToManyRowsetException()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        // Use nonexistant class for destination table
        try {
            $destRows = $originRow1->findManyToManyRowset('nonexistant_class', 'My_ZendDbTable_TableBugsProducts');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('File "nonexistant' . DIRECTORY_SEPARATOR . 'class.php" does not exist or class "nonexistant_class" was not found in the file', $e->getMessage());
        }

        // Use stdClass instead of table class for destination table
        try {
            $destRows = $originRow1->findManyToManyRowset(new stdClass(), 'My_ZendDbTable_TableBugsProducts');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('Match table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }

        // Use nonexistant class for intersection table
        try {
            $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'nonexistant_class');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('File "nonexistant' . DIRECTORY_SEPARATOR . 'class.php" does not exist or class "nonexistant_class" was not found in the file', $e->getMessage());
        }

        // Use stdClass instead of table class for intersection table
        try {
            $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', new stdClass());
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('Intersection table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }

    }

    public function testTableRelationshipFindDependentRowset()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');

        $parentRows = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $parentRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('My_ZendDbTable_TableBugsProducts');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(3, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(1, $childRow1->$product_id);
    }

    public function testTableRelationshipFindDependentRowsetSelect()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');

        $select = $table->select()->limit(2)
                                  ->order($product_id . ' DESC');

        $parentRows = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $parentRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('My_ZendDbTable_TableBugsProducts', null, $select);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(3, $childRow1->$product_id);
    }

    public function testTableRelationshipMagicFindDependentRowset()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findMy_ZendDbTable_TableBugsProducts();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(3, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(1, $childRow1->$product_id);
    }

    public function testTableRelationshipMagicFindDependentRowsetSelect()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');
        $select = $table->select()->limit(2)
                                  ->order($product_id . ' DESC');

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findMy_ZendDbTable_TableBugsProducts($select);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(2, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $this->assertEquals(1, $childRow1->$bug_id);
        $this->assertEquals(3, $childRow1->$product_id);
    }

    public function testTableRelationshipFindDependentRowsetException()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $parentRows = $table->find(1);
        $parentRow1 = $parentRows->current();

        try {
            $childRows = $parentRow1->findDependentRowset('nonexistant_class');
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for nonexistent table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('File "nonexistant' . DIRECTORY_SEPARATOR . 'class.php" does not exist or class "nonexistant_class" was not found in the file', $e->getMessage());
        }

        try {
            $childRows = $parentRow1->findDependentRowset(new stdClass());
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception for wrong table class');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception got '.get_class($e));
            $this->assertEquals('Dependent table must be a Zend_Db_Table_Abstract, but it is stdClass', $e->getMessage());
        }
    }

    /**
     * Ensures that basic cascading update functionality succeeds using strings for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageBasicString()
    {
        $bug = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsCustom')
                ->find(1)
                ->current();
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        $bug->$bug_id = 333;

        $bug->save();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        foreach ($bugProducts as $bugProduct) {
            $this->assertEquals(333, $bugProduct->$bug_id);
        }

        $bug->$bug_id = 1;

        $bug->save();

        $this->assertEquals(
            3,
            count($bugProducts = $bug->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        foreach ($bugProducts as $bugProduct) {
            $this->assertEquals(1, $bugProduct->$bug_id);
        }
    }

    /**
     * Ensures that basic cascading update functionality succeeds using arrays for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageBasicArray()
    {
        $account1 = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableAccountsCustom')
                    ->find('mmouse')
                    ->current();
        $account_name = $this->sharedFixture->dbAdapter->foldCase('account_name');
        $reported_by = $this->sharedFixture->dbAdapter->foldCase('reported_by');

        $this->assertEquals(
            1,
            count($account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        $account1->$account_name = 'daisy';

        $account1->save();

        $this->assertEquals(
            1,
            count($account1Bugs = $account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($account1Bugs as $account1Bug) {
            $this->assertEquals('daisy', $account1Bug->$reported_by);
        }

        $account1->$account_name = 'mmouse';

        $account1->save();

        $this->assertEquals(
            1,
            count($account1Bugs = $account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($account1Bugs as $account1Bug) {
            $this->assertEquals('mmouse', $account1Bug->$reported_by);
        }
    }

    /**
     * Ensures that cascading update functionality is not run when onUpdate != self::CASCADE
     *
     * @return void
     */
    public function testTableRelationshipCascadingUpdateUsageInvalidNoop()
    {
        $product1 = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableProductsCustom')
                    ->find(1)
                    ->current();

        $this->assertEquals(
            1,
            count($product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');
        $product1->$product_id = 333;

        $product1->save();

        $this->assertEquals(
            0,
            count($product1BugsProducts = $product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->$product_id = 1;

        $product1->save();

        $this->assertEquals(
            1,
            count($product1BugsProducts = $product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        foreach ($product1BugsProducts as $product1BugsProduct) {
            $this->assertEquals(1, $product1BugsProduct->$product_id);
        }
    }

    /**
     * Ensures that basic cascading delete functionality succeeds using strings for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageBasicString()
    {
        $bug1 = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsCustom')
                ->find(1)
                ->current();

        $this->assertEquals(
            3,
            count($bug1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find three dependent rows'
            );

        $bug1->delete();

        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);

        $this->assertEquals(
            0,
            count($this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsProductsCustom')->fetchAll("$bug_id = 1")),
            'Expecting cascading delete to have reduced dependent rows to zero'
            );
    }

    /**
     * Ensures that basic cascading delete functionality succeeds using arrays for single columns
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageBasicArray()
    {
        $reported_by = $this->sharedFixture->dbAdapter->quoteIdentifier('reported_by', true);

        $account1 = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableAccountsCustom')
                    ->find('mmouse')
                    ->current();

        $this->assertEquals(
            1,
            count($account1->findDependentRowset('My_ZendDbTable_TableBugsCustom')),
            'Expecting to find one dependent row'
            );

        $account1->delete();

        $tableBugsCustom = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsCustom');

        $this->assertEquals(
            0,
            count(
                $tableBugsCustom->fetchAll(
                    $tableBugsCustom->getAdapter()
                                    ->quoteInto("$reported_by = ?", 'mmouse')
                    )
                ),
            'Expecting cascading delete to have reduced dependent rows to zero'
            );
    }

    /**
     * Ensures that cascading delete functionality is not run when onDelete != self::CASCADE
     *
     * @return void
     */
    public function testTableRelationshipCascadingDeleteUsageInvalidNoop()
    {
        $product1 = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableProductsCustom')
                    ->find(1)
                    ->current();

        $this->assertEquals(
            1,
            count($product1->findDependentRowset('My_ZendDbTable_TableBugsProductsCustom')),
            'Expecting to find one dependent row'
            );

        $product1->delete();

        $product_id = $this->sharedFixture->dbAdapter->quoteIdentifier('product_id', true);

        $this->assertEquals(
            1,
            count($this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsProductsCustom')->fetchAll("$product_id = 1")),
            'Expecting to find one dependent row'
            );
    }

    public function testTableRelationshipGetReference()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $map = $table->getReference('My_ZendDbTable_TableAccounts', 'Reporter');

        $this->assertThat($map, $this->arrayHasKey('columns'));
        $this->assertThat($map, $this->arrayHasKey('refTableClass'));
        $this->assertThat($map, $this->arrayHasKey('refColumns'));
    }

    public function testTableRelationshipGetReferenceException()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        try {
            $table->getReference('My_ZendDbTable_TableAccounts', 'Nonexistent');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent reference rule');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }

        try {
            $table->getReference('Nonexistent', 'Reporter');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent rule tableClass');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }

        try {
            $table->getReference('Nonexistent');
            $this->fail('Expected to catch Zend_Db_Table_Exception for nonexistent rule tableClass');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
        }
    }

    /**
     * Ensures that findParentRow() returns an instance of a custom row class when passed an instance
     * of the table class having $_rowClass overridden.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowCustomInstance()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        $myRowClass = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowClass);

        $accounts = $this->sharedFixture->tableUtility->getTableById('Accounts');
        
        $bug1Reporter = $this->sharedFixture->tableUtility->getTableById('Bugs')
                        ->find(1)
                        ->current()
                        ->findParentRow($accounts->setRowClass($myRowClass));

        $this->assertType($myRowClass, $bug1Reporter,
            "Expecting object of type $myRowClass, got ".get_class($bug1Reporter));
    }

    /**
     * Ensures that findParentRow() returns an instance of a custom row class when passed a string class
     * name, where the class has $_rowClass overridden.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowCustomClass()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        $myRowClass = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowClass);

        Zend_Loader::loadClass('My_ZendDbTable_TableAccountsCustom');

        $bug1Reporter = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsCustom')
                        ->find(1)
                        ->current()
                        ->findParentRow(new My_ZendDbTable_TableAccountsCustom(array('db' => $this->sharedFixture->dbAdapter)));

        $this->assertType($myRowClass, $bug1Reporter,
            "Expecting object of type $myRowClass, got ".get_class($bug1Reporter));
    }

    /**
     * Ensures that findDependentRowset() returns instances of custom row and rowset classes when
     * passed an instance of the table class.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetCustomInstance()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $account_name = $this->sharedFixture->dbAdapter->quoteIdentifier('account_name', true);

        $accounts = $this->sharedFixture->tableUtility->getTableById('Accounts');
        
        $bugs = $accounts
                ->fetchRow($this->sharedFixture->dbAdapter->quoteInto("$account_name = ?", 'mmouse'))
                ->findDependentRowset(
                    $this->sharedFixture->tableUtility->getTableById('Bugs')
                        ->setRowsetClass($myRowsetClass)
                        ->setRowClass($myRowClass),
                    'Engineer'
                    );

        $this->assertType($myRowsetClass, $bugs,
            "Expecting object of type $myRowsetClass, got ".get_class($bugs));

        $this->assertEquals(3, count($bugs));

        foreach ($bugs as $bug) {
            $this->assertType($myRowClass, $bug,
                "Expecting object of type $myRowClass, got ".get_class($bug));
        }
    }

    /**
     * Ensures that findDependentRowset() returns instances of custom row and rowset classes when
     * passed the named class.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetCustomClass()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $account_name = $this->sharedFixture->dbAdapter->quoteIdentifier('account_name', true);

        $bugs = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableAccountsCustom')
                ->fetchRow($this->sharedFixture->dbAdapter->quoteInto("$account_name = ?", 'mmouse'))
                ->findDependentRowset('My_ZendDbTable_TableBugsCustom', 'Engineer');

        $this->assertType($myRowsetClass, $bugs,
            "Expecting object of type $myRowsetClass, got ".get_class($bugs));

        $this->assertEquals(3, count($bugs));

        foreach ($bugs as $bug) {
            $this->assertType($myRowClass, $bug,
                "Expecting object of type $myRowClass, got ".get_class($bug));
        }
    }

    /**
     * Ensures that findManyToManyRowset() returns instances of custom row and rowset class when
     * passed an instance of the table class.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetCustomInstance()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $bug1Products = $this->sharedFixture->tableUtility->getTableById('Bugs')
                        ->find(1)
                        ->current()
                        ->findManyToManyRowset(
                            $this->sharedFixture->tableUtility->getTableById('Products')
                                ->setRowsetClass($myRowsetClass)
                                ->setRowClass($myRowClass),
                            'My_ZendDbTable_TableBugsProducts'
                            );

        $this->assertType($myRowsetClass, $bug1Products,
            "Expecting object of type $myRowsetClass, got ".get_class($bug1Products));

        $this->assertEquals(3, count($bug1Products));

        foreach ($bug1Products as $bug1Product) {
            $this->assertType($myRowClass, $bug1Product,
                "Expecting object of type $myRowClass, got ".get_class($bug1Product));
        }
    }

    /**
     * Ensures that findManyToManyRowset() returns instances of custom row and rowset classes when
     * passed the named class.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetCustomClass()
    {
        $this->sharedFixture->tableUtility->useMyIncludePath();
        
        $myRowsetClass = 'My_ZendDbTable_Rowset_TestMyRowset';
        $myRowClass    = 'My_ZendDbTable_Row_TestMyRow';

        Zend_Loader::loadClass($myRowsetClass);

        $bug1Products = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugsCustom')
                        ->find(1)
                        ->current()
                        ->findManyToManyRowset(
                            'My_ZendDbTable_TableProductsCustom',
                            'My_ZendDbTable_TableBugsProductsCustom'
                            );

        $this->assertType($myRowsetClass, $bug1Products,
            "Expecting object of type $myRowsetClass, got ".get_class($bug1Products));

        $this->assertEquals(3, count($bug1Products));

        foreach ($bug1Products as $bug1Product) {
            $this->assertType($myRowClass, $bug1Product,
                "Expecting object of type $myRowClass, got ".get_class($bug1Product));
        }
    }

    /**
     * Ensures that rows returned by findParentRow() are updatable.
     *
     * @return void
     */
    public function testTableRelationshipFindParentRowIsUpdateable()
    {
        $bug_id = $this->sharedFixture->dbAdapter->quoteIdentifier('bug_id', true);
        $account_name = $this->sharedFixture->dbAdapter->foldCase('account_name');

        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');

        $childRows = $table->fetchAll("$bug_id = 1");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $parentRow = $childRow1->findParentRow('My_ZendDbTable_TableAccounts');
        $this->assertType('Zend_Db_Table_Row_Abstract', $parentRow,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($parentRow));

        $this->assertEquals('goofy', $parentRow->$account_name);

        $parentRow->$account_name = 'clarabell';
        try {
            $parentRow->save();
        } catch (Zend_Exception $e) {
            $this->fail('Failed with unexpected '.get_class($e).': '.$e->getMessage());
        }

        $accounts = $this->sharedFixture->dbAdapter->quoteIdentifier('zf_accounts', true);
        $account_name = $this->sharedFixture->dbAdapter->quoteIdentifier('account_name', true);
        $accounts_list = $this->sharedFixture->dbAdapter->fetchCol("SELECT $account_name from $accounts ORDER BY $account_name");
        // if the save() did an UPDATE instead of an INSERT, then goofy should
        // be missing, and clarabell should be present
        $this->assertEquals(array('clarabell', 'dduck', 'mmouse'), $accounts_list);
    }

    /**
     * Ensures that rows returned by findDependentRowset() are updatable.
     *
     * @return void
     */
    public function testTableRelationshipFindDependentRowsetIsUpdateable()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Accounts');
        $bug_id_column = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $bug_description = $this->sharedFixture->dbAdapter->foldCase('bug_description');

        $parentRows = $table->find('mmouse');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $parentRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($parentRows));
        $parentRow1 = $parentRows->current();

        $childRows = $parentRow1->findDependentRowset('My_ZendDbTable_TableBugs');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $childRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($childRows));

        $this->assertEquals(1, $childRows->count());

        $childRow1 = $childRows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $childRow1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($childRow1));

        $childRow1->$bug_description = 'Updated description';
        $bug_id = $childRow1->$bug_id_column;
        try {
            $childRow1->save();
        } catch (Zend_Exception $e) {
            $this->fail('Failed with unexpected '.get_class($e).': '.$e->getMessage());
        }

        // find the row we just updated and make sure it has the new value.
        $bugs_table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $bugs_rows = $bugs_table->find($bug_id);
        $this->assertEquals(1, $bugs_rows->count());
        $bug1 = $bugs_rows->current();
        $this->assertEquals($bug_id, $bug1->$bug_id_column);
        $this->assertEquals('Updated description', $bug1->$bug_description);
    }

    /**
     * Ensures that rows returned by findManyToManyRowset() are updatable.
     *
     * @return void
     */
    public function testTableRelationshipFindManyToManyRowsetIsUpdateable()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Bugs');
        $product_id_column = $this->sharedFixture->dbAdapter->foldCase('product_id');
        $product_name = $this->sharedFixture->dbAdapter->foldCase('product_name');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableProducts', 'My_ZendDbTable_TableBugsProducts');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(3, $destRows->count());

        $row1 = $destRows->current();
        $product_id = $row1->$product_id_column;
        $row1->$product_name = 'AmigaOS';
        try {
            $row1->save();
        } catch (Zend_Exception $e) {
            $this->fail('Failed with unexpected '.get_class($e).': '.$e->getMessage());
        }

        // find the row we just updated and make sure it has the new value.
        $products_table = $this->sharedFixture->tableUtility->getTableById('Products');
        $product_rows = $products_table->find($product_id);
        $this->assertEquals(1, $product_rows->count());
        $product_row = $product_rows->current();
        $this->assertEquals($product_id, $product_row->$product_id_column);
        $this->assertEquals('AmigaOS', $product_row->$product_name);
    }

    public function testTableRelationshipOmitRefColumns()
    {
        $refMap = array(
            'Reporter' => array(
                'columns'       => array('reported_by'),
                'refTableClass' => 'My_ZendDbTable_TableAccounts'
            )
        );
        $table = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableSpecial',
            array(
                'name'          => 'zf_bugs',
                'referenceMap'  => $refMap
            )
        );

        $bug1 = $table->find(1)->current();
        $reporter = $bug1->findParentRow('My_ZendDbTable_TableAccounts');
        $this->assertEquals(array('account_name' => 'goofy'), $reporter->toArray());
    }

    /**
     * Test that findParentRow() works even if the column names are
     * not the same.
     */
    public function testTableRelationshipFindParentRowWithDissimilarColumns()
    {
        $bug_id = $this->sharedFixture->dbAdapter->foldCase('bug_id');
        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');

        $intersectionTable = $this->_getBugsProductsWithDissimilarColumns();
        $intRow = $intersectionTable->find(2, 3)->current();

        $bugRow = $intRow->findParentRow('My_ZendDbTable_TableBugs');
        $this->assertEquals(2, $bugRow->$bug_id);

        $productRow = $intRow->findParentRow('My_ZendDbTable_TableProducts');
        $this->assertEquals(3, $productRow->$product_id);
    }

    /**
     * Test that findDependentRowset() works even if the column names are
     * not the same.
     */
    public function testTableRelationshipFindDependentRowsetWithDissimilarColumns()
    {
        $intersectionTable = $this->_getBugsProductsWithDissimilarColumns();
        $bugsTable = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugs');
        $bugRow = $bugsTable->find(2)->current();

        $intRows = $bugRow->findDependentRowset($intersectionTable);
        $this->assertEquals(array(2, 3), array_values($intRows->current()->toArray()));
    }

    /**
     * Test that findManyToManyRowset() works even if the column names are
     * not the same.
     */
    public function testTableRelationshipFindManyToManyRowsetWithDissimilarColumns()
    {
        $product_id = $this->sharedFixture->dbAdapter->foldCase('product_id');

        $intersectionTable = $this->_getBugsProductsWithDissimilarColumns();
        $bugsTable = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableBugs');
        $bugRow = $bugsTable->find(2)->current();

        $productRows = $bugRow->findManyToManyRowset('My_ZendDbTable_TableProducts', $intersectionTable);
        $this->assertEquals(3, $productRows->current()->$product_id);
    }

    /**
     * Test that findManyToManyRowset() works even if the column types are
     * not the same.
     */
    public function testTableRelationshipFindManyToManyRowsetWithDissimilarTypes()
    {
        $table = $this->sharedFixture->tableUtility->getTableById('Products');

        $originRows = $table->find(1);
        $originRow1 = $originRows->current();

        $destRows = $originRow1->findManyToManyRowset('My_ZendDbTable_TableBugs', 'My_ZendDbTable_TableBugsProducts');
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $destRows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($destRows));

        $this->assertEquals(1, $destRows->count());
    }
    
    
    
    /**
     * Utility Methods Below
     * 
     */
    
    
    
    /**
     * Create database table based on BUGS_PRODUCTS bug with alternative
     * spellings of column names.  Then create a Table class for this
     * physical table and return it.
     */
    protected function _getBugsProductsWithDissimilarColumns()
    {
        $altCols = array(
            'boog_id'      => 'INTEGER NOT NULL',
            'produck_id'   => 'INTEGER NOT NULL',
            'PRIMARY KEY'  => 'boog_id,produck_id'
        );
        
        $clonedUtility = $this->_getClonedUtility(false);
        $altTableName = $clonedUtility->createTable('AltBugsProducts', 'bugs_products_alt', $altCols);
        $dbAdapter = $clonedUtility->getDbAdapter();
        
        $altBugsProducts = $dbAdapter->quoteIdentifier($this->sharedFixture->dbAdapter->foldCase($altTableName), true);
        $bugsProducts = $dbAdapter->quoteIdentifier($this->sharedFixture->dbAdapter->foldCase('zf_bugs_products'), true);
        $dbAdapter->query("INSERT INTO $altBugsProducts SELECT * FROM $bugsProducts");

        $refMap    = array(
            'Boog' => array(
                'columns'           => array('boog_id'),
                'refTableClass'     => 'My_ZendDbTable_TableBugs',
                'refColumns'        => array('bug_id')
            ),
            'Produck' => array(
                'columns'           => array('produck_id'),
                'refTableClass'     => 'My_ZendDbTable_TableProducts',
                'refColumns'        => array('product_id')
            )
        );
        $options = array('name' => $altTableName, 'referenceMap' => $refMap);
        $table = $this->sharedFixture->tableUtility->getTable('My_ZendDbTable_TableSpecial', $options);
        return $table;
    }

}
