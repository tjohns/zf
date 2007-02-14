<?php
/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */

/** Zend_Controller_Router_Route_Regex */
require_once 'Zend/Controller/Router/Route/Regex.php';

/** PHPUnit test case */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Controller
 * @subpackage UnitTests
 */
class Zend_Controller_Router_Route_RegexTest extends PHPUnit_Framework_TestCase
{

    public function testStaticMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/all');
        $values = $route->match('users/all');

        $this->assertSame(array(), $values);
    }

    public function testStaticUTFMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('żółć');
        $values = $route->match('żółć');

        $this->assertSame(array(), $values);
    }

    public function testURLDecode()
    {
        $route = new Zend_Controller_Router_Route_Regex('żółć');
        $values = $route->match('%C5%BC%C3%B3%C5%82%C4%87');

        $this->assertSame(array(), $values);
    }

    public function testStaticNoMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/a/martel');
        $values = $route->match('users/a');

        $this->assertSame(false, $values);
    }

    public function testStaticMatchWithDefaults()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/all', array('controller' => 'ctrl'));
        $values = $route->match('users/all');

        $this->assertSame(1, count($values));
        $this->assertSame('ctrl', $values['controller']);
    }

    public function testRootRoute()
    {
        $route = new Zend_Controller_Router_Route_Regex('');
        $values = $route->match('/');

        $this->assertSame(array(), $values);
    }

    public function testVariableMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)');
        $values = $route->match('users/martel');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values[1]);
    }

    public function testDoubleMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(user_(\d+).html)');
        $values = $route->match('users/user_1354.html');
        
        $this->assertSame(2, count($values));
        $this->assertSame('user_1354.html', $values[1]);
        $this->assertSame('1354', $values[2]);
    }

    public function testNegativeMatch()
    {

        $route = new Zend_Controller_Router_Route_Regex('((?!admin|moderator).+)', 
           array('module' => 'index', 'controller' => 'index'),
           array(1 => 'action')
        );

        $values = $route->match('users');
        
        $this->assertSame(3, count($values));
        $this->assertSame('index', $values['module']);
        $this->assertSame('index', $values['controller']);
        $this->assertSame('users', $values['action']);
    }

    public function testNumericDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array(1 => 'martel'));
        $values = $route->match('users');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values[1]);
    }
    
    public function testVariableMatchWithNumericDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array(1 => 'martel'));
        $values = $route->match('users/vicki');
        
        $this->assertSame(1, count($values));
        $this->assertSame('vicki', $values[1]);
    }
    
    public function testNamedVariableMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(?P<username>.+)');
        $values = $route->match('users/martel');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values[1]);
    }

    public function testMappedVariableMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, array(1 => 'username'));
        $values = $route->match('users/martel');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }
    
    public function testMappedVariableWithDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users(?:/(.+))?', array('username' => 'martel'), array(1 => 'username'));
        $values = $route->match('users');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }
    
    public function testMappedVariableWithNamedSubpattern()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(?P<name>.+)', null, array(1 => 'username'));
        $values = $route->match('users/martel');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testOptionalVar()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(\w+)/?(?:p/(\d+))?', null, array(1 => 'username', 2 => 'page'));
        $values = $route->match('users/martel/p/1');
        
        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values['page']);
    }
    
    public function testEmptyOptionalVar()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(\w+)/?(?:p/(\d+))?', null, array(1 => 'username', 2 => 'page'));
        $values = $route->match('users/martel');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }
    
    public function testMixedMap()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(\w+)/?(?:p/(\d+))?', null, array(1 => 'username'));
        $values = $route->match('users/martel/p/1');
        
        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values[2]);
    }
    
    public function testNumericDefaultWithMap()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array(1 => 'martel'), array(1 => 'username'));
        $values = $route->match('users');
        
        $this->assertSame(1, count($values));
        $this->assertSame('martel', $values['username']);
    }

    public function testMixedMapWithDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(\w+)/?(?:p/(\d+))?', array(2 => '1'), array(1 => 'username'));
        $values = $route->match('users/martel/p/10');
        
        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('10', $values[2]);
    }
    
    public function testMixedMapWithDefaults2()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(\w+)?/?(?:p/(\d+))?', array(2 => '1', 'username' => 'martel'), array(1 => 'username'));
        $values = $route->match('users');
        
        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values[2]);
    }
    
    public function testOptionalVarWithMapAndDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(\w+)/?(?:p/(\d+))?', array('page' => '1', 'username' => 'martel'), array(1 => 'username', 2 => 'page'));
        $values = $route->match('users/martel');
        
        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values['username']);
        $this->assertSame('1', $values['page']);
    }
    
    public function testOptionalVarWithMapAndNumericDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(\w+)/?(?:p/(\d+))?', array(2 => '1'), array(2 => 'page'));
        $values = $route->match('users/martel');
        
        $this->assertSame(2, count($values));
        $this->assertSame('martel', $values[1]);
        $this->assertSame('1', $values['page']);
    }
    
    public function testMappedAndNumericDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(\w+)?', array(1 => 'martel', 'username' => 'vicki'), array(1 => 'username'));
        $values = $route->match('users');

        // Matches both defaults but the one defined last is used
        
        $this->assertSame(1, count($values));
        $this->assertSame('vicki', $values['username']);
    }
    
    public function testAssemble()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        $values = $route->match('users/martel');
        
        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }

    public function testAssembleWithDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array(1 => 'martel'), null, 'users/%s');
        $values = $route->match('users');
        
        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }
    
    public function testAssembleWithMappedDefault()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array('username' => 'martel'), array(1 => 'username'), 'users/%s');
        $values = $route->match('users');
        
        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }
    
    public function testAssembleWithData()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, null, 'users/%s');
        $values = $route->match('users/martel');
        
        $url = $route->assemble(array(1 => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }
    
    public function testAssembleWithMappedVariable()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        $values = $route->match('users/martel');
        
        $url = $route->assemble(array('username' => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }
    
    public function testAssembleWithMappedVariableAndNumericKey()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        $values = $route->match('users/martel');
        
        $url = $route->assemble(array(1 => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }
    
    public function testAssembleWithoutMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, null, 'users/%s');
        
        try {
            $url = $route->assemble();
            $this->fail();
        } catch (Exception $e) {}
    }

    public function testAssembleWithDefaultWithoutMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array(1 => 'martel'), null, 'users/%s');
        
        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }
    
    public function testAssembleWithMappedDefaultWithoutMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/?(.+)?', array('username' => 'martel'), array(1 => 'username'), 'users/%s');
        
        $url = $route->assemble();
        $this->assertSame('users/martel', $url);
    }
    
    public function testAssembleWithDataWithoutMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, null, 'users/%s');
        
        $url = $route->assemble(array(1 => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }
    
    public function testAssembleWithMappedVariableWithoutMatch()
    {
        $route = new Zend_Controller_Router_Route_Regex('users/(.+)', null, array(1 => 'username'), 'users/%s');
        
        $url = $route->assemble(array('username' => 'vicki'));
        $this->assertSame('users/vicki', $url);
    }
    
}
