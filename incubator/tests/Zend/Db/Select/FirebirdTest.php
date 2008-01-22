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

require_once 'Zend/Db/Select/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

class Zend_Db_Select_FirebirdTest extends Zend_Db_Select_TestCommon
{

    protected function _selectOrderByAutoExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("UPPER($products.$product_id)");
        return $select;
    }
    
    protected function _selectGroupByAutoExpr()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id'=>"UPPER($bugs_products.$bug_id)", new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group("UPPER($bugs_products.$bug_id)")
            ->order("UPPER($bugs_products.$bug_id)");
        return $select;
    }

    public function testSelectFromQualified()
    {
        $this->markTestSkipped($this->getDriver() . ' does not report its schema as we expect.');
    }    
    
    public function testSelectJoinQualified()
    {
        $this->markTestSkipped($this->getDriver() . ' does not report its schema as we expect.');
    }    
    
    public function testSelectJoin()
    {
        $select = $this->_selectJoin();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(4, count($result[0]));
    }    
    
    public function testSelectJoinInner()
    {
        $select = $this->_selectJoinInner();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(4, count($result[0]));
    }    
    
    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(10, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }    
    
    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(10, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }    
    
    public function testSelectJoinCross()
    {
        $select = $this->_selectJoinCross();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(18, count($result));
        $this->assertEquals(4, count($result[0]));
    }    
    
    public function testSelectJoinWithCorrelationName()
    {
        $select = $this->_selectJoinWithCorrelationName();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(4, count($result[0]));
    }
    
    
    public function getDriver()
    {
        return 'Firebird';
    }

}
