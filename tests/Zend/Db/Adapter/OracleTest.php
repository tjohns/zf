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

/**
 * @see Zend_Db_Adapter_TestCommon
 */
require_once 'Zend/Db/Adapter/TestCommon.php';

/**
 * @see Zend_Db_Adapter_Oracle
 */
require_once 'Zend/Db/Adapter/Oracle.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Adapter_OracleTest extends Zend_Db_Adapter_TestCommon
{

    public function testAdapterExceptionInvalidLoginCredentials()
    {
        $params = $this->_util->getParams();
        $params['password'] = 'xxxxxxxx'; // invalid password

        try {
            $db = new Zend_Db_Adapter_Oracle($params);
			$db->getConnection();
            $this->fail('Expected to catch Zend_Db_Adapter_Oracle_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Adapter_Oracle_Exception', $e,
                'Expected to catch Zend_Db_Adapter_Oracle_Exception, got '.get_class($e));
        }
    }

    /**
     * Test the Adapter's limit() method.
     * Fetch 1 row.  Then fetch 1 row offset by 1 row.
     */
    public function testAdapterLimit()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');

        $sql = $this->_db->limit("SELECT * FROM $products", 1);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result),
            'Expecting row count to be 1');
        $this->assertEquals(3, count($result[0]),
            'Expecting column count to be 3');
        $this->assertEquals(1, $result[0]['PRODUCT_ID'],
            'Expecting to get product_id 1');
    }

    public function testAdapterLimitOffset()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');

        $sql = $this->_db->limit("SELECT * FROM $products", 1, 1);

        $stmt = $this->_db->query($sql);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result),
            'Expecting row count to be 1');
        $this->assertEquals(3, count($result[0]),
            'Expecting column count to be 3');
        $this->assertEquals(2, $result[0]['PRODUCT_ID'],
            'Expecting to get product_id 2');
    }

    public function getDriver()
    {
        return 'Oracle';
	}

    public function testAdapterListTables()
    {
        $tables = $this->_db->listTables();
        $this->assertContains(strtoupper('zfproducts'), $tables);
    }

    public function testAdapterQuoteIdentifier()
    {
        $value = $this->_db->quoteIdentifier('table_name');
        $this->assertEquals('table_name', $value);
        $value = $this->_db->quoteIdentifier('table_"_name');
        $this->assertEquals('table_""_name', $value);
    }

    public function testAdapterQuote()
    {
        // test double quotes are fine
        $value = $this->_db->quote('St John"s Wort');
        $this->assertEquals('\'St John"s Wort\'', $value);

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

    public function testAdapterQuoteInto()
    {
        // test double quotes are fine
        $value = $this->_db->quoteInto('id=?', 'St John"s Wort');
        $this->assertEquals("id='St John\"s Wort'", $value);

        // test that single quotes are escaped with another single quote
        $value = $this->_db->quoteInto('id = ?', 'St John\'s Wort');
        $this->assertEquals("id = 'St John''s Wort'", $value);
	}

    public function testAdapterDescribeTable()
    {
        $desc = $this->_db->describeTable('zfproducts');

        $this->assertThat($desc, $this->arrayHasKey('PRODUCT_NAME'));
        $this->assertThat($desc, $this->arrayHasKey('PRODUCT_NAME'));

        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('SCHEMA_NAME'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('TABLE_NAME'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('COLUMN_NAME'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('COLUMN_POSITION'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('DATA_TYPE'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('DEFAULT'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('NULLABLE'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('LENGTH'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('SCALE'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('PRECISION'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('UNSIGNED'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('PRIMARY'));
        $this->assertThat($desc['PRODUCT_NAME'], $this->arrayHasKey('PRIMARY_POSITION'));

        $this->assertEquals('ZFPRODUCTS',          $desc['PRODUCT_NAME']['TABLE_NAME']);
        $this->assertEquals('PRODUCT_NAME',      $desc['PRODUCT_NAME']['COLUMN_NAME']);
        $this->assertEquals(2,                   $desc['PRODUCT_NAME']['COLUMN_POSITION']);
        $this->assertRegExp('/varchar/i',        $desc['PRODUCT_NAME']['DATA_TYPE']);
        $this->assertEquals('',                  $desc['PRODUCT_NAME']['DEFAULT']);
        $this->assertTrue(                       $desc['PRODUCT_NAME']['NULLABLE']);
        $this->assertEquals(0,                   $desc['PRODUCT_NAME']['SCALE']);
        $this->assertEquals(0,                   $desc['PRODUCT_NAME']['PRECISION']);
        $this->assertFalse(                      $desc['PRODUCT_NAME']['PRIMARY']);
        $this->assertEquals('',                  $desc['PRODUCT_NAME']['PRIMARY_POSITION']);

        $this->assertTrue(                       $desc['PRODUCT_ID']['PRIMARY'], 'Expected product_id to be a primary key');
        $this->assertEquals(1,                   $desc['PRODUCT_ID']['PRIMARY_POSITION']);
    }

    public function testAdapterDelete()
    {
        $select = $this->_db->select()->from('zfproducts')->order('product_id ASC');

        $result = $this->_db->fetchAll($select);

        $this->assertEquals(3, count($result), 'Expected count of result to be 2');
        $this->assertEquals(1, $result[0]['PRODUCT_ID'], 'Expecting product_id of 0th row to be 1');

        $rowsAffected = $this->_db->delete('zfproducts', 'product_id = 2');
        $this->assertEquals(1, $rowsAffected, 'Expected rows affected to return 1', 'Expecting rows affected to be 1');

        $select = $this->_db->select()->from('zfproducts')->order('product_id ASC');
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(2, count($result), 'Expected count of result to be 2');
        $this->assertEquals(1, $result[0]['PRODUCT_ID'], 'Expecting product_id of 0th row to be 1');

        $rowsAffected = $this->_db->delete('zfproducts', 'product_id = 327');
        $this->assertEquals(0, $rowsAffected, 'Expected rows affected to return 0');
    }

    public function testAdapterUpdate()
    {
        // Test that we can change the values in
        // an existing row.
        $result = $this->_db->update(
            'zfproducts',
            array('PRODUCT_NAME' => 'Vista'),
            'PRODUCT_ID = 1'
        );
        $this->assertEquals(1, $result);

        // Query the row to see if we have the new values.
        $select = $this->_db->select();
        $select->from('zfproducts');
        $select->where('product_id = 1');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();

        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
        $this->assertEquals('Vista', $result[0]['PRODUCT_NAME']);

        // Test that update affects no rows if the WHERE
        // clause matches none.
        $result = $this->_db->update(
            'zfproducts',
            array('PRODUCT_NAME' => 'Vista'),
            'PRODUCT_ID= 327'
        );
        $this->assertEquals(0, $result);
    }


   /**
     * Test the Adapter's fetchAll() method.
     */
    public function testAdapterFetchAll()
    {
        $result = $this->_db->fetchAll('SELECT * FROM zfproducts WHERE product_id > :id ORDER BY product_id ASC', array(":id"=>1));
        $this->assertEquals(2, count($result));
        $this->assertEquals('2', $result[0]['PRODUCT_ID']);
    }

    /**
     * Test the Adapter's fetchAssoc() method.
     */
    public function testAdapterFetchAssoc()
    {
        $result = $this->_db->fetchAssoc('SELECT * FROM zfproducts WHERE product_id > :id ORDER BY product_id DESC', array(":id"=>1));
        foreach ($result as $idKey => $row) {
            $this->assertEquals($idKey, $row['PRODUCT_ID']);
        }
    }

    /**
     * Test the Adapter's fetchCol() method.
     */
    public function testAdapterFetchCol()
    {
        $result = $this->_db->fetchCol('SELECT * FROM zfproducts WHERE product_id > :id ORDER BY product_id ASC', array(":id"=>1));
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(3, $result[1]);
    }

    /**
     * Test the Adapter's fetchOne() method.
     */
    public function testAdapterFetchOne()
    {
        $prod = 'Linux';
        $result = $this->_db->fetchOne('SELECT PRODUCT_NAME FROM zfproducts WHERE product_id > :id ORDER BY product_id', array(":id"=>1));
        $this->assertEquals($prod, $result);
    }

    /**
     * Test the Adapter's fetchPairs() method.
     */
    public function testAdapterFetchPairs()
    {
        $prod = 'Linux';
        $result = $this->_db->fetchPairs('SELECT product_id, PRODUCT_NAME FROM zfproducts WHERE product_id > :id ORDER BY product_id ASC', array(":id"=>1));
        $this->assertEquals(2, count($result)); // count rows
        $this->assertEquals($prod, $result[2]);
    }

    /**
     * Test the Adapter's fetchRow() method.
     */
    public function testAdapterFetchRow()
    {
        $result = $this->_db->fetchRow('SELECT * FROM zfproducts WHERE product_id > :id ORDER BY product_id', array(":id"=>1));
        $this->assertEquals(2, count($result)); // count columns
        $this->assertEquals(2, $result['PRODUCT_ID']);
    }

    public function testAdapterInsert()
    {
        $row = array (
            'PRODUCT_ID' => '4',
            'PRODUCT_NAME' => 'Solaris',
        );
        $rowsAffected = $this->_db->insert('zfproducts', $row);
        $this->assertEquals(1, $rowsAffected);
		/*
		 * this is not MySQL
        $id = $this->_db->lastInsertId();
        $this->assertEquals('4', (string) $id, 'Expected new id to be 4');
		*/
    }

}
