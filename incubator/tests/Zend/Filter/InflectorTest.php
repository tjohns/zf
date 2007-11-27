<?php
// Call Zend_Filter_InflectorTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_InflectorTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Inflector.php';
require_once 'Zend/Filter/PregReplace.php';

/**
 * Test class for Zend_Filter_Inflector.
 */
class Zend_Filter_InflectorTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_InflectorTest");
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
        $this->inflector = new Zend_Filter_Inflector();
        $this->loader    = $this->inflector->getPluginLoader();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        $this->loader->clearPaths();
    }

    public function testGetPluginLoaderReturnsLoaderByDefault()
    {
        $loader = $this->inflector->getPluginLoader();
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader_Interface);
        $paths = $loader->getPaths();
        $this->assertEquals(1, count($paths));
        $this->assertTrue(array_key_exists('Zend_Filter_', $paths));
    }

    public function testSetPluginLoaderAllowsSettingAlternatePluginLoader()
    {
        $defaultLoader = $this->inflector->getPluginLoader();
        $loader = new Zend_Loader_PluginLoader();
        $this->inflector->setPluginLoader($loader);
        $receivedLoader = $this->inflector->getPluginLoader();
        $this->assertNotSame($defaultLoader, $receivedLoader);
        $this->assertSame($loader, $receivedLoader);
    }

    public function testAddFilterPrefixPathAddsPathsToPluginLoader()
    {
        $this->inflector->addFilterPrefixPath('Foo_Bar', 'Zend/View/');
        $loader = $this->inflector->getPluginLoader();
        $paths  = $loader->getPaths();
        $this->assertTrue(array_key_exists('Foo_Bar_', $paths));
    }

    public function testTargetAccessorsWork()
    {
        $this->inflector->setTarget('foo/:bar/:baz');
        $this->assertEquals('foo/:bar/:baz', $this->inflector->getTarget());
    }

    public function testTargetInitiallyNull()
    {
        $this->assertNull($this->inflector->getTarget());
    }

    public function testPassingTargetToConstructorSetsTarget()
    {
        $inflector = new Zend_Filter_Inflector('foo/:bar/:baz');
        $this->assertEquals('foo/:bar/:baz', $inflector->getTarget());
    }

    public function testSetTargetByReferenceWorks()
    {
        $target = 'foo/:bar/:baz';
        $this->inflector->setTargetReference($target);
        $this->assertEquals('foo/:bar/:baz', $this->inflector->getTarget());
        $target .= '/:bat';
        $this->assertEquals('foo/:bar/:baz/:bat', $this->inflector->getTarget());
    }

    public function testSetFilterRuleWithStringRuleCreatesRuleEntryAndFilterObject()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', 'PregReplace');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $filter = $rules[0];
        $this->assertTrue($filter instanceof Zend_Filter_Interface);
    }

    public function testSetFilterRuleWithFilterObjectCreatesRuleEntryWithFilterObject()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $filter = new Zend_Filter_PregReplace();
        $this->inflector->setFilterRule('controller', $filter);
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $received = $rules[0];
        $this->assertTrue($received instanceof Zend_Filter_Interface);
        $this->assertSame($filter, $received);
    }

    public function testSetFilterRuleWithArrayOfRulesCreatesRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', array('PregReplace', 'Alpha'));
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(2, count($rules));
        $this->assertTrue($rules[0] instanceof Zend_Filter_Interface);
        $this->assertTrue($rules[1] instanceof Zend_Filter_Interface);
    }

    public function testAddFilterRuleAppendsRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setFilterRule('controller', 'PregReplace');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(1, count($rules));
        $this->inflector->addFilterRule('controller', 'Alpha');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals(2, count($rules));
    }

    public function testSetStaticRuleCreatesScalarRuleEntry()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setStaticRule('controller', 'foobar');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar', $rules);
    }

    public function testSetStaticRuleMultipleTimesOverwritesEntry()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setStaticRule('controller', 'foobar');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar', $rules);
        $this->inflector->setStaticRule('controller', 'bazbat');
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('bazbat', $rules);
    }

    public function testSetStaticRuleReferenceAllowsUpdatingRuleByReference()
    {
        $rule  = 'foobar';
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $this->inflector->setStaticRuleReference('controller', $rule);
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar', $rules);
        $rule .= '/baz';
        $rules = $this->inflector->getRules('controller');
        $this->assertEquals('foobar/baz', $rules);
    }

    public function testAddRulesCreatesAppropriateRuleEntries()
    {
        $rules = $this->inflector->getRules();
        $this->assertEquals(0, count($rules));
        $action = 'foo';
        $this->inflector->addRules(array(
            ':controller' => array('PregReplace', 'Alpha'),
            'suffix'      => 'phtml',
        ));
        $rules = $this->inflector->getRules();
        $this->assertEquals(2, count($rules));
        $this->assertEquals(2, count($rules['controller']));
        $this->assertEquals('phtml', $rules['suffix']);
    }

    public function testFilterTransformsStringAccordingToRules()
    {
        $this->inflector->setTarget(':controller/:action.:suffix')
             ->addRules(array(
                 ':controller' => array('CamelCaseToDash'),
                 ':action'     => array('CamelCaseToDash'),
                 'suffix'      => 'phtml'
             ));
        $filtered = $this->inflector->filter(array(
            'controller' => 'FooBar',
            'action'     => 'bazBat'
        ));
        $this->assertEquals('Foo-Bar/baz-Bat.phtml', $filtered);
    }
    
    public function testTargetReplacementIdentiferAccessorsWork()
    {
        $this->assertEquals(':', $this->inflector->getTargetReplacementIdentifier());
        $this->inflector->setTargetReplacementIdentifier('?=');
        $this->assertEquals('?=', $this->inflector->getTargetReplacementIdentifier());
    }

    public function testTargetReplacementIdentiferWorksWhenInflected()
    {
        $this->inflector = new Zend_Filter_Inflector(
            '?=##controller/?=##action.?=##suffix', 
            array(
                 ':controller' => array('CamelCaseToDash'),
                 ':action'     => array('CamelCaseToDash'),
                 'suffix'      => 'phtml'
                 ),
            null,
            '?=##'
            );

        $filtered = $this->inflector->filter(array(
            'controller' => 'FooBar',
            'action'     => 'bazBat'
        ));

        $this->assertEquals('Foo-Bar/baz-Bat.phtml', $filtered);
    }
    
    public function testThrowTargetExceptionsAccessorsWork()
    {
        $this->assertEquals(':', $this->inflector->getTargetReplacementIdentifier());
        $this->inflector->setTargetReplacementIdentifier('?=');
        $this->assertEquals('?=', $this->inflector->getTargetReplacementIdentifier());
    }
    

    public function testThrowTargetExceptionsOnAccessorsWork()
    {
        $this->assertTrue($this->inflector->isThrowTargetExceptionsOn());
        $this->inflector->setThrowTargetExceptionsOn(false);
        $this->assertFalse($this->inflector->isThrowTargetExceptionsOn());
    }
    
    public function testTargetExceptionThrownWhenTargetSourceNotSatisfied()
    {
        $this->inflector = new Zend_Filter_Inflector(
            '?=##controller/?=##action.?=##suffix', 
            array(
                 ':controller' => array('CamelCaseToDash'),
                 ':action'     => array('CamelCaseToDash'),
                 'suffix'      => 'phtml'
                 ),
            true,
            '?=##'
            );

        try {
            $filtered = $this->inflector->filter(array('controller' => 'FooBar'));
            $this->fail();
        } catch (Exception $e) {
            $this->assertTrue($e instanceof Zend_Filter_Exception);
        }

    }
    
    public function testTargetExceptionNotThrownOnIdentifierNotFollowedByCharacter()
    {
        $this->inflector = new Zend_Filter_Inflector(
            'e:\path\to\:controller\:action.:suffix',
            array(
                 ':controller' => array('CamelCaseToDash', 'StringToLower'),
                 ':action'     => array('CamelCaseToDash'),
                 'suffix'      => 'phtml'
                ),
            true,
            ':'
            );
            
        try {
            $filtered = $this->inflector->filter(array('controller' => 'FooBar', 'action' => 'MooToo'));
            $this->assertEquals($filtered, 'e:\path\to\foo-bar\Moo-Too.phtml');
        } catch (Exception $e) {
            $this->fail();
        }
            
            
    }
    
}

// Call Zend_Filter_InflectorTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_InflectorTest::main") {
    Zend_Filter_InflectorTest::main();
}
