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

class Zend_Db_Select_OracleTest extends Zend_Db_Select_TestCommon
{

    public function testSelectFromQualified()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not report its schema as we expect.');
    }

    public function testSelectJoinQualified()
    {
        $this->markTestIncomplete($this->getDriver() . ' does not report its schema as we expect.');
    }

    public function getDriver()
    {
        return 'Oracle';
    }
   
    public function testSelect()
    {
        $select = $this->_select();
        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $this->_db->query($select);
        $row = $stmt->fetch();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['PRODUCT_ID']); // correct data
    }

    public function testSelectQuery()
    {
        $select = $this->_select();
        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $select->query();
        $row = $stmt->fetch();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['PRODUCT_ID']); // correct data
    }

    /**
     * Test Zend_Db_Select specifying columns
     */
    protected function _selectColumnsScalar()
    {
        $select = $this->_db->select()
            ->from('zfproducts', 'product_name'); // scalar
        return $select;
    }

    public function testSelectColumnsScalar()
    {
        $select = $this->_selectColumnsScalar();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertEquals(1, count($result[0]), 'Expected column count of result set to be 1');
        $this->assertThat($result[0], $this->arrayHasKey('PRODUCT_NAME'));
    }

    public function testSelectColumnsArray()
    {
        $select = $this->_selectColumnsArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertEquals(2, count($result[0]), 'Expected column count of result set to be 2');
        $this->assertThat($result[0], $this->arrayHasKey('PRODUCT_ID'));
        $this->assertThat($result[0], $this->arrayHasKey('PRODUCT_NAME'));
    }

    public function testSelectColumnsAliases()
    {
        $select = $this->_selectColumnsAliases();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertThat($result[0], $this->arrayHasKey('ALIAS'));
        $this->assertThat($result[0], $this->logicalNot($this->arrayHasKey('PRODUCT_NAME')));
    }

    public function testSelectColumnsQualified()
    {
        $select = $this->_selectColumnsQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('PRODUCT_NAME'));
    }

    public function testSelectColumnsExpr()
    {
        $select = $this->_selectColumnsExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('PRODUCT_NAME'));
    }
	
    public function testSelectColumnsAutoExpr()
    {
        $select = $this->_selectColumnsAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('COUNT'));
        $this->assertEquals(3, $result[0]['COUNT']);
    }

    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(9, count($result[0]));
        $this->assertEquals(3, $result[3]['PRODUCT_ID']);
        $this->assertNull($result[6]['PRODUCT_ID']);
    }

    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(9, count($result[0]));
        $this->assertEquals(3, $result[3]['PRODUCT_ID']);
        $this->assertNull($result[6]['PRODUCT_ID']);
    }

    public function testSelectWhere()
    {
        $select = $this->_selectWhere();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['PRODUCT_ID']);
    }

    public function testSelectWhereWithParameter()
    {
        $select = $this->_selectWhereWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['PRODUCT_ID']);
    }

    public function testSelectWhereOr()
    {
        $select = $this->_selectWhereOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
        $this->assertEquals(2, $result[1]['PRODUCT_ID']);
    }

    public function testSelectWhereOrWithParameter()
    {
        $select = $this->_selectWhereOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
        $this->assertEquals(2, $result[1]['PRODUCT_ID']);
    }

    public function testSelectGroupBy()
    {
        $select = $this->_selectGroupBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['BUG_ID']);
        $this->assertEquals(1, $result[1]['THECOUNT']);
    }

    public function testSelectGroupByQualified()
    {
        $select = $this->_selectGroupByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['BUG_ID']);
        $this->assertEquals(1, $result[1]['THECOUNT']);
    }

    public function testSelectGroupByExpr()
    {
        $select = $this->_selectGroupByExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(2, $result[0]['BUG_ID'],
            'Expected first bug_id to be 2');
        $this->assertEquals(3, $result[0]['THECOUNT'],
            'Expected count(*) of first group to be 2');
        $this->assertEquals(3, $result[1]['BUG_ID'],
            'Expected second bug_id to be 3');
        $this->assertEquals(1, $result[1]['THECOUNT'],
            'Expected count(*) of second group to be 1');
    }

    public function testSelectGroupByAutoExpr()
    {
        $select = $this->_selectGroupByAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT'], 'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['BUG_ID']);
        $this->assertEquals(1, $result[1]['THECOUNT']);
    }

    public function testSelectHaving()
    {
        $select = $this->_selectHaving();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT']);
    }

    public function testSelectHavingWithParameter()
    {
        $select = $this->_selectHavingWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT']);
    }

    public function testSelectHavingOr()
    {
        $select = $this->_selectHavingOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT']);
        $this->assertEquals(2, $result[1]['BUG_ID']);
        $this->assertEquals(1, $result[1]['THECOUNT']);
    }

    public function testSelectHavingOrWithParameter()
    {
        $select = $this->_selectHavingOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['BUG_ID']);
        $this->assertEquals(3, $result[0]['THECOUNT']);
        $this->assertEquals(2, $result[1]['BUG_ID']);
        $this->assertEquals(1, $result[1]['THECOUNT']);
    }

    public function testSelectOrderBy()
    {
        $select = $this->_selectOrderBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectOrderByArray()
    {
        $select = $this->_selectOrderByArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectOrderByAsc()
    {
        $select = $this->_selectOrderByAsc();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectOrderByDesc()
    {
        $select = $this->_selectOrderByDesc();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(3, $result[0]['PRODUCT_ID']);
    }

    public function testSelectOrderByQualified()
    {
        $select = $this->_selectOrderByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectOrderByExpr()
    {
        $select = $this->_selectOrderByExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectOrderByAutoExpr()
    {
        $select = $this->_selectOrderByAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectLimit()
    {
        $select = $this->_selectLimit();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectLimitOffset()
    {
        $select = $this->_selectLimitOffset();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['PRODUCT_ID']);
    }

    public function testSelectLimitPageOne()
    {
        $select = $this->_selectLimitPageOne();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['PRODUCT_ID']);
    }

    public function testSelectLimitPageTwo()
    {
        $select = $this->_selectLimitPageTwo();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['PRODUCT_ID']);
    }

}
