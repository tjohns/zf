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

require_once 'Zend/Db/Table/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Table_OracleTest extends Zend_Db_Table_TestCommon
{

    public function testTableInsert()
    {
        $this->markTestSkipped($this->getDriver().' does not support auto-increment columns.');
    }

    public function testTableInsertSequence()
    {
        $table = $this->_getTable('Zend_Db_Table_TableBugs',
            array(Zend_Db_Table_Abstract::SEQUENCE => 'zfbugs_seq'));
        $row = array (
            'bug_description' => 'New bug',
            'bug_status'      => 'NEW',
            'created_on'      => new Zend_Db_Expr(
                $this->_db->quoteInto('DATE ?', '2007-04-02')),
            'updated_on'      => new Zend_Db_Expr(
                $this->_db->quoteInto('DATE ?', '2007-04-02')),
            'reported_by'     => 'micky',
            'assigned_to'     => 'goofy'
        );
        $insertResult         = $table->insert($row);
        $lastInsertId         = $this->_db->lastInsertId('zfbugs');
        $lastSequenceId       = $this->_db->lastSequenceId('zfbugs_seq');
        $this->assertEquals($insertResult, $lastInsertId);
        $this->assertEquals($insertResult, $lastSequenceId);
        $this->assertEquals(5, $insertResult);
    }

    public function getDriver()
    {
        return 'Oracle';
    }

     public function testTableUpdate()
    {
        $bug_id = $this->_db->quoteIdentifier('BUG_ID');
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
        $this->assertEquals(2, $row->BUG_ID, "Expecting row->bug_id to be 2");
        $this->assertEquals($data['bug_description'], $row->BUG_DESCRIPTION);
        $this->assertEquals($data['bug_status'], $row->BUG_STATUS);
    }

    public function testTableUpdateWhereArray()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');

        $data = array(
            'BUG_DESCRIPTION' => 'Synesthesia',
        );

        $where = array(
            "$bug_id IN (1, 3)",
            "$bug_status != 'UNKNOWN'"
            );

        $this->assertEquals(2, $this->_table['bugs']->update($data, $where));

        $count = 0;
        foreach ($this->_table['bugs']->find(array(1, 3)) as $row) {
            $this->assertEquals($data['BUG_DESCRIPTION'], $row->BUG_DESCRIPTION);
            ++$count;
        }

        $this->assertEquals(2, $count);
    }

    public function testTableFetchRow()
    {
        $table = $this->_table['bugs'];
        $row = $table->fetchRow();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertTrue(isset($row->BUG_DESCRIPTION));
    }

    public function testTableFetchRowWhere()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];
        $row = $table->fetchRow("$bug_id = 2");
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(2, $row->BUG_ID);
    }

    public function testTableFetchRowOrderAsc()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $row = $table->fetchRow("$bug_id > 1", "bug_id ASC");
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(2, $row->BUG_ID);
    }

    public function testTableFetchRowOrderDesc()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $table = $this->_table['bugs'];

        $row = $table->fetchRow(null, "bug_id DESC");
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertEquals(4, $row->BUG_ID);
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
        $this->assertEquals(2, $row1->BUG_ID);
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
        $this->assertEquals(4, $row1->BUG_ID);
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
        $this->assertEquals(4, $row1->BUG_ID);
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
        $this->assertEquals(2, $row1->BUG_ID);
    }

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
            'BUG_ID',
            'BUG_DESCRIPTION',
            'BUG_STATUS',
            'CREATED_ON',
            'UPDATED_ON',
            'REPORTED_BY',
            'ASSIGNED_TO',
            'VERIFIED_BY'
        );
        $this->assertEquals($cols, $info['cols']);

        $this->assertEquals(1, count($info['primary']));
        $pk = array('BUG_ID');
        $this->assertEquals($pk, array_values($info['primary']));
    }

    public function testTableCreateRow()
    {
        $table = $this->_table['bugs'];
        $row = $table->createRow();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row));
        $this->assertTrue(isset($row->BUG_DESCRIPTION));
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
        $this->assertTrue(isset($row->BUG_DESCRIPTION));
        $this->assertEquals('New bug', $row->BUG_DESCRIPTION);
    }
}
