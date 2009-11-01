<?php

class Zend_Entity_ManagerFactoryTest extends Zend_Entity_TestCase
{
    public function testCreateManager_NoStorageOptionsNoMapper_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        Zend_Entity_ManagerFactory::createEntityManager(array('mapper' => null, 'storageOptions' => null));
    }

    public function testCreateManager_WithPreinstantiatedMapper()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $mapper = $this->createMapper();
        $options = array('mapper' => $mapper, 'storageOptions' => null);

        $mapper = Zend_Entity_ManagerFactory::createEntityManager($options);

        $this->assertSame($mapper, $em->getMapper());
    }

    public function testCreateManager_NoMetadataDefinitionPath_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $options = array(
            'storageOptions' => array(
                'backend' => 'Db',
                'adapter' => new Zend_Test_DbAdapter()
            )
        );

        $em = Zend_Entity_ManagerFactory::createEntityManager($options);
    }

    public function testCreateManager_UnknownMapperClass_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $options = array(
            'metadataDefinitionPath' => '/tmp',
            'storageOptions' => array(
                'backend' => 'Foo',
            ),
        );

        $em = Zend_Entity_ManagerFactory::createEntityManager($options);
    }

    public function testCreateManager_KnownClass_NotAMapperImpl_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");
        $options = array(
            'metadataDefinitionPath' => '/tmp',
            'storageOptions' => array(
                'backend' => 'stdClass',
            ),
        );

        $em = Zend_Entity_ManagerFactory::createEntityManager($options);
    }

    public function testCreateManager_WithDbMapper()
    {
        $options = array(
            'metadataDefinitionPath' => dirname(__FILE__)."/DbMapper/IntegrationTest/Clinic/Definition/",
            'storageOptions' => array(
                'backend' => 'Db',
                'adapter' => new Zend_Test_DbAdapter(),
            ),
        );
        $em = Zend_Entity_ManagerFactory::createEntityManager($options);

        $this->assertType('Zend_Entity_Manager', $em);
        $this->assertType('Zend_Db_Mapper_Mapper', $em->getMapper());
    }

    public function testCreateManager_FromZendConfig()
    {
        $options = array(
            'metadataDefinitionPath' => dirname(__FILE__)."/DbMapper/IntegrationTest/Clinic/Definition/",
            'storageOptions' => array(
                'backend' => 'Db',
                'adapter' => new Zend_Test_DbAdapter(),
            ),
        );
        $em = Zend_Entity_ManagerFactory::createEntityManager(new Zend_Config($options));

        $this->assertType('Zend_Entity_Manager', $em);
        $this->assertType('Zend_Db_Mapper_Mapper', $em->getMapper());
    }

    public function testCreateManager_WithNamedQueries()
    {
        $options = array(
            'metadataDefinitionPath' => dirname(__FILE__)."/DbMapper/IntegrationTest/Clinic/Definition/",
            'namedQueries' => array('MyFoo_Queries' => 'MyFoo/Queries'),
            'storageOptions' => array(
                'backend' => 'Db',
                'adapter' => new Zend_Test_DbAdapter(),
            ),
        );
        
        $em = Zend_Entity_ManagerFactory::createEntityManager($options);

        $this->assertType('Zend_Loader_PluginLoader', $em->getNamedQueryLoader());
        $this->assertEquals(array('MyFoo_Queries_' => array('MyFoo/Queries/')), $em->getNamedQueryLoader()->getPaths());
    }

    public function testCreateMapper_Database()
    {
        $options = array(
            'backend' => 'Db',
            'adapter' => new Zend_Test_DbAdapter()
        );

        $dbMapper = Zend_Entity_ManagerFactory::createMapper($options);

        $this->assertType('Zend_Db_Mapper_Mapper', $dbMapper);
    }

    public function testCreateMapperFromZendConfig()
    {
        $options = array(
            'backend' => 'Db',
            'adapter' => new Zend_Test_DbAdapter()
        );

        $dbMapper = Zend_Entity_ManagerFactory::createMapper(new Zend_Config($options));

        $this->assertType('Zend_Db_Mapper_Mapper', $dbMapper);
    }

    public function testCreateMapper_NoBackendGiven_ThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        Zend_Entity_ManagerFactory::createMapper(array());
    }

    public function testCreateMetadataFactory_FromDirectory()
    {
        $codePath = dirname(__FILE__)."/MetadataFactory/_files/empty/";
        $mf = Zend_Entity_ManagerFactory::createMetadataFactoryFromPath($codePath);

        $this->assertType('Zend_Entity_MetadataFactory_Code', $mf);
    }

    public function testCreateMetadataFactory_InvalidPath_ThrowsException()
    {
        $invalidPath = sys_get_temp_dir().DIRECTORY_SEPARATOR."invalidPath".DIRECTORY_SEPARATOR."invalidPath";

        $this->setExpectedException("Zend_Entity_Exception");
        $mf = Zend_Entity_ManagerFactory::createMetadataFactoryFromPath($invalidPath);
    }
}
