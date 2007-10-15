<?php
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_InflectorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Controller/Front.php';
require_once 'Zend/Controller/Request/Http.php';
require_once 'Zend/View/Inflector.php';

/**
 * Test class for Zend_View_Inflector.
 */
class Zend_View_InflectorTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_InflectorTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->inflector = new Zend_View_Inflector();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->inflector);
    }

    /**
     * @return void
     */
    public function testDefaultRuleExists()
    {
        $this->assertEquals('controllerAction', $this->inflector->getDefaultRule());
    }

    /**
     * @return void
     */
    public function testSetDefaultRuleChangesDefaultRule()
    {
        $this->inflector->setDefaultRule('foobar');
        $this->assertEquals('foobar', $this->inflector->getDefaultRule());
    }

    /**
     * @return void
     */
    public function testDefaultPathRegisteredAtInstantiation()
    {
        $expected = array(array(
            'prefix' => 'Zend_View_Inflector_Rule',
            'path'   => 'Zend/View/Inflector/Rule'
        ));
        $this->assertEquals($expected, $this->inflector->getRulePath());
    }

    /**
     * @return void
     */
    public function testSetRulePathWithOnlyPathSetsPathAndDefaultPrefix()
    {
        $path = dirname(__FILE__);
        $this->inflector->setRulePath($path);
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(2, count($paths));
        $received = $paths[1];

        $this->assertEquals('Zend_View_Inflector_Rule', $received['prefix']);
        $this->assertEquals($path, $received['path']);
    }

    /**
     * @return void
     */
    public function testSetRulePathWithPathAndPrefix()
    {
        $path = dirname(__FILE__);
        $this->inflector->setRulePath($path, 'Foo_Bar');
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(2, count($paths));
        $received = $paths[1];

        $this->assertEquals('Foo_Bar', $received['prefix']);
        $this->assertEquals($path, $received['path']);
    }

    /**
     * @return void
     */
    public function testSetRulePathUtilizingArrayOfPathsAndNoPrefix()
    {
        $path1 = dirname(__FILE__);
        $path2 = dirname(dirname(__FILE__));
        $this->inflector->setRulePath(array($path1, $path2));
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(3, count($paths));
        $received1 = $paths[1];
        $received2 = $paths[2];

        $this->assertEquals('Zend_View_Inflector_Rule', $received1['prefix']);
        $this->assertEquals($path1, $received1['path']);
        $this->assertEquals('Zend_View_Inflector_Rule', $received2['prefix']);
        $this->assertEquals($path2, $received2['path']);
    }

    /**
     * @return void
     */
    public function testSetRulePathUtilizingArrayOfPathsAndPrefix()
    {
        $path1 = dirname(__FILE__);
        $path2 = dirname(dirname(__FILE__));
        $this->inflector->setRulePath(array($path1, $path2), 'Foo_Bar');
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(3, count($paths));
        $received1 = $paths[1];
        $received2 = $paths[2];

        $this->assertEquals('Foo_Bar', $received1['prefix']);
        $this->assertEquals($path1, $received1['path']);
        $this->assertEquals('Foo_Bar', $received2['prefix']);
        $this->assertEquals($path2, $received2['path']);
    }

    /**
     * @return void
     */
    public function testInitRulePathResetsRulePath()
    {
        $path1 = dirname(__FILE__);
        $path2 = dirname(dirname(__FILE__));
        $this->inflector->setRulePath(array($path1, $path2), 'Foo_Bar');
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(3, count($paths));

        $this->inflector->initRulePath();
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(1, count($paths));
    }

    /**
     * @return void
     */
    public function testAddRulePathAddsARulePath()
    {
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(1, count($paths));

        $path = dirname(__FILE__);
        $this->inflector->addRulePath($path);
        $paths = $this->inflector->getRulePath();
        $this->assertEquals(2, count($paths));
    }

    /**
     * @return void
     */
    public function testNormalizePrefixStripsTrailingUnderscore()
    {
        $this->assertEquals('Foo_Bar', $this->inflector->normalizePrefix('Foo_Bar_'));
    }

    /**
     * @return void
     */
    public function testNormalizePathTrimsTrailingDirectorySeparators()
    {
        $this->assertEquals('/foo/bar', $this->inflector->normalizePath('/foo/bar/'));
        $this->assertEquals('\\foo\bar', $this->inflector->normalizePath('\\foo\bar\\'));
    }

    /**
     * @return void
     */
    public function testInflectWithDefaultRuleTransformsName()
    {
        $request = new Zend_Controller_Request_Http();
        $request->setParams(array(
            'controller' => 'foo',
            'action'     => 'baz'
        ));
        Zend_Controller_Front::getInstance()->setRequest($request);

        $this->assertEquals('foo/bar.phtml', $this->inflector->inflect('bar'));
    }

    /**
     * @return void
     */
    public function testLoadRuleClassReturnsFullRuleClassname()
    {
        $class = $this->inflector->loadRuleClass('controllerAction');
        $this->assertEquals('Zend_View_Inflector_Rule_ControllerAction', $class);
    }

    /**
     * @return void
     */
    public function testLoadRuleClassLoadsClass()
    {
        $class = $this->inflector->loadRuleClass('controllerAction');
        $this->assertTrue(class_exists($class));
    }

    /**
     * @return void
     */
    public function testLoadRuleClassFromCustomDirectory()
    {
        $this->inflector->addRulePath(dirname(__FILE__) . '/_files/Rules', 'Rules');
        $class = $this->inflector->loadRuleClass('fooBar');
        $this->assertEquals('Rules_FooBar', $class);
        $this->assertTrue(class_exists($class));
    }

    /**
     * @return void
     */
    public function testLoadNonexistentRuleClassThrowsException()
    {
        try {
            $class = $this->inflector->loadRuleClass('BazBat');
            $this->fail('Nonexistent rule classes should cause loadRuleClass() to throw an exception');
        } catch (Exception $e) {
        }
    }

    /**
     * @return void
     */
    public function testGetRuleClassReturnsRulePrependedWithPrefixAndUnderscore()
    {
        $this->assertEquals('Foo_Bar_fooBar', $this->inflector->getRuleClass('fooBar', 'Foo_Bar'));
    }

    /**
     * @return void
     */
    public function testGetRuleReturnsAppropriateRuleObject()
    {
        $rule = $this->inflector->getRule('controllerAction');
        $this->assertTrue($rule instanceof Zend_View_Inflector_Rule_ControllerAction, 'Could not retrieve rule class; received variable of type ' . gettype($rule));
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_View_InflectorTest::main") {
    Zend_View_InflectorTest::main();
}
