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
 * @version    $Id$
 */


/**
 * @see Zend_Db_Table_TestSetup
 */
require_once 'Zend/Db/Table/TestSetup.php';

/**
 * @see Zend_Registry
 */
require_once 'Zend/Registry.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableInfo()
    {
        $bugs = $this->_table['bugs'];
        $this->assertType('Zend_Db_Table_Abstract', $bugs);
        $info = $bugs->info();

        $keys = array(
            Zend_Db_Table_Abstract::SCHEMA,
            Zend_Db_Table_Abstract::NAME,
            Zend_Db_Table_Abstract::COLS,
            Zend_Db_Table_Abstract::PRIMARY,
            Zend_Db_Table_Abstract::METADATA,
            Zend_Db_Table_Abstract::ROW_CLASS,
            Zend_Db_Table_Abstract::ROWSET_CLASS,
            Zend_Db_Table_Abstract::REFERENCE_MAP,
            Zend_Db_Table_Abstract::DEPENDENT_TABLES,
            Zend_Db_Table_Abstract::SEQUENCE,
        );

        $this->assertEquals($keys, array_keys($info));

        $this->assertEquals('zfbugs', $info['name']);

        $this->assertEquals(8, count($info['cols']));
        $cols = array(
            'bug_id',
            'bug_description',
            'bug_status',
            'created_on',
            'updated_on',
            'reported_by',
            'assigned_to',
            'verified_by'
        );
        $this->assertEquals($cols, $info['cols']);

        $this->assertEquals(1, count($info['primary']));
        $pk = array('bug_id');
        $this->assertEquals($pk, array_values($info['primary']));
    }

    public function testTableExceptionSetInvalidRowClass()
    {
        $table = $this->_table['products'];
        $this->assertType('Zend_Db_Table_Abstract', $table);

        // @todo test
        $table->setRowClass('stdClass');

        // @todo test
        $table->setRowsetClass('stdClass');

        $this->markTestIncomplete();
    }

    public function testTableImplicitName()
    {
        Zend_Loader::loadClass('Zend_Db_Table_TableSpecial');
        // TableSpecial.php contains class bugs_products too.
        $table = new zfbugs_products(array('db' => $this->_db));
        $info = $table->info();
        $this->assertContains('name', array_keys($info));
        $this->assertEquals('zfbugs_products', $info['name']);
    }

    public function testTableOptionName()
    {
        $table = $this->_getTable('Zend_Db_Table_TableSpecial',
            array('name' => 'zfbugs'));
        $info = $table->info();
        $this->assertContains('name', array_keys($info));
        $this->assertEquals($info['name'], 'zfbugs');
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
        $this->assertType('Zend_Db_Table_Abstract', $table);

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
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e);
            $this->assertEquals('No reference rule "Verifier" from table Zend_Db_Table_TableBugs to table Zend_Db_Table_TableAccounts', $e->getMessage());
        }

        try {
            $ref = $table->getReference('Zend_Db_Table_TableProducts');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e);
            $this->assertEquals('No reference from table Zend_Db_Table_TableBugs to table Zend_Db_Table_TableProducts', $e->getMessage());
        }

        try {
            $ref = $table->getReference('Zend_Db_Table_TableProducts', 'Product');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e);
            $this->assertEquals('No reference rule "Product" from table Zend_Db_Table_TableBugs to table Zend_Db_Table_TableProducts', $e->getMessage());
        }

        try {
            $ref = $table->getReference('Zend_Db_Table_TableProducts', 'Reporter');
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e);
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
        $this->assertType('Zend_Db_Table_Abstract', $table);

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
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                Zend_Db_Table_Abstract::setDefaultAdapter(new stdClass());
                $this->fail('Expected to catch PHPUnit_Framework_Error');
            } catch (Exception $e) {
                $this->assertType('PHPUnit_Framework_Error', $e,
                    'Expecting object of type PHPUnit_Framework_Error, got '.get_class($e));
                $mesg = substr("Argument 1 passed to Zend_Db_Table_Abstract::setDefaultAdapter() must be an instance of Zend_Db_Adapter_Abstract, instance of stdClass given", 0, 100);
                $this->assertEquals($mesg, substr($e->getMessage(), 0, 100));
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testTableExceptionPrimaryKeyNotSpecified()
    {
        try {
            $table = $this->_getTable('Zend_Db_Table_TableBugs', array('primary' => ''));
            $this->fail('Expected to catch Zend_Db_Table_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
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
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
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
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals("No object of type Zend_Db_Adapter_Abstract has been specified", $e->getMessage());
        }

        Zend_Registry::set('registered_db', 327);
        try {
            $table = new Zend_Db_Table_TableBugs(array('db' => 'registered_db'));
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals("No object of type Zend_Db_Adapter_Abstract has been specified", $e->getMessage());
        }

        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                Zend_Db_Table_Abstract::setDefaultAdapter(327);
            } catch (Exception $e) {
                $this->assertType('PHPUnit_Framework_Error', $e,
                    'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
                $mesg = substr("Argument 1 passed to Zend_Db_Table_Abstract::setDefaultAdapter() must be an instance of Zend_Db_Adapter_Abstract, integer given", 0, 100);
                $this->assertEquals($mesg, substr($e->getMessage(), 0, 100));
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }

    }

    public function testTableFindSingleRow()
    {
        $table = $this->_table['bugs'];
        $rowset = $table->find(1);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(1, count($rowset));
    }

    public function testTableFindMultipleRows()
    {
        $table = $this->_table['bugs'];
        $rowset = $table->find(array(1, 2));
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(2, count($rowset));
    }

    public function testTableFindExceptionMissingKey()
    {
        $table = $this->_table['bugs'];
        try {
            $table->find();
            $this->fail('Expected to catch Zend_Db_Table_Exception for missing key');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals('No value(s) specified for the primary key', $e->getMessage());
        }
    }

    public function testTableFindExceptionIncorrectKeyCount()
    {
        $table = $this->_table['bugs'];
        try {
            $table->find(1, 2);
            $this->fail('Expected to catch Zend_Db_Table_Exception for incorrect key count');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals('Missing value(s) for the primary key', $e->getMessage());
        }
    }

    public function testTableFindCompoundSingleRow()
    {
        $table = $this->_table['bugs_products'];
        $rowset = $table->find(1, 2);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(1, count($rowset));
    }

    public function testTableFindCompoundMultipleRows()
    {
        $table = $this->_table['bugs_products'];
        $rowset = $table->find(array(1, 1), array(2, 3));
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(2, count($rowset));
    }

    public function testTableFindCompoundExceptionIncorrectKeyCount()
    {
        $table = $this->_table['bugs_products'];
        try {
            $rowset = $table->find(1);
            $this->fail('Expected to catch Zend_Db_Table_Exception for incorrect key count');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals('Missing value(s) for the primary key', $e->getMessage());
        }
    }

    public function testTableFindCompoundMultipleExceptionIncorrectValueCount()
    {
        $table = $this->_table['bugs_products'];
        try {
            $rowset = $table->find(array(1, 1), 2);
            $this->fail('Expected to catch Zend_Db_Table_Exception for incorrect key count');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Exception', $e,
                'Expecting object of type Zend_Db_Table_Exception, got '.get_class($e));
            $this->assertEquals('Missing value(s) for the primary key', $e->getMessage());
        }
    }

    public function testTableInsert()
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
        $lastInsertId = $this->_db->lastInsertId();
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals(5, $lastInsertId);
    }

    public function testTableInsertSequence()
    {
        $table = $this->_getTable('Zend_Db_Table_TableProducts',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zfproducts_seq'));
        $row = array (
            'product_name' => 'Solaris'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->_db->lastInsertId('zfproducts');
        $lastSequenceId       = $this->_db->lastSequenceId('zfproducts_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(4, $insertResult);
    }

    public function testTableInsertNaturalCompound()
    {
        $table = $this->_table['bugs_products'];
        $row = array(
            'bug_id'     => 2,
            'product_id' => 1
        );
        $primary = $table->insert($row);
        $this->assertType('array', $primary);
        $this->assertEquals(2, count($primary));
        $this->assertEquals(array(2, 1), array_values($primary));
    }

    /*
    public function testTableInsertNaturalExceptionKeyViolation()
    {
        $table = $this->_table['bugs'];
        $row = array (
            'bug_id'          => 1,
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        try {
            $insertResult = $table->insert($row);
            $this->fail('Expected to catch Zend_Db_Table_Exception for key violation');
        } catch (Zend_Exception $e) {
            echo "*** caught ".get_class($e)."\n";
            echo "*** ".$e->getMessage()."\n";
            $this->assertEquals('xxx', $e->getMessage());
        }
    }
     */

    /*
    public function testTableInsertNaturalCompoundExceptionKeyViolation()
    {
        $table = $this->_table['bugs_products'];
        $row = array(
            'bug_id'     => 1,
            'product_id' => 1
        );
        try {
            $table->insert($row);
            $this->fail('Expected to catch Zend_Db_Table_Exception for key violation');
        } catch (Zend_Exception $e) {
            echo "*** caught ".get_class($e)."\n";
            echo "*** ".$e->getMessage()."\n";
            $this->assertEquals('xxx', $e->getMessage());
        }
    }
     */

    public function testTableUpdate()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $data = array(
            'bug_description' => 'Implement Do What I Mean function',
            'bug_status'      => 'INCOMPLETE'
        );
        $table = $this->_table['bugs'];
        $result = $table->update($data, "$bug_id = 2");
        $this->assertEquals(1, $result);

        // Query the row to see if we have the new values.
        $rowset = $table->find(2);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(1, count($rowset), "Expecting rowset count to be 1");
        $row = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(2, $row->bug_id, "Expecting row->bug_id to be 2");
        $this->assertEquals($data['bug_description'], $row->bug_description);
        $this->assertEquals($data['bug_status'], $row->bug_status);
    }

    public function testTableUpdateWhereArray()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $data = array(
            'bug_description' => 'Synesthesia',
        );

        $where = array(
            "$bug_id IN (1, 3)",
            "$bug_status != 'UNKNOWN'"
            );

        $this->assertEquals(2, $this->_table['bugs']->update($data, $where));

        $count = 0;
        foreach ($this->_table['bugs']->find(array(1, 3)) as $row) {
            $this->assertEquals($data['bug_description'], $row->bug_description);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    public function testTableDelete()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];
        $rowset = $table->find(array(1, 2));
        $this->assertEquals(2, count($rowset));

        $table->delete("$bug_id = 2");

        $rowset = $table->find(array(1, 2));
        $this->assertEquals(1, count($rowset));
    }

    public function testTableDeleteWhereArray()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $where = array(
            "$bug_id IN (1, 3)",
            "$bug_status != 'UNKNOWN'"
            );

        $this->assertEquals(2, $this->_table['bugs']->delete($where));

        $this->assertEquals(0, count($this->_table['bugs']->find(array(1, 3))));
    }

    public function testTableCreateRow()
    {
        $table = $this->_table['bugs'];
        $row = $table->createRow();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertTrue(isset($row->bug_description));
    }

    public function testTableCreateRowWithData()
    {
        $table = $this->_table['bugs'];
        $data = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => '2007-04-02',
            'updated_on'      => '2007-04-02',
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $row = $table->createRow($data);
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertTrue(isset($row->bug_description));
        $this->assertEquals('New bug', $row->bug_description);
    }

    public function testTableFetchRow()
    {
        $table = $this->_table['bugs'];
        $row = $table->fetchRow();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertTrue(isset($row->bug_description));
    }

    public function testTableFetchRowWhere()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];
        $row = $table->fetchRow("$bug_id = 2");
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(2, $row->bug_id);
    }

    public function testTableFetchRowOrderAsc()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $row = $table->fetchRow("$bug_id > 1", "bug_id ASC");
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(2, $row->bug_id);
    }

    public function testTableFetchRowOrderDesc()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $row = $table->fetchRow(null, "bug_id DESC");
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(4, $row->bug_id);
    }

    public function testTableFetchRowEmpty()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $row = $table->fetchRow("$bug_id = -1");
        $this->assertEquals(null, $row,
            'Expecting null result for non-existent row');
    }

    public function testTableFetchAll()
    {
        $table = $this->_table['bugs'];
        $rowset = $table->fetchAll();
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(4, count($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
    }

    public function testTableFetchAllWhere()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $rowset = $table->fetchAll("$bug_id = 2");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(1, count($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
        $this->assertEquals(2, $row1->bug_id);
    }

    public function testTableFetchAllOrder()
    {
        $table = $this->_table['bugs'];
        $rowset = $table->fetchAll(null, "bug_id DESC");
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowset));
        $this->assertEquals(4, count($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
        $this->assertEquals(4, $row1->bug_id);
    }

    public function testTableFetchAllOrderExpr()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $rowset = $table->fetchAll(null, new Zend_Db_Expr("$bug_id + 1 DESC"));
        $this->assertType('Zend_Db_Table_Rowset', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset, got '.get_class($rowset));
        $this->assertEquals(4, count($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row', $row1,
            'Expecting object of type Zend_Db_Table_Row, got '.get_class($row1));
        $this->assertEquals(4, $row1->bug_id);
    }

    public function testTableFetchAllLimit()
    {
        $table = $this->_table['bugs'];
        $rowset = $table->fetchAll(null, null, 2, 1);
        $this->assertType('Zend_Db_Table_Rowset', $rowset,
            'Expecting object of type Zend_Db_Table_Rowset, got '.get_class($rowset));
        $this->assertEquals(2, count($rowset));
        $row1 = $rowset->current();
        $this->assertType('Zend_Db_Table_Row', $row1,
            'Expecting object of type Zend_Db_Table_Row, got '.get_class($row1));
        $this->assertEquals(2, $row1->bug_id);
    }

    public function testTableFetchAllEmpty()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $rowset = $table->fetchAll("$bug_id = -1");
        $this->assertEquals(0, count($rowset));
    }

    /**
     * Ensures that Zend_Db_Table_Abstract::setDefaultMetadataCache() performs as expected
     *
     * @return void
     */
    public function testTableSetDefaultMetadataCache()
    {
        $cache = $this->_getCache();

        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

        $this->assertSame($cache, Zend_Db_Table_Abstract::getDefaultMetadataCache());

        Zend_Db_Table_Abstract::setDefaultMetadataCache();

        $this->assertNull(Zend_Db_Table_Abstract::getDefaultMetadataCache());
    }

    /**
     * Ensures that table metadata caching works as expected when the cache object
     * is set in the configuration for a new table object.
     *
     * @return void
     */
    public function testTableMetadataCacheNew()
    {
        $cache = $this->_getCache();

        $tableBugsCustom1 = $this->_getTable(
            'Zend_Db_Table_TableBugsCustom',
            array('metadataCache' => $cache)
        );

        $this->assertType(
            'Zend_Cache_Core',
            $tableBugsCustom1->getMetadataCache()
        );

        $this->assertFalse($tableBugsCustom1->isMetadataFromCache);

        $tableBugsCustom1->setup();

        $this->assertTrue($tableBugsCustom1->isMetadataFromCache);

        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);

        $tableBugsCustom1->setup();

        $this->assertFalse($tableBugsCustom1->isMetadataFromCache);
    }

    /**
     * Ensures that table metadata caching works as expected when the default cache object
     * is set for the abstract table class.
     *
     * @return void
     */
    public function testTableMetadataCacheClass()
    {
        $cache = $this->_getCache();

        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);

        $tableBugsCustom1 = $this->_getTable('Zend_Db_Table_TableBugsCustom');

        $this->assertFalse($tableBugsCustom1->isMetadataFromCache);

        $this->assertType(
            'Zend_Cache_Core',
            $tableBugsCustom1->getMetadataCache()
        );

        $tableBugsCustom1->setup();

        $this->assertTrue($tableBugsCustom1->isMetadataFromCache);

        $cache->clean(Zend_Cache::CLEANING_MODE_ALL);

        $tableBugsCustom1->setup();

        $this->assertFalse($tableBugsCustom1->isMetadataFromCache);
    }

    /**
     * Returns a clean Zend_Cache_Core with File backend
     *
     * @return Zend_Cache_Core
     */
    protected function _getCache()
    {
        /**
         * @see Zend_Cache
         */
        require_once 'Zend/Cache.php';

        $frontendOptions = array(
            'automatic_serialization' => true
        );

        $backendOptions  = array(
            'cacheDir'                => dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files',
            'fileNamePrefix'          => 'Zend_Db_Table_TestCommon'
        );

        $cacheFrontend = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);

        $cacheFrontend->clean(Zend_Cache::CLEANING_MODE_ALL);

        return $cacheFrontend;
    }

}
