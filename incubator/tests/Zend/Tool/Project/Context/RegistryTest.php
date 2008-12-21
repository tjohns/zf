<?php


require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Tool/Project/Context/Registry.php';

require_once 'Zend/Debug.php';

class Zend_Tool_Project_Context_RegistryTest extends PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        Zend_Tool_Project_Context_Registry::resetInstance();
    }
    
    public function testGetInstanceReturnsIntstance()
    {
        $this->assertEquals('Zend_Tool_Project_Context_Registry', get_class(Zend_Tool_Project_Context_Registry::getInstance()));
    }
    
    public function testNewRegistryIsEmpty()
    {
        $this->assertEquals(0, Zend_Tool_Project_Context_Registry::getInstance()->count());
    }
    
    public function testRegistryLoadsSystemContexts()
    {
        Zend_Tool_Project_Context_Registry::loadSystem();
        $this->assertEquals(3, Zend_Tool_Project_Context_Registry::getInstance()->count());
        
        // ensure number remains the same
        Zend_Tool_Project_Context_Registry::loadSystem();
        $this->assertEquals(3, Zend_Tool_Project_Context_Registry::getInstance()->count());
    }
    
    public function testRegistryReturnsSystemContext()
    {
        Zend_Tool_Project_Context_Registry::loadSystem();
        $this->assertEquals('Zend_Tool_Project_Context_System_ProjectProfileFile', get_class(Zend_Tool_Project_Context_Registry::getInstance()->getContext('projectProfileFile')));
    }
    
    public function testRegistryLoadsZFContexts()
    {
        Zend_Tool_Project_Context_Registry::loadZf();
        // the number of initial ZF Components
        $count = Zend_Tool_Project_Context_Registry::getInstance()->count();
        $this->assertGreaterThanOrEqual(32, $count);
        
        // ensure the number remains the same
        Zend_Tool_Project_Context_Registry::loadZf();
        $this->assertEquals($count, Zend_Tool_Project_Context_Registry::getInstance()->count());
    }
    
    /**
     * @expectedException Zend_Tool_Project_Context_Exception
     */
    public function testRegistryThrowsExceptionOnUnallowedContextOverwrite()
    {
        Zend_Tool_Project_Context_Registry::loadSystem();
        Zend_Tool_Project_Context_Registry::getInstance()->addContextClass('Zend_Tool_Project_Context_System_ProjectDirectory');
    }
    
    /**
     * @expectedException Zend_Tool_Project_Context_Exception
     */
    public function testRegistryThrowsExceptionOnUnknownContextRequest()
    {
        Zend_Tool_Project_Context_Registry::loadSystem();
        Zend_Tool_Project_Context_Registry::getInstance()->getContext('somethingUnknown');
    }
    
}
