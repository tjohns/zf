<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route */
require_once 'Zend/Controller/Router/Route/Module.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_ModuleTest extends PHPUnit_Framework_TestCase
{
    
    protected $_request; 
    protected $_dispatcher; 
    protected $route; 
    
    public function setUp()
    {
        $front = Zend_Controller_Front::getInstance();
        $front->resetInstance();

        $this->_dispatcher = $front->getDispatcher();
        
        $this->_dispatcher->setControllerDirectory(array(
            'default' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files',
            'mod'     => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'Admin',
        ));
        
        $defaults = array(
            'controller' => 'defctrl', 
            'action'     => 'defact',
            'module'     => 'default'
        );
        
        require_once 'Zend/Controller/Request/Http.php';
        $this->_request = new Zend_Controller_Request_Http();
        $front->setRequest($this->_request);
        
        $this->route = new Zend_Controller_Router_Route_Module($defaults, $this->_dispatcher, $this->_request);
    }

    public function testModuleMatch()
    {
        $values = $this->route->match('mod');
        
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
    }

    public function testModuleAndControllerMatch()
    {
        $values = $this->route->match('mod/con');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
    }

    public function testModuleControllerAndActionMatch()
    {
        $values = $this->route->match('mod/con/act');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
    }

    public function testModuleControllerActionAndParamsMatch()
    {
        $values = $this->route->match('mod/con/act/var/val/foo');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['module']));
        $this->assertEquals('mod', $values['module']);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
        $this->assertTrue(isset($values['var']));
        $this->assertEquals('val', $values['var']);
        $this->assertTrue(array_key_exists('foo', $values), var_export($values, 1));
        $this->assertTrue(empty($values['foo']));
    }

    public function testControllerOnlyMatch()
    {
        $values = $this->route->match('con');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
    }

    public function testControllerOnlyAndActionMatch()
    {
        $values = $this->route->match('con/act');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
    }

    public function testControllerOnlyActionAndParamsMatch()
    {
        $values = $this->route->match('con/act/var/val/foo');
        $this->assertType('array', $values);
        $this->assertTrue(isset($values['controller']));
        $this->assertEquals('con', $values['controller']);
        $this->assertTrue(isset($values['action']));
        $this->assertEquals('act', $values['action']);
        $this->assertTrue(isset($values['var']));
        $this->assertEquals('val', $values['var']);
        $this->assertTrue(array_key_exists('foo', $values), var_export($values, 1));
        $this->assertTrue(empty($values['foo']));
    }

    public function testModuleMatchWithControlKeysChange()
    {
        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');
        
        $this->route = new Zend_Controller_Router_Route_Module(array(), $this->_dispatcher, $this->_request);
        
        $values = $this->route->match('mod/ctrl');
        
        $this->assertType('array', $values);
        $this->assertSame('mod', $values['m']);
        $this->assertSame('ctrl', $values['c']);
        $this->assertSame('index', $values['a']);
    }
    
    public function testModuleMatchWithLateControlKeysChange()
    {
        $this->_request->setModuleKey('m');
        $this->_request->setControllerKey('c');
        $this->_request->setActionKey('a');
        
        $values = $this->route->match('mod/ctrl');
        
        $this->assertType('array', $values);
        $this->assertSame('mod', $values['m']);
        $this->assertSame('ctrl', $values['c']);
        $this->assertSame('index', $values['a']);
    }
    
    public function testAssembleNoModuleOrController()
    {
        $params = array(
            'action' => 'act',
            'foo'    => 'bar'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('defctrl/act/foo/bar', $url);
    }

    public function testAssembleControllerOnly()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        
        $this->assertEquals('con/act/foo/bar', $url);
    }

    public function testAssembleModuleAndController()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/con/act/foo/bar', $url);
    }

    public function testAssembleNoController()
    {
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/defctrl/act/foo/bar', $url);
    }

    public function testAssembleNoAction()
    {
        $params = array(
            'module'     => 'mod',
            'controller' => 'ctrl'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/ctrl', $url);
    }

    public function testAssembleNoActionWithParams()
    {
        $params = array(
            'foo'		 => 'bar',
            'module'     => 'mod',
            'controller' => 'ctrl'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/ctrl/defact/foo/bar', $url);
    }

    public function testAssembleNoModuleOrControllerMatched()
    {
        $this->route->match('');
        
        $params = array(
            'action' => 'act',
            'foo'    => 'bar'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('defctrl/act/foo/bar', $url);
    }
    
    public function testAssembleControllerOnlyMatched()
    {
        $this->route->match('ctrl');
        
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        
        $this->assertEquals('con/act/foo/bar', $url);
    }

    public function testAssembleModuleAndControllerMatched()
    {
        $this->route->match('mod/ctrl');
        
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'm'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('m/ctrl/act/foo/bar', $url);
    }

    public function testAssembleNoControllerMatched()
    {
        $this->route->match('mod');
        
        $params = array(
            'foo'        => 'bar',
            'action'     => 'act',
            'module'     => 'mod'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('mod/defctrl/act/foo/bar', $url);
    }

    public function testAssembleNoActionMatched()
    {
        $this->route->match('mod/ctrl');
        
        $params = array(
            'module'     => 'def',
            'controller' => 'con'
        );
        $url = $this->route->assemble($params);
        $this->assertEquals('def/con', $url);
    }

}
