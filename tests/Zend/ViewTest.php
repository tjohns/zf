<?php
/**
 * @package    Zend_View
 * @subpackage UnitTests
 */


/** Zend_View */
require_once 'Zend/View.php';

/** Zend_View_Interface */
require_once 'Zend/View/Interface.php';


/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_ViewTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Tests that the default script path is properly initialized
	 */
	public function testDefaultScriptPath()
	{
		$this->_testDefaultPath('script', false);
	}
    
    
	/**
	 * Tests that the default helper path is properly initialized
	 * and the directory is readable
	 */
	public function testDefaultHelperPath()
	{	
		$this->_testDefaultPath('helper');
	}

	
	/**
	 * Tests that the default filter path is properly initialized
	 * and the directory is readable
	 */
	public function testDefaultFilterPath()
	{
		$this->_testDefaultPath('filter');
	}


	/**
	 * Tests that script paths are added, properly ordered, and that
	 * directory separators are handled correctly.
	 */
    public function testAddScriptPath()
    {
    	$this->_testAddPath('script');
    }
    
    
	/**
	 * Tests that helper paths are added, properly ordered, and that
	 * directory separators are handled correctly.
	 */
    public function testAddHelperPath()
    {
    	$this->_testAddPath('helper');
    }
    
    
	/**
	 * Tests that filter paths are added, properly ordered, and that
	 * directory separators are handled correctly.
	 */
    public function testAddFilterPath()
    {
    	$this->_testAddPath('filter');
    }
    

	/**
	 * Tests that the (script|helper|filter) path array is properly
	 * initialized after instantiation.
	 * 
	 * @param string  $pathType         one of "script", "helper", or "filter".
	 * @param boolean $testReadability  check if the path is readable?
	 */
    protected function _testDefaultPath($pathType, $testReadability = true)
    {
    	$view = new Zend_View();
    	
		$reflector = $view->getAllPaths();
        $paths     = $reflector[$pathType];
			
		// test default helper path
		$this->assertType('array', $paths);
        if ('script' == $pathType) {
            $this->assertEquals(0, count($paths));
        } else {
            $this->assertEquals(1, count($paths));
            $item = $paths[0];

            $prefix = 'Zend_View_' . ucfirst($pathType) . '_';
            $this->assertEquals($prefix, $item['prefix']);

            if ($testReadability) {
                $this->assertTrue(is_dir($item['dir']));
                $this->assertTrue(is_readable($item['dir']));	    	
            }
        }
    }
        
    
    /**
     * Tests (script|helper|filter) paths can be added, that they are added
     * in the proper order, and that directory separators are properly handled. 
     * 
     * @param string $pathType one of "script", "helper", or "filter".
     */
    protected function _testAddPath($pathType)
    {
    	$view   = new Zend_View();
        $prefix = 'Zend_View_' . ucfirst($pathType) . '_';

    	// introspect default paths and build expected results.
		$reflector     = $view->getAllPaths(); 
		$expectedPaths = $reflector[$pathType];

        if ('script' == $pathType) {
            array_unshift($expectedPaths, 'baz' . DIRECTORY_SEPARATOR);
            array_unshift($expectedPaths, 'bar' . DIRECTORY_SEPARATOR);
            array_unshift($expectedPaths, 'foo' . DIRECTORY_SEPARATOR);
        } else {
            array_unshift($expectedPaths, array('prefix' => $prefix, 'dir' => 'baz' . DIRECTORY_SEPARATOR));
            array_unshift($expectedPaths, array('prefix' => $prefix, 'dir' => 'bar' . DIRECTORY_SEPARATOR));
            array_unshift($expectedPaths, array('prefix' => $prefix, 'dir' => 'foo' . DIRECTORY_SEPARATOR));
        }
    	
    	// add paths
    	$func = 'add' . ucfirst($pathType) . 'Path';
		$view->$func('baz');    // no separator
		$view->$func('bar\\');  // windows 
		$view->$func('foo/');   // unix		
		
    	// introspect script paths after adding two new paths
		$reflector   = $view->getAllPaths(); 
		$actualPaths = $reflector[$pathType];

        $this->assertSame($expectedPaths, $actualPaths);
    }

    
	/**
	 * Tests that the Zend_View environment is clean of any instance variables
	 */
    public function testSandbox()
    {
    	$view = new Zend_View();
    	$this->assertSame(array(), get_object_vars($view));
    }

    
    /**
     * Tests that isset() and empty() work correctly.  This is a common problem
     * because __isset() was not supported until PHP 5.1.
     */
    public function testIssetEmpty()
    {
    	$view = new Zend_View();
		$this->assertFalse(isset($view->foo));    	
		$this->assertTrue(empty($view->foo));
		
		$view->foo = 'bar';
		$this->assertTrue(isset($view->foo));
		$this->assertFalse(empty($view->foo));
    }    
    
    
    /**
     * Tests that a help can be loaded from the search path 
     *
     */
    public function testLoadHelper()
    {
		$view = new Zend_View();
		
		$view->setHelperPath(array(dirname(__FILE__) . '/View/_stubs/HelperDir1',
								   dirname(__FILE__) . '/View/_stubs/HelperDir2'));
								   

		$this->assertEquals('foo', $view->stub1());
		$this->assertEquals('bar', $view->stub2());

		// erase the paths to the helper stubs
		$view->setHelperPath(null);

		// verify that object handle of a stub was cache by calling it again
		// without its path in the helper search paths
		$this->assertEquals( 'foo', $view->stub1() );	
    }
    
    
    /**
     * Tests that calling a nonexistant helper file throws the expected exception
     */
    public function testLoadHelperNonexistantFile()
    {
    	$view = new Zend_View();
    	
    	try {
	    	$view->nonexistantHelper();
    	} catch (Zend_View_Exception $e) {
    		$this->assertRegexp('/helper [\'a-z]+ not found in path/i', $e->getMessage());
    		return;
    	}
    }

    
    /**
     * Tests that calling a helper whose file exists but class is not found within
     * throws the expected exception
     */
    public function testLoadHelperNonexistantClass()
    {
    	$view = new Zend_View();
    	
		$view->setHelperPath(array(dirname(__FILE__) . '/View/_stubs/HelperDir1'));

		
		try {
			// attempt to load the helper StubEmpty, whose file exists but 
			// does not contain the expected class within
			$view->stubEmpty();	
		} catch (Zend_View_Exception $e) {
    		$this->assertRegexp("/['_a-z]+ not found in path/i", $e->getMessage());
		}
    }
    
    
    
    /**
     * Tests that render() can render a template.
     */
    public function testRender()
    {
    	$view = new Zend_View();
    	
    	$view->setScriptPath(dirname(__FILE__) . '/View/_templates');
    	
    	$view->bar = 'bar';
    	
    	$this->assertEquals("foo bar baz\n", $view->render('test.phtml') );
    }
    
    /**
     * Tests that render() works when called within a template, and that 
     * protected members are not available
     */
    public function testRenderSubTemplates()
    {
        $view = new Zend_View();
        $view->setScriptPath(dirname(__FILE__) . '/View/_templates');
        $view->content = 'testSubTemplate.phtml';
        $this->assertEquals('', $view->render('testParent.phtml'));

        $logFile = dirname(__FILE__) . '/View/_templates/view.log';
        $this->assertTrue(file_exists($logFile));
        $log = file_get_contents($logFile);
        unlink($logFile); // clean up...
        $this->assertContains('This text should not be displayed', $log);
        $this->assertNotContains('testSubTemplate.phtml', $log);
    }
    
    /**
     * Tests that array properties may be modified after being set (see [ZF-460] 
     * and [ZF-268] for symptoms leading to this test)
     */
    public function testSetArrayProperty()
    {
        $view = new Zend_View();
        $view->foo = array();
        $view->foo[] = 42;

        $foo = $view->foo;

        $this->assertTrue(is_array($foo));
        $this->assertEquals(42, $foo[0]);
    }

    /**
     * Test that array properties are cleared following clearVars() call
     */
    public function testClearVars()
    {
        $view = new Zend_View();
        $view->foo     = array();
        $view->content = 'content';

        $this->assertTrue(is_array($view->foo));
        $this->assertEquals('content', $view->content);

        $view->clearVars();
        $this->assertFalse(isset($view->foo));
        $this->assertFalse(isset($view->content));
    }

    /**
     * Test that script paths are cleared following setScriptPath(null) call
     */
    public function testClearScriptPath()
    {
        $view = new Zend_View();
        
        // paths should be initially empty
        $this->assertSame(array(), $view->getScriptPaths());

        // add a path
        $view->setScriptPath('foo');
        $scriptPaths = $view->getScriptPaths();
        $this->assertType('array', $scriptPaths);
        $this->assertEquals(1, count($scriptPaths));
        
        // clear paths
        $view->setScriptPath(null);
        $this->assertSame(array(), $view->getScriptPaths());
    }

    /**
     * Test that an exception is thrown when no script path is set
     */
    public function testNoPath()
    {
        $view = new Zend_View();
        try {
            $view->render('somefootemplate.phtml');
            $this->fail('Rendering a template when no script path is set should raise an exception');
        } catch (Exception $e) {
            // success...
        }
    }

    /**
     * Test that getEngine() returns the same object
     */
    public function testGetEngine()
    {
        $view = new Zend_View();
        $this->assertSame($view, $view->getEngine());
    }

    public function testInstanceOfInterface()
    {
        $view = new Zend_View();
        $this->assertTrue($view instanceof Zend_View_Interface);
    }

    public function testGetVars()
    {
        $view = new Zend_View();
        $view->foo = 'bar';
        $view->bar = 'baz';
        $view->baz = array('foo', 'bar');

        $vars = $view->getVars();
        $this->assertEquals(3, count($vars));
        $this->assertEquals('bar', $vars['foo']);
        $this->assertEquals('baz', $vars['bar']);
        $this->assertEquals(array('foo', 'bar'), $vars['baz']);
    }

    /**
     * Test set/getEncoding() 
     */
    public function testSetGetEncoding()
    {
        $view = new Zend_View();
        $this->assertEquals('ISO-8859-1', $view->getEncoding());

        $view->setEncoding('UTF-8');
        $this->assertEquals('UTF-8', $view->getEncoding());
    }
}
