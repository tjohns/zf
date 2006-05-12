<?php
/**
 * @package    Zend_View
 * @subpackage UnitTests
 */


/** Zend_View */
require_once 'Zend/View.php';


/** PHPUnit2_Framework_TestCase */
require_once 'PHPUnit2/Framework/TestCase.php';


/**
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_ViewTest extends PHPUnit2_Framework_TestCase
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
    	
		$reflector = (array)$view; 
		$paths     = $reflector["\0Zend_View_Abstract\0_path"][$pathType];
			
		// test default helper path
		$this->assertType('array', $paths);
		$this->assertEquals(1, count($paths));
		
		if ($testReadability) {
			$this->assertTrue(is_dir($paths[0]));
			$this->assertTrue(is_readable($paths[0]));	    	
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
    	$view = new Zend_View();

    	// introspect default paths and build expected results.
		$reflector = (array)$view; 
		$expectedPaths = $reflector["\0Zend_View_Abstract\0_path"][$pathType];
    	array_unshift($expectedPaths, 'baz' . DIRECTORY_SEPARATOR);
    	array_unshift($expectedPaths, 'bar' . DIRECTORY_SEPARATOR);
    	array_unshift($expectedPaths, 'foo' . DIRECTORY_SEPARATOR);
    	
    	// add paths
    	$func = 'add' . ucfirst($pathType) . 'Path';
		$view->$func('baz');    // no separator
		$view->$func('bar\\');  // windows 
		$view->$func('foo/');   // unix		
		
    	// introspect script paths after adding two new paths
		$reflector = (array)$view; 
		$actualPaths = $reflector["\0Zend_View_Abstract\0_path"][$pathType];

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
								   

		$this->assertEquals( 'bar', $view->stub2() );	
		$this->assertEquals( 'foo', $view->stub1() );	

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
    		$this->assertRegexp('/helper [\'A-z]+ not found in path/i', $e->getMessage());
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
    		$this->assertRegexp('/loaded but class [\'_A-z]+ not found/i', $e->getMessage());
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
    	
    	$this->assertEquals('foo bar baz', $view->render('test.phtml') );
    }
    
    
}
