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

require_once 'Zend/Db/Statement/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Statement_OracleTest extends Zend_Db_Statement_TestCommon
{

    public function testStatementConstruct()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $sql = $select->__toString();
        $stmt = new Zend_Db_Statement_Oracle($this->_db, $sql);
        $this->assertType('Zend_Db_Statement_Oracle', $stmt);
    }

    public function testStatementErrorCodeKeyViolation()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not return error codes correctly.');
    }

    public function testStatementErrorInfoKeyViolation()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not return error codes correctly.');
    }

    public function testStatementExecuteWithParams()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:product_id, :product_name)");
        $stmt->execute(array('product_id' => 4, 'product_name' => 'Solaris'));

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 4");
        $result = $this->_db->fetchAll($select);
        $stmt->closeCursor();

        $this->assertEquals(array(array('product_id'=>4, 'product_name'=>'Solaris')), $result);
    }

    public function testStatementFetchAllStyleBoth()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_BOTH);

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(2, $result[0][0]);
        $this->assertEquals('Linux', $result[0][1]);
        $this->markTestIncomplete($this->getDriver() . ' does not implement FETCH_BOTH correctly.');
        // $this->assertEquals(2, $result[0]['product_id']);
        // $this->assertEquals('Linux', $result[0]['product_name']);
    }

    public function testStatementNextRowset()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = $this->_db->prepare($select->__toString());
        try {
            $stmt->nextRowset();
            $this->fail('Expected to catch Zend_Db_Statement_Oracle_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Oracle_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Oracle_Exception, got '.get_class($e));
            $this->assertEquals('HYC00 Optional feature not implemented', $e->getMessage());
        }
        $stmt->closeCursor();
    }

    public function testStatementBindParamByPosition()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support bound parameters by position');
    }

    public function testStatementBindValueByPosition()
    {
        $this->markTestSkipped($this->getDriver() . ' does not support bound parameters by position');
    }

    public function testStatementBindColumnByPosition()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not support FETCH_BOUND yet');
    }

    public function testStatementBindColumnByName()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not support FETCH_BOUND yet');
    }

    public function getDriver()
    {
        return 'Oracle';
    }

}
