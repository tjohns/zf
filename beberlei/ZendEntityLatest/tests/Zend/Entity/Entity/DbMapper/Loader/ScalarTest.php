<?php

class Zend_Entity_DbMapper_Loader_ScalarTest
    extends Zend_Entity_DbMapper_Loader_TestCase
{
    
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Scalar";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_OneToManyDefs";
    }

    public function testLoadSimpleEntity()
    {
        $loader = $this->createLoader();

        $rows = array(
            array("a_id" => "100"),
            array("a_id" => "200"),
        );

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "t")
            ->addProperty("t", "a_id", "id");

        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(2, count($result));
        $this->assertSame(array("id" => 100), $result[0]);
        $this->assertSame(array("id" => 200), $result[1]);
    }

    public function testLoadDontFilterDuplicates()
    {
        $loader = $this->createLoader();

        $rows = array(
            array("a_id" => "100"),
            array("a_id" => "100"),
        );

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "t")
            ->addProperty("t", "a_id", "id");

        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(2, count($result));
        $this->assertSame(array("id" => 100), $result[0]);
        $this->assertSame(array("id" => 100), $result[1]);
    }

    public function testLoadSimpleEntityWithScalars()
    {
        $loader = $this->createLoader();

        $rows = array(
            array("a_id" => "100", "scalarA" => "foo", "scalarB" => "bar"),
        );

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "t")
            ->addProperty("t", "a_id", "id")
            ->addScalar("scalarA")
            ->addScalar("scalarB");

        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(1, count($result));
        $this->assertSame(array(
            "id" => 100, "scalarA" => "foo", "scalarB" => "bar",
         ), $result[0]);
    }

    public function testLoadEntityWithHasManyEntities_MultipleSameNameKeys_LastOneWins()
    {
        $loader = $this->createLoader();

        $rows = array(
            array("a_id" => "100", "b_id" => "1", "b_fkey" => "100"),
            array("a_id" => "100", "b_id" => "2", "b_fkey" => "100"),
            array("a_id" => "100", "b_id" => "3", "b_fkey" => "100"),
        );

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Zend_TestEntity1", "t")
            ->addProperty("t", "a_id", "id")
            ->addProperty("t", "b_fkey", "onetomany")
            ->addJoinedEntity("Zend_TestEntity2", "b")
            ->addProperty("b", "b_id", "id");

        $result = $loader->processResultset($rows, $rsm);

        $this->assertEquals(3, count($result));
        $this->assertSame( array(
                array('id' => 1, 'onetomany' => '100'),
                array('id' => 2, 'onetomany' => '100'),
                array('id' => 3, 'onetomany' => '100'),
            ), $result);
    }
}
