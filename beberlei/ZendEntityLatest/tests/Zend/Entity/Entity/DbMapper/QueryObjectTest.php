<?php

class Zend_Entity_DbMapper_QueryObjectTest extends PHPUnit_Framework_TestCase
{
    public function testJoinWithEmptyColumnsDoesNotUseWildcard()
    {
        $db = new Zend_Test_DbAdapter();
        $select = new Zend_Db_Mapper_QueryObject($db);
        $select->from("foo", array("propA", "propB"))
               ->join("bar", "foo.propA = bar.propC");

        $sql = preg_replace('/(\s)/', ' ', $select->assemble());
        $this->assertEquals(
            "SELECT foo.propA, foo.propB FROM foo  INNER JOIN bar ON foo.propA = bar.propC",
            $sql
        );
    }
}