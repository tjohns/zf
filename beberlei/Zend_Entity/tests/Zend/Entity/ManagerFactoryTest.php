<?php

class Zend_Entity_ManagerFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testNoMetadataDefinitionPath_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $em = Zend_Entity_ManagerFactory::createEntityManager('Foo', array());
    }

    public function testUnknownClass_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $options = array('metadataDefinitionPath' => '/tmp');

        $em = Zend_Entity_ManagerFactory::createEntityManager('Foo', $options);
    }

    public function testKnownClass_NotMapper_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $options = array('metadataDefinitionPath' => '/tmp');

        $em = Zend_Entity_ManagerFactory::createEntityManager('stdClass', $options);
    }

    public function testCreateDbMapper()
    {
        $options = array(
            'metadataDefinitionPath' => dirname(__FILE__)."/IntegrationTest/Clinic/",
            'db' => new Zend_Test_DbAdapter()
        );
        $em = Zend_Entity_ManagerFactory::createEntityManager('Db', $options);

        $this->assertType('Zend_Entity_Manager', $em);
        $this->assertType('Zend_Db_Mapper_Mapper', $em->getMapper());
    }

    public function testCreateMapper_WithNamedQueries()
    {
        $options = array(
            'metadataDefinitionPath' => dirname(__FILE__)."/IntegrationTest/Clinic/",
            'db' => new Zend_Test_DbAdapter(),
            'namedQueries' => array('MyFoo_Queries' => 'MyFoo/Queries')
        );
        
        $em = Zend_Entity_ManagerFactory::createEntityManager('Db', $options);

        $this->assertType('Zend_Loader_PluginLoader', $em->getNamedQueryLoader());
        $this->assertEquals(array('MyFoo_Queries_' => array('MyFoo/Queries/')), $em->getNamedQueryLoader()->getPaths());
    }
}
