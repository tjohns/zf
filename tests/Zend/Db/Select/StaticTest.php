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

class Zend_Db_Select_StaticTest extends Zend_Db_Select_TestCommon
{
    /**
     * Test basic use of the Zend_Db_Select class.
     */

    public function testSelect()
    {
        $select = $this->_select();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM\\s+"products"/', $select->__toString()); }

    /**
     * Test basic use of the Zend_Db_Select class.
     */
    public function testSelectQuery()
    {
        $select = $this->_select();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM\\s+"products"/', $select->__toString()); $stmt = $select->query();
        Zend_Loader::loadClass('Zend_Db_Statement_Static');
        $this->assertType('Zend_Db_Statement_Static', $stmt);
    }

    /**
     * Test Zend_Db_Select specifying columns
     */

    public function testSelectColumnsScalar()
    {
        $select = $this->_selectColumnsScalar();
        $this->assertRegexp('/SELECT\\s+"products"."product_name"\\s+FROM "products"/', $select->__toString()); }


    public function testSelectColumnsArray()
    {
        $select = $this->_selectColumnsArray();
        $this->assertRegexp('/SELECT\\s+"products"."product_id",\\s+"products"."product_name"\\s+FROM "products"/', $select->__toString()); }

    /**
     * Test support for column aliases.
     * e.g. from('table', array('alias' => 'col1')).
     */

    public function testSelectColumnsAliases()
    {
        $select = $this->_selectColumnsAliases();
        $this->assertRegexp('/SELECT\\s+"products"."product_name" AS "alias"\\s+FROM "products"/', $select->__toString());
    }

    /**
     * Test syntax to support qualified column names,
     * e.g. from('table', array('table.col1', 'table.col2')).
     */

    public function testSelectColumnsQualified()
    {
        $select = $this->_selectColumnsQualified();
        $this->assertRegexp('/SELECT\\s+"products"."product_name"\\s+FROM "products"/', $select->__toString());
    }

    /**
     * Test support for columns defined by Zend_Db_Expr.
     */

    public function testSelectColumnsExpr()
    {
        $select = $this->_selectColumnsExpr();
        $this->assertRegexp('/SELECT\\s+products.product_name\\s+FROM "products"/', $select->__toString());
    }

    /**
     * Test support for automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. from('table', array('COUNT(*)'))
     * should generate the same result as
     * from('table', array(new Zend_Db_Expr('COUNT(*)')))
     */

    public function testSelectColumnsAutoExpr()
    {
        $select = $this->_selectColumnsAutoExpr();
        $this->assertRegexp('/SELECT\\s+COUNT\\(.\\) AS "count"\\s+FROM "products"/', $select->__toString());
    }

    /**
     * Test adding the DISTINCT query modifier to a Zend_Db_Select object.
     */

    public function testSelectDistinctModifier()
    {
        $select = $this->_selectDistinctModifier();
        $this->assertRegexp('/SELECT DISTINCT\\s+327\\s+FROM "products"/', $select->__toString());
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     *
    public function testSelectForUpdateModifier()
    {
    }
     */

    /**
     * Test support for schema-qualified table names in from()
     * e.g. from('schema.table').
     */

    public function testSelectFromQualified()
    {
        $select = $this->_selectFromQualified();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "dummy"."products"/', $select->__toString());
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */

    public function testSelectJoin()
    {
        $select = $this->_selectJoin();
        $this->assertRegexp('/SELECT\\s+"products".*,\\s+"bugs_products".*\\s+FROM "products"\\s+INNER JOIN "bugs_products" ON products.product_id = bugs_products.product_id/', $select->__toString());
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */

    public function testSelectJoinWithCorrelationName()
    {
        $select = $this->_selectJoinWithCorrelationName();
        $this->assertRegexp('/SELECT\\s+"xyz1".*,\\s+"xyz2".*\\s+FROM "products" AS "xyz1"\\s+INNER JOIN "bugs_products" AS "xyz2" ON xyz1.product_id = xyz2.product_id\\s+WHERE\\s+\\(xyz1.product_id = 1\\)/', $select->__toString());
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */

    public function testSelectJoinInner()
    {
        $select = $this->_selectJoinInner();
        $this->assertRegexp('/SELECT\\s+"products".*,\\s+"bugs_products".*\\s+FROM "products"\\s+INNER JOIN "bugs_products" ON products.product_id = bugs_products.product_id/', $select->__toString());
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */

    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $this->assertRegexp('/SELECT\\s+"bugs".*,\\s+"bugs_products".*\\s+FROM "bugs"\\s+LEFT JOIN "bugs_products" ON bugs.bug_id = bugs_products.bug_id/', $select->__toString());
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */

    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $this->assertRegexp('/SELECT\\s+"bugs_products".*,\\s+"bugs".*\\s+FROM "bugs_products"\\s+RIGHT JOIN "bugs" ON bugs_products.bug_id = bugs.bug_id/', $select->__toString());
    }

    /**
     * Test adding a cross join to a Zend_Db_Select object.
     */

    public function testSelectJoinCross()
    {
        $select = $this->_selectJoinCross();
        $this->assertRegexp('/SELECT\\s+"products".*,\\s+"bugs_products".*\\s+FROM "products"\\s+CROSS JOIN "bugs_products"/', $select->__toString());
    }

    /**
     * Test support for schema-qualified table names in join(),
     * e.g. join('schema.table', 'condition')
     */

    public function testSelectJoinQualified()
    {
        $select = $this->_selectJoinQualified();
        $this->assertRegexp('/SELECT\\s+"products".*,\\s+"bugs_products".*\\s+FROM "products"\\s+INNER JOIN "dummy"."bugs_products" ON products.product_id = bugs_products.product_id/', $select->__toString());
    }

    /**
     * Test adding a WHERE clause to a Zend_Db_Select object.
     */

    public function testSelectWhere()
    {
        $select = $this->_selectWhere();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+WHERE\\s+\\(product_id = 2\\)/', $select->__toString());
    }

    /**
     * test adding more WHERE conditions,
     * which should be combined with AND by default.
     */

    public function testSelectWhereAnd()
    {
        $select = $this->_selectWhereAnd();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+WHERE\\s+\\(product_id = 2\\)\\s+AND \\(product_id = 1\\)/', $select->__toString());
    }

    /**
     * Test support for where() with a parameter,
     * e.g. where('id = ?', 1).
     */

    public function testSelectWhereWithParameter()
    {
        $select = $this->_selectWhereWithParameter();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+WHERE\\s+\\(product_id = 2\\)/', $select->__toString());
    }

    /**
     * Test adding an OR WHERE clause to a Zend_Db_Select object.
     */

    public function testSelectWhereOr()
    {
        $select = $this->_selectWhereOr();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+WHERE\\s+\\(product_id = 1\\)\\s+OR \\(product_id = 2\\)/', $select->__toString());
    }

    /**
     * Test support for where() with a parameter,
     * e.g. orWhere('id = ?', 2).
     */

    public function testSelectWhereOrWithParameter()
    {
        $select = $this->_selectWhereOrWithParameter();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+WHERE\\s+\\(product_id = 1\\)\\s+OR \\(product_id = 2\\)/', $select->__toString());
    }

    /**
     * Test adding a GROUP BY clause to a Zend_Db_Select object.
     */

    public function testSelectGroupBy()
    {
        $select = $this->_selectGroupBy();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) as thecount\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bug_id"\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test support for qualified table in group(),
     * e.g. group('schema.table').
     */

    public function testSelectGroupByQualified()
    {
        $select = $this->_selectGroupByQualified();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) as thecount\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bugs_products"."bug_id"\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test support for Zend_Db_Expr in group(),
     * e.g. group(new Zend_Db_Expr('id+1'))
     */

    public function testSelectGroupByExpr()
    {
        $select = $this->_selectGroupByExpr();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) as thecount\\s+FROM "bugs_products"\\s+GROUP BY\\s+bug_id\\+1\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test support for automatic conversion of a SQL
     * function to a Zend_Db_Expr in group(),
     * e.g.  group('LOWER(title)') should give the same
     * result as group(new Zend_Db_Expr('LOWER(title)')).
     */


    public function testSelectGroupByAutoExpr()
    {
        $select = $this->_selectGroupByAutoExpr();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) as thecount\\s+FROM "bugs_products"\\s+GROUP BY\\s+ABS\\("bugs_products"."bug_id"\\)\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */

    public function testSelectHaving()
    {
        $select = $this->_selectHaving();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) AS "thecount"\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bug_id"\\s+HAVING\\s+\\(count\\(.\\) > 1\\)\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }


    public function testSelectHavingAnd()
    {
        $select = $this->_selectHavingAnd();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) AS "thecount"\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bug_id"\\s+HAVING\\s+\\(count\\(.\\) > 1\\)\\s+AND \\(count\\(.\\) = 1\\)\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test support for parameter in having(),
     * e.g. having('count(*) > ?', 1).
     */


    public function testSelectHavingWithParameter()
    {
        $select = $this->_selectHavingWithParameter();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) AS "thecount"\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bug_id"\\s+HAVING\\s+\\(count\\(.\\) > 1\\)\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */


    public function testSelectHavingOr()
    {
        $select = $this->_selectHavingOr();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) AS "thecount"\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bug_id"\\s+HAVING\\s+\\(count\\(.\\) > 1\\)\\s+OR \\(count\\(.\\) = 1\\)\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test support for parameter in orHaving(),
     * e.g. orHaving('count(*) > ?', 1).
     */

    public function testSelectHavingOrWithParameter()
    {
        $select = $this->_selectHavingOrWithParameter();
        $this->assertRegexp('/SELECT\\s+"bugs_products"."bug_id",\\s+count\\(.\\) AS "thecount"\\s+FROM "bugs_products"\\s+GROUP BY\\s+"bug_id"\\s+HAVING\\s+\\(count\\(.\\) > 1\\)\\s+OR \\(count\\(.\\) = 1\\)\\s+ORDER BY\\s+"bug_id" ASC/', $select->__toString());
    }

    /**
     * Test adding an ORDER BY clause to a Zend_Db_Select object.
     */

    public function testSelectOrderBy()
    {
        $select = $this->_selectOrderBy();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+"product_id" ASC/', $select->__toString());
    }


    public function testSelectOrderByArray()
    {
        $select = $this->_selectOrderByArray();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+"product_id" ASC,\\s+"product_id" ASC/', $select->__toString());
    }


    public function testSelectOrderByAsc()
    {
        $select = $this->_selectOrderByAsc();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+"product_id" ASC/', $select->__toString());
    }


    public function testSelectOrderByDesc()
    {
        $select = $this->_selectOrderByDesc();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+"product_id" DESC/', $select->__toString());
    }

    /**
     * Test support for qualified table in order(),
     * e.g. order('schema.table').
     */

    public function testSelectOrderByQualified()
    {
        $select = $this->_selectOrderByQualified();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+"products"."product_id" ASC/', $select->__toString());
    }

    /**
     * Test support for Zend_Db_Expr in order(),
     * e.g. order(new Zend_Db_Expr('id+1')).
     */

    public function testSelectOrderByExpr()
    {
        $select = $this->_selectOrderByExpr();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+1/', $select->__toString());
    }

    /**
     * Test automatic conversion of SQL functions to 
     * Zend_Db_Expr, e.g. order('LOWER(title)')
     * should give the same result as
     * order(new Zend_Db_Expr('LOWER(title)')).
     */

    public function testSelectOrderByAutoExpr()
    {
        $select = $this->_selectOrderByAutoExpr();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+ORDER BY\\s+ABS\\("products"."product_id"\\) ASC/', $select->__toString());
    }

    /**
     * Test adding a LIMIT clause to a Zend_Db_Select object.
     */

    public function testSelectLimit()
    {
        $select = $this->_selectLimit();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+LIMIT 1 OFFSET 0/', $select->__toString());
    }


    public function testSelectLimitNone()
    {
        $select = $this->_selectLimitNone();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"/', $select->__toString());
    }


    public function testSelectLimitOffset()
    {
        $select = $this->_selectLimitOffset();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+LIMIT 1 OFFSET 1/', $select->__toString());
    }

    /**
     * Test the limitPage() method of a Zend_Db_Select object.
     */

    public function testSelectLimitPageOne()
    {
        $select = $this->_selectLimitPageOne();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+LIMIT 1 OFFSET 0/', $select->__toString());
    }


    public function testSelectLimitPageTwo()
    {
        $select = $this->_selectLimitPageTwo();
        $this->assertRegexp('/SELECT\\s+"products".*\\s+FROM "products"\\s+LIMIT 1 OFFSET 1/', $select->__toString());
    }

    public function getDriver()
    {
        return 'Static';
    }

}
