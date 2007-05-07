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

require_once 'Zend/Db/Table/Rowset/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Table_Rowset_OracleTest extends Zend_Db_Table_Rowset_TestCommon
{

    public function getDriver()
    {
        return 'Oracle';
    }

    public function testTableRowsetIterator()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(array(1, 2));
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rows));

        // see if we're at the beginning
        $this->assertEquals(0, $rows->key());
        $this->assertTrue($rows->valid());

        // get first row and see if it's the right one
        $row1 = $rows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
        $this->assertEquals(1, $row1->BUG_ID);

        // advance to next row
        $rows->next();
        $this->assertEquals(1, $rows->key());
        $this->assertTrue($rows->valid());

        // get second row and see if it's the right one
        $row2 = $rows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row2,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row2));
        $this->assertEquals(2, $row2->BUG_ID);

        // advance beyond last row
        $rows->next();
        $this->assertEquals(2, $rows->key());
        $this->assertFalse($rows->valid());

        // current() returns null if beyond last row
        $row3 = $rows->current();
        $this->assertNull($row3);

        // rewind to beginning
        $rows->rewind();
        $this->assertEquals(0, $rows->key());
        $this->assertTrue($rows->valid());

        // get row at beginning and compare it to
        // the one we got earlier
        $row1Copy = $rows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
        $this->assertEquals(1, $row1->BUG_ID);
        $this->assertSame($row1, $row1Copy);
    }

    public function testTableRowsetToArray()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(array(1, 2));
        $this->assertEquals(2, count($rows));

        // iterate through the rowset, because that's the only way
        // to force it to instantiate the individual Rows
        foreach ($rows as $row)
        {
            $row->BUG_DESCRIPTION = 'foo';
        }

        $a = $rows->toArray();

        $this->assertTrue(is_array($a));
        $this->assertEquals(count($a), count($rows));
        $this->assertTrue(is_array($a[0]));
        $this->assertEquals(8, count($a[0]));
        $this->assertEquals('foo', $a[0]['BUG_DESCRIPTION']);
    }

    public function testTableSerializeRowsetExceptionWrongTable()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);

        // iterate through the rowset, because that's the only way
        // to force it to instantiate the individual Rows
        foreach ($rows as $row)
        {
            $row->BUG_DESCRIPTION = $row->BUG_DESCRIPTION;
        }

        $serRows = serialize($rows);

        $rowsNew = unserialize($serRows);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowsNew,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowsNew));

        $table2 = $this->_table['products'];
        $connected = false;
        try {
            $connected = $rowsNew->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('The specified Table is of class Zend_Db_Table_TableProducts, expecting class to be instance of Zend_Db_Table_TableBugs', $e->getMessage());
        }
        $this->assertFalse($connected);
    }

}
