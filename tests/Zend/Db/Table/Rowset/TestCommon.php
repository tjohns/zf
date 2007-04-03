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

abstract class Zend_Db_Table_Rowset_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableRowsetIterator()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(array(1, 2));
        $this->assertThat($rows, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');

        // see if we're at the beginning
        $this->assertEquals(0, $rows->key());

        // get first row and see if it's the right one
        $row1 = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $this->assertEquals(1, $row1->bug_id);

        // advance to next row
        $this->assertEquals(1, $rows->next());
        $this->assertEquals(1, $rows->key());
        $this->assertTrue($rows->valid());

        // get second row and see if it's the right one
        $row2 = $rows->current();
        $this->assertThat($row2, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $this->assertEquals(2, $row2->bug_id);

        // advance beyond last row
        $this->assertEquals(2, $rows->next());
        $this->assertEquals(2, $rows->key());
        $this->assertFalse($rows->valid());
        $this->assertFalse($rows->current());

        // rewind to beginning 
        $rows->rewind();
        $this->assertEquals(0, $rows->key());
        $this->assertTrue($rows->valid());

        // get row at beginning and compare it to 
        // the one we got earlier
        $row1Copy = $rows->current();
        $this->assertThat($row1, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
        $this->assertEquals(1, $row1->bug_id);
        $this->assertSame($row1, $row1Copy);
    }

    public function testTableRowsetToArray()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(array(1, 2));
        $this->assertEquals(2, $rows->count());

        // iterate through the rowset, because that's the only way
        // to force it to instantiate the individual Rows
        foreach ($rows as $row)
        {
            $row->bug_description = 'foo';
        }

        $a = $rows->toArray();

        $this->assertTrue(is_array($a));
        $this->assertEquals(count($a), $rows->count());
        $this->assertTrue(is_array($a[0]));
        $this->assertEquals(8, count($a[0]));
        $this->assertEquals('foo', $a[0]['bug_description']);
    }

    public function testTableSerializeRowset()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);

        $serRows = serialize($rows);

        $rowsNew = unserialize($serRows);
        $this->assertThat($rowsNew, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');

        $row1New = $rowsNew->current();
        $this->assertThat($row1New, $this->isInstanceOf('Zend_Db_Table_Row_Abstract'),
            'Expecting object of type Zend_Db_Table_Row_Abstract');
    }

    public function testTableSerializeRowsetExceptionWrongTable()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);

        // iterate through the rowset, because that's the only way
        // to force it to instantiate the individual Rows
        foreach ($rows as $row)
        {
            $row->bug_description = $row->bug_description;
        }

        $serRows = serialize($rows);

        $rowsNew = unserialize($serRows);
        $this->assertThat($rowsNew, $this->isInstanceOf('Zend_Db_Table_Rowset_Abstract'),
            'Expecting object of type Zend_Db_Table_Rowset_Abstract');

        $table2 = $this->_table['products'];
        $connected = false;
        try {
            $connected = $rowsNew->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Table_Row_Exception'),
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('The specified Table is of class Zend_Db_Table_TableProducts, expecting class to be instance of Zend_Db_Table_TableBugs', $e->getMessage());
        }
        $this->assertFalse($connected);
    }

}
