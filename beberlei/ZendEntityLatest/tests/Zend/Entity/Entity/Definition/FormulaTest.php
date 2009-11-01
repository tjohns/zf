<?php

class Zend_Entity_Definition_FormulaTest extends PHPUnit_Framework_TestCase
{
    public function testSetGetSqlExpr()
    {
        $formula = new Zend_Entity_Definition_Formula("name1");
        $formula->setSqlExpr("now");
        $this->assertTrue($formula->getSqlExpr() instanceof Zend_Db_Expr);
        $this->assertEquals("now", (string)$formula->getSqlExpr());
    }

    public function testGetSqlColumnValueReturnsZendDbExprFormula()
    {
        $formula = new Zend_Entity_Definition_Formula("name1");
        $formula->setSqlExpr("now");
        $this->assertTrue($formula->getColumnSqlName() instanceof Zend_Db_Expr);
        $this->assertEquals("now", (string)$formula->getSqlExpr());
        $this->assertEquals($formula->getSqlExpr(), $formula->getColumnSqlName());
    }
}