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

abstract class Zend_Db_Table_Row_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableFindRow()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $this->assertTrue($rows->exists());
        $this->assertEquals(1, $rows->count());
    }

    public function testTableRowConstructor()
    {
        $table = $this->_table['bugs'];

        $row1 = new Zend_Db_Table_Row(
            array(
                'db'    => $this->_db,
                'table' => $table
            )
        );

        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $bug_description = $row1->bug_description;
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"bug_description\" is not in the row", $e->getMessage());
        }
    }

    public function testTableRowToArray()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $a = $row1->toArray();

        $this->assertTrue(is_array($a));

        $this->assertThat($a, $this->arrayHasKey('bug_id'));
        $this->assertThat($a, $this->arrayHasKey('bug_description'));
        $this->assertThat($a, $this->arrayHasKey('bug_status'));
        $this->assertThat($a, $this->arrayHasKey('created_on'));
        $this->assertThat($a, $this->arrayHasKey('updated_on'));
        $this->assertThat($a, $this->arrayHasKey('reported_by'));
        $this->assertThat($a, $this->arrayHasKey('assigned_to'));
        $this->assertThat($a, $this->arrayHasKey('verified_by'));
    }

    public function testTableRowMagicGet()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $this->assertEquals(1, $row1->bug_id);
            $this->assertEquals('System needs electricity to run', $row1->bug_description);
            $this->assertEquals('NEW', $row1->bug_status);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        
        if (!isset($row1->bug_id)) {
            $this->fail('Column "id" is set but isset() returns false');
        }
    }

    public function testTableRowMagicSet()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $row1->bug_description = 'foo';
            $this->assertEquals('foo', $row1->bug_description);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSetFromArray()
    {
        $table = $this->_table['bugs'];

        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $row1->setFromArray($data);

        try {
            $this->assertEquals($data['bug_description'], $row1->bug_description);
            $this->assertEquals($data['bug_status'], $row1->bug_status);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveInsert()
    {
        $table = $this->_table['bugs'];

        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );

        $row3 = $table->fetchNew();

        $row3->setFromArray($data);

        $row3->save();

        try {
            $this->assertEquals(5, $row3->bug_id);
            $this->assertEquals($data['bug_description'], $row3->bug_description);
            $this->assertEquals($data['bug_status'], $row3->bug_status);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSaveUpdate()
    {
        $table = $this->_table['bugs'];

        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $row1->setFromArray($data);
        $row1->save();

        try {
            $this->assertEquals(1, $row1->bug_id);
            $this->assertEquals($data['bug_description'], $row1->bug_description);
            $this->assertEquals($data['bug_status'], $row1->bug_status);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
    }

    public function testTableRowSetTable()
    {
        $table = $this->_table['bugs'];
        $table2 = $this->_table['products'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $row1->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Exception for incorrect parent table');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Exception'),
                'Expecting object of type Zend_Db_Table_Exception got '.get_class($e));
            $this->assertEquals('The specified Table is of class Zend_Db_Table_TableProducts, expecting class to be instance of Zend_Db_Table_TableBugs', $e->getMessage());
        }
    }

    public function testTableRowExceptionGetColumnNotInRow()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $column = 'doesNotExist';

        try {
            $dummy = $row1->$column;
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"$column\" is not in the row", $e->getMessage());
        }
    }

    public function testTableRowExceptionSetColumnNotInRow()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $column = 'doesNotExist';

        try {
            $row1->$column = 'dummy value';
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Specified column \"$column\" is not in the row", $e->getMessage());
        }
    }

    public function testTableRowExceptionSetPrimaryKey()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $row1->bug_id = 'dummy value';
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Changing the primary key value(s) is not allowed", $e->getMessage());
        }
    }

    public function testTableRowSerialize()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $row1 = $rows->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertThat($row1New, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $this->assertEquals($row1->toArray(), $row1New->toArray());
    }

    public function testTableRowSerializeExceptionNotConnected()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $row1 = $rows->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertThat($row1New, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $row1New->bug_description = 'New description';

        try {
            $row1New->save();
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals("Cannot save a Row unless it is connected", $e->getMessage());
        }
    }

    public function testTableRowSerializeReconnectedUpdate()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $row1 = $rows->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertThat($row1New, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $connected = $row1New->setTable($table);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertTrue($connected);

        $data = array(
            'bug_description' => 'New Description',
            'bug_status'      => 'INVALID'
        );
        $row1New->setFromArray($data);

        try {
            $rowsAffected = $row1New->save();
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertEquals(1, $rowsAffected);
    }

    public function testTableRowSerializeReconnectedDelete()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $row1 = $rows->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertThat($row1New, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        try {
            $connected = $row1New->setTable($table);
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertTrue($connected);

        try {
            $rowsAffected = $row1New->delete();
        } catch (Exception $e) {
            $this->fail("Caught exception of type \"".get_class($e)."\" where no exception was expected.  Exception message: \"".$e->getMessage()."\"\n");
        }
        $this->assertEquals(1, $rowsAffected);
    }

    public function testTableRowSerializeExceptionWrongTable()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);
        $row1 = $rows->current();

        $serRow1 = serialize($row1);

        $row1New = unserialize($serRow1);
        $this->assertThat($row1New, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');

        $table2 = $this->_table['products'];
        $connected = false;
        try {
            $connected = $row1New->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('The specified Table is of class Zend_Db_Table_TableProducts, expecting class to be instance of Zend_Db_Table_TableBugs', $e->getMessage());
        }
        $this->assertFalse($connected);
    }

}
