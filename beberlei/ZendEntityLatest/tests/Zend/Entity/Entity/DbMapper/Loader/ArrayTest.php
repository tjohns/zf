<?php

class Zend_Entity_DbMapper_Loader_ArrayTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getLoaderClassName()
    {
        return "Zend_Db_Mapper_Loader_Array";
    }

    public function getFixtureClassName()
    {
        return "Zend_Entity_Fixture_SimpleFixtureDefs";
    }

    public function testProcessResultset_SimpleFixture()
    {
        $loader = $this->createLoader();
        $row = $this->fixture->getDummyDataRow();
        $state = $this->fixture->getDummyDataState();

        $resultSet = array($row);

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS, "a");
        $rsm->addProperty("a", "a_id", "id");
        $rsm->addProperty("a", "a_property", "property");

        $array = $loader->processResultset($resultSet, $rsm);

        $this->assertTrue(is_array($array));
        $this->assertEquals(1, count($array));

        $this->assertEquals($state, $array[0]);
    }

    public function testNoEntityGiven_ThrowsException()
    {
        $loader = $this->createLoader();

        $this->setExpectedException("Zend_Entity_Exception");

        $rsm = new Zend_Entity_Query_ResultSetMapping();

        $loader->processResultset(array(), $rsm);
    }

    public function testProcessResultSet_SimpleFixtureWithScalars()
    {
        $expected = array(
            0 => array(
                "id" => 1,
                "property" => "foo",
            ),
            "foo" => "bar",
        );

        $loader = $this->createLoader();
        $row = $this->fixture->getDummyDataRow();

        $resultSet = array( array_merge($row, array('foo' => 'bar')) );

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity(Zend_Entity_Fixture_SimpleFixtureDefs::TEST_A_CLASS, "a");
        $rsm->addProperty("a", "a_id", "id");
        $rsm->addProperty("a", "a_property", "property");
        $rsm->addScalar("foo");

        $array = $loader->processResultset($resultSet, $rsm);

        $this->assertEquals(1, count($array));
        $this->assertEquals($expected, $array[0]);
    }

    public function testProcessResultset_JoinedEntityWithoutReference_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Query_InvalidResultSetMappingException");

        $this->initFixture("Zend_Entity_Fixture_ManyToOneDefs");

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');
        $rsm->addJoinedEntity('Zend_TestEntity2', 'b')
            ->addProperty('b', 'b_id', 'id')
            ->addProperty('b', 'b_property', 'property');

        $rows = array(
            array(
                'a_id' => '1',
                'a_property' => 'foo',
                'a_manytoone' => '1',
                'b_id' => '1',
                'b_property' => 'baz',
            )
        );

        $loader = $this->createLoader();
        $result = $loader->processResultset($rows, $rsm);
    }

    public function testProcessResultset_ManyToOneFixture_JoinedEntity()
    {
        $this->initFixture("Zend_Entity_Fixture_ManyToOneDefs");

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'a')
            ->addProperty('a', 'a_id', 'id')
            ->addProperty('a', 'a_property', 'property')
            ->addProperty('a', 'a_manytoone', 'manytoone');
        $rsm->addJoinedEntity('Zend_TestEntity2', 'b', 'a', 'manytoone')
            ->addProperty('b', 'b_id', 'id')
            ->addProperty('b', 'b_property', 'property');

        $rows = array(
            array(
                'a_id' => '1',
                'a_property' => 'foo',
                'a_manytoone' => '1',
                'b_id' => '1',
                'b_property' => 'baz',
            ),
            array(
                'a_id' => '2',
                'a_property' => 'bar',
                'a_manytoone' => '1',
                'b_id' => '1',
                'b_property' => 'baz',
            )
        );

        $expectedResult = array(
            array(
                'id' => 1,
                'property' => 'foo',
                'manytoone' => array(
                    'id' => 1,
                    'property' => 'baz',
                ),
            ),
            array(
                'id' => 2,
                'property' => 'bar',
                'manytoone' => array(
                    'id' => 1,
                    'property' => 'baz',
                ),
            ),
        );

        $loader = $this->createLoader();
        $result = $loader->processResultset($rows, $rsm);

        $this->assertSame($expectedResult, $result);
    }
}