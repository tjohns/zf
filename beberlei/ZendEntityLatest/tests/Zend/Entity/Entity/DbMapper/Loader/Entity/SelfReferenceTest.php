<?php

class Zend_Entity_DbMapper_Loader_Entity_SelfReferenceTest extends Zend_Entity_DbMapper_Loader_TestCase
{
    public function getFixtureClassName()
    {
        return 'Zend_Entity_Fixture_SelfReferenceDefs';
    }

    public function getLoaderClassName()
    {
        return 'Zend_Db_Mapper_Loader_Entity';
    }

    public function testLoadMultipleSelfReferencingEntities()
    {
        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity('Zend_TestEntity1', 'c')
            ->addProperty('c', 'c_id', 'id')
            ->addProperty('c', 'c_name', 'name')
            ->addProperty('c', 'c_mother', 'mother')
            ->addProperty('c', 'c_father', 'father')
            ->addJoinedEntity('Zend_TestEntity1', 'm', 'c', 'mother')
            ->addProperty('m', 'm_id', 'id')
            ->addProperty('m', 'm_name', 'name')
            ->addProperty('m', 'm_mother', 'mother')
            ->addProperty('m', 'm_father', 'father')
            ->addJoinedEntity('Zend_TestEntity1', 'f', 'c', 'father')
            ->addProperty('f', 'f_id', 'id')
            ->addProperty('f', 'f_name', 'name')
            ->addProperty('f', 'f_mother', 'mother')
            ->addProperty('f', 'f_father', 'father');

        $resultSet = array(
            array(
                'c_id'      => '1',
                'c_name'    => 'Jesus',
                'c_mother'  => '2',
                'c_father'  => '3',
                'm_id'      => '2',
                'm_name'    => 'Maria',
                'm_mother'  => '4',
                'm_father'  => '5',
                'f_id'      => '3',
                'f_name'    => 'Josef',
                'f_mother'  => '6',
                'f_father'  => '7',
            )
        );

        $loader = $this->createLoader();
        $data = $loader->processResultset($resultSet, $rsm);

        $this->assertEquals(1,          count($data));
        $this->assertEquals(1,          $data[0]->id);
        $this->assertEquals("Jesus",    $data[0]->name);
        $this->assertEquals(2,          $data[0]->mother->id);
        $this->assertEquals("Maria",    $data[0]->mother->name);
        $this->assertEquals(3,          $data[0]->father->id);
        $this->assertEquals("Josef",    $data[0]->father->name);
        
        $identityMap = $this->entityManager->getIdentityMap();

        $this->assertEquals(4, $identityMap->getPrimaryKey($data[0]->mother->mother));
        $this->assertEquals(5, $identityMap->getPrimaryKey($data[0]->mother->father));
        $this->assertEquals(6, $identityMap->getPrimaryKey($data[0]->father->mother));
        $this->assertEquals(7, $identityMap->getPrimaryKey($data[0]->father->father));
    }
}