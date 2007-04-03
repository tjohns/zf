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

require_once 'Zend/Db/TestSetup.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_Adapter_TestCommon extends Zend_Db_TestSetup
{

    public abstract function testDbAdapterExceptionInvalidLoginCredentials();

    /**
     * Test Adapter's delete() method.
     * Delete one row from test table, and verify it was deleted.
     * Then try to delete a row that doesn't exist, and verify it had no effect.
     *
     * @todo: test that require delimited identifiers.
     */
    public function testDbAdapterDelete()
    {
        $select = $this->_db->select()->from('products')->order('product_id ASC');
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(3, count($result), 'Expected count of result to be 2');
        $this->assertEquals(1, $result[0]['product_id'], 'Expecting product_id of 0th row to be 1');

        $rowsAffected = $this->_db->delete('products', 'product_id = 2');
        $this->assertEquals(1, $rowsAffected, 'Expected rows affected to return 1', 'Expecting rows affected to be 1');

        $select = $this->_db->select()->from('products')->order('product_id ASC');
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(2, count($result), 'Expected count of result to be 2');
        $this->assertEquals(1, $result[0]['product_id'], 'Expecting product_id of 0th row to be 1');

        $rowsAffected = $this->_db->delete('products', 'product_id = 327');
        $this->assertEquals(0, $rowsAffected, 'Expected rows affected to return 0');
    }

    /**
     * Test Adapter's describeTable() method.
     * Retrieve the adapter's description of the test table and examine it.
     */
    public function testDbAdapterDescribeTable()
    {
        $desc = $this->_db->describeTable('products');

        $this->assertThat($desc, $this->arrayHasKey('product_name'));

        $this->assertThat($desc['product_name'], $this->arrayHasKey('SCHEMA_NAME'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('TABLE_NAME'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('COLUMN_NAME'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('COLUMN_POSITION'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('DATA_TYPE'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('DEFAULT'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('NULLABLE'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('LENGTH'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('SCALE'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('PRECISION'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('UNSIGNED'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('PRIMARY'));
        $this->assertThat($desc['product_name'], $this->arrayHasKey('PRIMARY_POSITION'));

        $this->assertEquals('products',          $desc['product_name']['TABLE_NAME']);
        $this->assertEquals('product_name',      $desc['product_name']['COLUMN_NAME']);
        $this->assertEquals(2,                   $desc['product_name']['COLUMN_POSITION']);
        $this->assertRegExp('/varchar/i',        $desc['product_name']['DATA_TYPE']);
        $this->assertEquals('',                  $desc['product_name']['DEFAULT']);
        $this->assertTrue(                       $desc['product_name']['NULLABLE']);
        $this->assertEquals(0,                   $desc['product_name']['SCALE']);
        $this->assertEquals(0,                   $desc['product_name']['PRECISION']);
        $this->assertFalse(                      $desc['product_name']['PRIMARY']);
        $this->assertEquals('',                  $desc['product_name']['PRIMARY_POSITION']);

        $this->assertTrue(                       $desc['product_id']['PRIMARY']);
        $this->assertEquals(1,                   $desc['product_id']['PRIMARY_POSITION']);
    }

    /**
     * Test the Adapter's fetchAll() method.
     */
    public function testDbAdapterFetchAll()
    {
        $result = $this->_db->fetchAll('SELECT * FROM products WHERE product_id > ? ORDER BY product_id ASC', 1);
        $this->assertEquals(2, count($result));
        $this->assertEquals('2', $result[0]['product_id']);
    }

    /**
     * Test the Adapter's fetchAssoc() method.
     */
    public function testDbAdapterFetchAssoc()
    {
        $result = $this->_db->fetchAssoc('SELECT * FROM products WHERE product_id > ? ORDER BY product_id DESC', 1);
        foreach ($result as $idKey => $row) {
            $this->assertEquals($idKey, $row['product_id']);
        }
    }

    /**
     * Test the Adapter's fetchCol() method.
     */
    public function testDbAdapterFetchCol()
    {
        $result = $this->_db->fetchCol('SELECT * FROM products WHERE product_id > ? ORDER BY product_id ASC', 1);
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(3, $result[1]);
    }

    /**
     * Test the Adapter's fetchOne() method.
     */
    public function testDbAdapterFetchOne()
    {
        $prod = 'Linux';
        $result = $this->_db->fetchOne('SELECT product_name FROM products WHERE product_id > ? ORDER BY product_id', 1);
        $this->assertEquals($prod, $result);
    }

    /**
     * Test the Adapter's fetchPairs() method.
     */
    public function testDbAdapterFetchPairs()
    {
        $prod = 'Linux';
        $result = $this->_db->fetchPairs('SELECT product_id, product_name FROM products WHERE product_id > ? ORDER BY product_id ASC', 1);
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals($prod, $result[2]);
    }

    /**
     * Test the Adapter's fetchRow() method.
     */
    public function testDbAdapterFetchRow()
    {
        $result = $this->_db->fetchRow('SELECT * FROM products WHERE product_id > ? ORDER BY product_id', 1);
        $this->assertEquals(2, count($result)); // count columns
        $this->assertEquals(2, $result['product_id']);
    }

    /**
     * Test the Adapter's insert() method.
     * This requires providing an associative array of column=>value pairs.
     *
     * @todo: test that require delimited identifiers.
     */
    public function testDbAdapterInsert()
    {
        $row = array (
            'product_name' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('products', $row);
        $this->assertEquals(1, $rowsAffected);
        $id = $this->_db->lastInsertId();
        $this->assertEquals('4', (string) $id, 'Expected new id to be 4');
    }

    /**
     * Test the Adapter's limit() method.
     * Fetch 1 row.  Then fetch 1 row offset by 1 row.
     */
    public function testDbAdapterLimit()
    {
        $sql = $this->_db->limit('SELECT * FROM products', 1);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(1, $result[0]['product_id']);

        $sql = $this->_db->limit('SELECT * FROM products', 1, 1);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test the Adapter's listTables() method.
     * Fetch the list of tables and verify that the test table exists in
     * the list.
     */
    public function testDbAdapterListTables()
    {
        $tables = $this->_db->listTables();
        $this->assertContains('products', $tables);
    }

    public function testDbAdapterQuoteIdentifier()
    {
        $value = $this->_db->quoteIdentifier('table_name');
        $this->assertEquals('"table_name"', $value);
        $value = $this->_db->quoteIdentifier('table_"_name');
        $this->assertEquals('"table_""_name"', $value);
    }

    public function testDbAdapterQuote()
    {
        // test double quotes are fine
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals("'St John\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quote("St John's Wort");
        $this->assertEquals("'St John''s Wort'", $value);

        // quote an array
        $value = $this->_db->quote(array("it's", 'all', 'right!'));
        $this->assertEquals("'it''s', 'all', 'right!'", $value);

        // test numeric
        $value = $this->_db->quote('1');
        $this->assertEquals("'1'", $value);

        $value = $this->_db->quote(1);
        $this->assertEquals("1", $value);

        $value = $this->_db->quote(array(1,'2',3));
        $this->assertEquals("1, '2', 3", $value);
    }

    public function testDbAdapterQuoteInto()
    {
        // test double quotes are fine
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John''s Wort'", $value);
    }


    /**
     *
    public function testDbAdapterTransactionCommit()
    {
    }
     */

    /**
     *
    public function testDbAdapterTransactionRollback()
    {
    }
     */

    /**
     * Test the Adapter's update() method.
     * Update a single row and verify that the change was made.
     * Attempt to update a row that does not exist, and verify
     * that no change was made.
     *
     * @todo: test that requires delimited identifiers.
     */
    public function testDbAdapterUpdate()
    {
        // Test that we can change the values in
        // an existing row.
        $result = $this->_db->update(
            'products',
            array('product_name' => 'Vista'),
            'product_id = 1'
        );
        $this->assertEquals(1, $result);

        // Query the row to see if we have the new values.
        $select = $this->_db->select();
        $select->from('products');
        $select->where('product_id = 1');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();

        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals('Vista', $result[0]['product_name']);

        // Test that update affects no rows if the WHERE
        // clause matches none.
        $result = $this->_db->update(
            'products',
            array('product_name' => 'Vista'),
            'product_id = 327'
        );
        $this->assertEquals(0, $result);
    }

    public function testDbAdapterExceptionInvalidLimitArgument()
    {
        $exceptionSeen = false;
        try {
            $sql = $this->_db->limit('SELECT * FROM products', 0);
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'), 'Expecting object of type Zend_Db_Adapter_Exception');
            $exceptionSeen = true;
        }
        $this->assertTrue($exceptionSeen);

        $exceptionSeen = false;
        try {
            $sql = $this->_db->limit('SELECT * FROM products', 1, -1);
        } catch (Exception $e) {
            $this->assertThat($e, $this->isInstanceOf('Zend_Db_Adapter_Exception'), 'Expecting object of type Zend_Db_Adapter_Exception');
            $exceptionSeen = true;
        }
        $this->assertTrue($exceptionSeen);
    }

}
