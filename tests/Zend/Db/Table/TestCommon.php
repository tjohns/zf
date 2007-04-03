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

require_once 'Zend/Db/Table/TestSetup.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_Table_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableInfo()
    {
        $bugs = $this->_table['bugs'];
        $this->assertThat($bugs, $this->isInstanceOf('Zend_Db_Table_Abstract'));
        $info = $bugs->info();

        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::SCHEMA));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::NAME));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::COLS));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::PRIMARY));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::METADATA));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::ROW_CLASS));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::ROWSET_CLASS));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::REFERENCE_MAP));
        $this->assertThat($info, $this->arrayHasKey(Zend_Db_Table_Abstract::DEPENDENT_TABLES));

        $this->assertEquals('bugs', $info['name']);

        $this->assertEquals(8, count($info['cols']));
        $this->assertContains('bug_id',          $info['cols']);
        $this->assertContains('bug_description', $info['cols']);

        $this->assertEquals(1, count($info['primary']));
        $this->assertContains('bug_id', $info['primary']);
    }

    public function testTableExceptionSetInvalidRowClass()
    {
        $table = $this->_table['products'];
        $this->assertThat($table, $this->isInstanceOf('Zend_Db_Table_Abstract'));

        // @todo test
        $table->setRowClass('stdClass');

        // @todo test
        $table->setRowsetClass('stdClass');
    }

    public function testTableImplicitName()
    {
        Zend_Loader::loadClass('Zend_Db_Table_TableSpecial');
        // TableSpecial.php contains class bugs_products too.
        $table = new bugs_products(array('db' => $this->_db));
        $info = $table->info();
        $this->assertThat($info, $this->arrayHasKey('name'));
        $this->assertEquals('bugs_products', $info['name']);
    }

    public function testTableOptionName()
    {
        $table = $this->_getTable('Zend_Db_Table_TableSpecial',
            array('name' => 'bugs'));
        $info = $table->info();
        $this->assertThat($info, $this->arrayHasKey('name'));
        $this->assertEquals($info['name'], 'bugs');
    }

    public function testTableOptionAdapter()
    {
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array('db' => $this->_db));
        $db = $table->getAdapter();
        $this->assertSame($this->_db, $db);
    }

    public function testTableOptionRowClass()
    {
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array('rowClass' => 'stdClass'));
        $rowClass = $table->getRowClass();
        $this->assertEquals($rowClass, 'stdClass');

        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array('rowsetClass' => 'stdClass'));
        $rowsetClass = $table->getRowsetClass();
        $this->assertEquals($rowsetClass, 'stdClass');
    }

    public function testTableGetRowClass()
    {
        $table = $this->_table['products'];
        $this->assertThat($table, $this->isInstanceOf('Zend_Db_Table_Abstract'));

        $rowClass = $table->getRowClass();
        $this->assertEquals($rowClass, 'Zend_Db_Table_Row');

        $rowsetClass = $table->getRowsetClass();
        $this->assertEquals($rowsetClass, 'Zend_Db_Table_Rowset');
    }

    public function testTableOptionReferenceMap()
    {
        $refReporter = array(
            'columns'           => array('reported_by'),
            'refTableClass'     => 'Zend_Db_Table_TableAccounts',
            'refColumns'        => array('account_id')
        );
        $refEngineer = array(
            'columns'           => array('assigned_to'),
            'refTableClass'     => 'Zend_Db_Table_TableAccounts',
            'refColumns'        => array('account_id')
        );
        $refMap = array(
            'Reporter' => $refReporter,
            'Engineer' => $refEngineer
        );
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array('referenceMap' => $refMap));

        $this->assertEquals($refReporter, $table->getReference('Zend_Db_Table_TableAccounts'));
        $this->assertEquals($refReporter, $table->getReference('Zend_Db_Table_TableAccounts', 'Reporter'));
        $this->assertEquals($refEngineer, $table->getReference('Zend_Db_Table_TableAccounts', 'Engineer'));
    }

    public function testTableExceptionOptionReferenceMap()
    {
        $refReporter = array(
            'columns'           => array('reported_by'),
            'refTableClass'     => 'Zend_Db_Table_TableAccounts',
            'refColumns'        => array('account_id')
        );
        $refEngineer = array(
            'columns'           => array('assigned_to'),
            'refTableClass'     => 'Zend_Db_Table_TableAccounts',
            'refColumns'        => array('account_id')
        );
        $refMap = array(
            'Reporter' => $refReporter,
            'Engineer' => $refEngineer
        );
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array('referenceMap' => $refMap));

        try {
            $ref = $table->getReference('Zend_Db_Table_TableAccounts', 'Verifier');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'));
            $this->assertEquals('No reference rule "Verifier" from table Zend_Db_Table_TableBugs to table Zend_Db_Table_TableAccounts', $e->getMessage());
        }

        try {
            $ref = $table->getReference('Zend_Db_Table_TableProducts');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'));
            $this->assertEquals('No reference from table Zend_Db_Table_TableBugs to table Zend_Db_Table_TableProducts', $e->getMessage());
        }

        try {
            $ref = $table->getReference('Zend_Db_Table_TableProducts', 'Product');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'));
            $this->assertEquals('No reference rule "Product" from table Zend_Db_Table_TableBugs to table Zend_Db_Table_TableProducts', $e->getMessage());
        }

        try {
            $ref = $table->getReference('Zend_Db_Table_TableProducts', 'Reporter');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'));
            $this->assertEquals('Reference rule "Reporter" does not reference table Zend_Db_Table_TableProducts', $e->getMessage());
        }

    }

    public function testTableOptionDependentTables()
    {
        $depTables = array('Zend_Db_Table_Foo');
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array('dependentTables' => $depTables));
        $this->assertEquals($depTables, $table->getDependentTables());
    }

    public function testTableSetRowClass()
    {
        $table = $this->_table['products'];
        $this->assertThat($table, $this->isInstanceOf('Zend_Db_Table_Abstract'));

        $table->setRowClass('stdClass');
        $rowClass = $table->getRowClass();
        $this->assertEquals($rowClass, 'stdClass');

        $table->setRowsetClass('stdClass');
        $rowsetClass = $table->getRowsetClass();
        $this->assertEquals($rowsetClass, 'stdClass');
    }

    public function testTableSetDefaultAdapter()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter($this->_db);
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $this->assertSame($this->_db, $db);
        // don't use _getTable() method because it defaults the adapter
        Zend_Loader::loadClass('Zend_Db_Table_TableBugs');
        $table = new Zend_Db_Table_TableBugs();
        $db = $table->getAdapter();
        $this->assertSame($this->_db, $db);
    }

    public function testTableExceptionSetInvalidDefaultAdapter()
    {
        try {
            Zend_Db_Table_Abstract::setDefaultAdapter(new stdClass());
            $this->fail('Expected to catch PHPUnit_Framework_Error');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('PHPUnit_Framework_Error'),
                'Expecting object of type PHPUnit_Framework_Error, got '.get_class($e));
            $mesg = substr("Argument 1 passed to Zend_Db_Table_Abstract::setDefaultAdapter() must be an instance of Zend_Db_Adapter_Abstract, instance of stdClass given", 0, 100);
            $this->assertEquals($mesg, substr($e->getMessage(), 0, 100));
        }
    }

    public function testTableExceptionPrimaryKeyNotSpecified()
    {
        try {
            $table = $this->_getTable('Zend_Db_Table_TableBugs', array('primary' => ''));
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertContains("Primary key column(s)", $e->getMessage());
            $this->assertContains("are not columns in this table", $e->getMessage());
        }
    }

    public function testTableExceptionInvalidPrimaryKey()
    {
        try {
            $table = new Zend_Db_Table_TableBugs(array('primary' => 'foo'));
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertContains("Primary key column(s)", $e->getMessage());
            $this->assertContains("are not columns in this table", $e->getMessage());
        }
    }

    public function testTableExceptionNoAdapter()
    {
        Zend_Loader::loadClass('Zend_Db_Table_TableBugs');

        try {
            $table = new Zend_Db_Table_TableBugs(array('db' => 327));
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals("No object of type Zend_Db_Adapter_Abstract has been specified", $e->getMessage());
        }

        Zend_Registry::set('registered_db', 327); 
        try {
            $table = new Zend_Db_Table_TableBugs(array('db' => 'registered_db'));
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals("No object of type Zend_Db_Adapter_Abstract has been specified", $e->getMessage());
        }

        try {
            Zend_Db_Table_Abstract::setDefaultAdapter(327);
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('PHPUnit_Framework_Error'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $mesg = substr("Argument 1 passed to Zend_Db_Table_Abstract::setDefaultAdapter() must be an instance of Zend_Db_Adapter_Abstract, integer given", 0, 100);
            $this->assertEquals($mesg, substr($e->getMessage(), 0, 100));
        }

    }

    public function testTableFind()
    {
        $table = $this->_table['bugs'];
        try {
            $table->find();
            $this->fail('Expected to catch Zend_Db_Table_Exception for missing key');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
        }

        $row1 = $table->find(1);
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');

        $rows = $table->find(array(1, 2));
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');

        try {
            $table->find(1, 2);
            $this->fail('Expected to catch Zend_Db_Table_Exception for incorrect key count');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
        }
    }

    public function testTableInsertAutoIncrement()
    {
        $table = $this->_table['bugs'];
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult = $table->insert($row);
        $last_insert_id = $this->_db->lastInsertId();

        $this->assertEquals($insertResult, $last_insert_id);
        $this->assertEquals(5, $last_insert_id);
    }

    public function testTableInsertSequence()
    {
        $driver = $this->getDriver();
        $this->markTestSkipped("$driver does not support sequences.");
    }

    public function testTableUpdate()
    {
        $data = array(
            'bug_description' => 'Implement Do What I Mean function',
            'bug_status'      => 'INCOMPLETE'
        );
        $table = $this->_table['bugs'];
        $result = $table->update($data, 'bug_id = 2');
        $this->assertEquals(1, $result);

        // Query the row to see if we have the new values.
        $rows = $table->find(2);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rows));
        $this->assertEquals(1, $rows->count(), "Expecting rowset count to be 1");
        $row = $rows->current();
        $this->assertThat($row, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(2, $row->bug_id, "Expecting row->bug_id to be 2");
        $this->assertEquals($data['bug_description'], $row->bug_description);
        $this->assertEquals($data['bug_status'], $row->bug_status);
    }

    public function testTableDelete()
    {
        $table = $this->_table['bugs'];
        $rows = $table->find(array(1, 2));
        $this->assertEquals(2, $rows->count());

        $table->delete('bug_id = 2');

        $rows = $table->find(array(1, 2));
        $this->assertEquals(1, $rows->count());
    }

    public function testTableFetchNew()
    {
        $table = $this->_table['bugs'];
        $row1 = $table->fetchNew();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
    }

    public function testTableFetchRow()
    {
        $table = $this->_table['bugs'];
        $rows = $table->fetchRow();
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $rows = $table->fetchRow('bug_id = -1');
        $this->assertEquals($rows, null,
            'Expecting null result for non-existent row');
    }

    public function testTableFetchAll()
    {
        $table = $this->_table['bugs'];
        $rows = $table->fetchAll();
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $this->assertEquals(4, $rows->count());
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
    }

    public function testTableFetchAllWhere()
    {
        $table = $this->_table['bugs'];
        $rows = $table->fetchAll('bug_id = 2');
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $this->assertEquals(1, $rows->count());
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $this->assertEquals(2, $row1->bug_id);
    }

    public function testTableFetchAllOrder()
    {
        $table = $this->_table['bugs'];
        $rows = $table->fetchAll(null, "bug_id DESC");
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $this->assertEquals(4, $rows->count());
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $this->assertEquals(4, $row1->bug_id);
    }

    public function testTableFetchAllOrderExpr()
    {
        $table = $this->_table['bugs'];
        $rows = $table->fetchAll(null, new Zend_Db_Expr('bug_id + 1 DESC'));
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset'),
            'Expecting object of type Zend_Db_Table_Rowset');
        $this->assertEquals(4, $rows->count());
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row'),
            'Expecting object of type Zend_Db_Table_Row');
        $this->assertEquals(4, $row1->bug_id);
    }

    public function testTableFetchAllLimit()
    {
        $table = $this->_table['bugs'];
        $rows = $table->fetchAll(null, null, 2, 1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset'),
            'Expecting object of type Zend_Db_Table_Rowset');
        $this->assertEquals(2, $rows->count());
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row'),
            'Expecting object of type Zend_Db_Table_Row');
        $this->assertEquals(2, $row1->bug_id);
    }

}
