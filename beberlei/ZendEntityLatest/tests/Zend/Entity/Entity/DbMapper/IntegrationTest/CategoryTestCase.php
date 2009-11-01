<?php

require_once dirname(__FILE__)."/Category/Entities/Category.php";

abstract class Zend_Entity_DbMapper_IntegrationTest_CategoryTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     * @var Zend_Entity_Manager
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_MetadataFactory_Code
     */
    protected $_metadataFactory = null;

    public function tearDown()
    {
        $this->getAdapter()->closeConnection();
    }

    public function setUp()
    {
        parent::setUp();

        $path = dirname(__FILE__)."/Category/Definition/";
        $dbAdapter = $this->getAdapter();
        $this->_metadataFactory = new Zend_Entity_MetadataFactory_Code($path);
        $this->init($this->_metadataFactory);
        $this->_entityManager = new Zend_Entity_Manager(array('adapter' => $dbAdapter, 'metadataFactory' => $this->_metadataFactory));
    }

    public function init(Zend_Entity_MetadataFactory_Code $mf)
    {

    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/Category/Fixtures/AdjacencyListFixture.xml');
    }

    public function testLoadSubCategory()
    {
        $category = $this->_entityManager->load("Zend_Entity_Category", 2);
        $this->assertType('Zend_Entity_Category', $category);
        $this->assertEquals(2, $category->id);
        $this->assertType('Zend_Entity_Category', $category->parent);
        $this->assertEquals(1, $category->parent->id);
    }
}
