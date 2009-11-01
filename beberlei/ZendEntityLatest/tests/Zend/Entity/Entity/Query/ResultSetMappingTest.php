<?php

class Zend_Entity_Query_ResultSetMappingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Entity_Query_ResultSetMapping
     */
    protected $rsm;

    public function setUp()
    {
        $this->rsm = new Zend_Entity_Query_ResultSetMapping();
    }

    public function testAddEntity()
    {
        $this->rsm->addEntity('Foo');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']['properties']));
    }

    public function testAddEntity_IsFluent()
    {
        $this->assertSame($this->rsm, $this->rsm->addEntity('Foo'));
    }

    public function testAddProperty_ToNonExistantEntity_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $this->rsm->addProperty('unknownEntity', 'foo', 'bar');
    }

    public function testAddPropertyToEntity()
    {
        $this->rsm->addEntity('Foo');
        $this->rsm->addProperty('Foo', 'foo', 'bar');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']['properties']['foo']));
        $this->assertEquals('bar', $this->rsm->entityResult['Foo']['properties']['foo']);
    }

    public function testAddProperty_ToEntity_WhichHasAlias_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $this->rsm->addEntity('Foo', 'f');
        $this->rsm->addProperty('Foo', 'foo', 'bar');
    }

    public function testAddProperty_ToEntityAlias()
    {
        $this->rsm->addEntity('Foo', 'f');
        $this->rsm->addProperty('f', 'foo', 'bar');

        $this->assertTrue(isset($this->rsm->entityResult['f']['properties']['foo']));
        $this->assertEquals('bar', $this->rsm->entityResult['f']['properties']['foo']);
    }

    public function testAddJoinedEntity_AndProperty()
    {
        $this->rsm->addJoinedEntity('Foo', null, null, null);
        $this->rsm->addProperty('Foo', 'bar', 'baz');

        $this->assertTrue(isset($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']));
        $this->assertTrue(is_array($this->rsm->entityResult['Foo']['properties']));

        $this->assertTrue(isset($this->rsm->entityResult['Foo']['properties']['bar']));
        $this->assertEquals('baz', $this->rsm->entityResult['Foo']['properties']['bar']);
    }

    public function testAddJoinedEntity_AndBindToParentAlias()
    {
        $this->rsm->addJoinedEntity('Foo', 'f', 'a', 'foo');

        $this->assertArrayHasKey('f', $this->rsm->joinedEntity, "ResultSetMapping should contain joined entity alias with name f.");
        $this->assertArrayHasKey('parentAlias', $this->rsm->joinedEntity['f'], "ResultSetMapping joined entity with alias f should have parentAlias key.");
        $this->assertArrayHasKey('parentProperty', $this->rsm->joinedEntity['f'], "ResultSetMapping joined entity with alias f should have parentEntityProperty key.");
        $this->assertEquals(array('parentAlias' => 'a', 'parentProperty' => 'foo'), $this->rsm->joinedEntity['f']);
    }

    public function testAddJoinedEntity_WithAlias_AndProperty()
    {
        $this->rsm->addJoinedEntity('Foo', 'f', null, null);
        $this->rsm->addProperty('f', 'bar', 'baz');

        $this->assertTrue(isset($this->rsm->entityResult['f']));
        $this->assertTrue(is_array($this->rsm->entityResult['f']));
        $this->assertTrue(is_array($this->rsm->entityResult['f']['properties']));

        $this->assertTrue(isset($this->rsm->entityResult['f']['properties']['bar']));
        $this->assertEquals('baz', $this->rsm->entityResult['f']['properties']['bar']);
    }

    /**
     *
     * @return Zend_Entity_Query_ResultSetMapping
     */
    public function createMultipleJoinedEntityRsm()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Bug", "b")
            ->addProperty("b", "bug_id", "id")
            ->addProperty("b", "bug_description", "description")
            ->addProperty("b", "bug_created", "created")
            ->addProperty("b", "bug_status", "status")
            ->addProperty("b", "reported_by", "reporter")
            ->addProperty("b", "assigned_to", "engineer")
            ->addJoinedEntity("User", "r")
            ->addProperty("r", "reporter_id", "id")
            ->addProperty("r", "reporter_name", "name")
            ->addJoinedEntity("User", "e")
            ->addProperty("e", "engineer_id", "id")
            ->addProperty("e", "engineer_name", "name");
        return $rsm;
    }

    public function testAddJoinedEntityTwice_SaveResultWithAlias()
    {
        $rsm = $this->createMultipleJoinedEntityRsm();

        $this->assertTrue(isset($rsm->entityResult["r"]));
        $this->assertEquals(
            array("reporter_id" => "id", "reporter_name" => "name"),
            $rsm->entityResult["r"]['properties']
        );
        $this->assertTrue(isset($rsm->entityResult["e"]));
        $this->assertEquals(
            array("engineer_id" => "id", "engineer_name" => "name"),
            $rsm->entityResult["e"]['properties']
        );
    }

    public function testAddJoinedEntityTwice_SaveJoinByAlias()
    {
        $rsm = $this->createMultipleJoinedEntityRsm();

        $this->assertTrue(isset($rsm->joinedEntity["r"]));
        $this->assertTrue(isset($rsm->joinedEntity["e"]));
    }

    /**
     *
     * @return Zend_Entity_Query_ResultSetMapping
     */
    public function createMultipleRootEntityRsm()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("User", "r")
            ->addProperty("r", "reporter_id", "id")
            ->addProperty("r", "reporter_name", "name")
            ->addEntity("User", "e")
            ->addProperty("e", "engineer_id", "id")
            ->addProperty("e", "engineer_name", "name");
        return $rsm;
    }


    public function testAddRootEntityTwice_SaveRootByAlias()
    {
        $rsm = $this->createMultipleRootEntityRsm();

        $this->assertTrue(isset($rsm->rootEntity["r"]));
        $this->assertEquals("User", $rsm->rootEntity["r"]);
        $this->assertTrue(isset($rsm->rootEntity["e"]));
        $this->assertEquals("User", $rsm->rootEntity["e"]);
    }

    public function testAddRootEntityTwice_SaveResultByAlias()
    {
        $rsm = $this->createMultipleRootEntityRsm();

        $this->assertTrue(isset($rsm->entityResult["r"]));
        $this->assertEquals(
            array("reporter_id" => "id", "reporter_name" => "name"),
            $rsm->entityResult["r"]['properties']
        );
        $this->assertTrue(isset($rsm->entityResult["e"]));
        $this->assertEquals(
            array("engineer_id" => "id", "engineer_name" => "name"),
            $rsm->entityResult["e"]['properties']
        );
    }
}