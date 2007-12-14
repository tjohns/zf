<?php
// Call Zend_View_Helper_HeadMetaTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HeadMetaTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_HeadMeta */
require_once 'Zend/View/Helper/HeadMeta.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_HeadMeta.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_HeadMetaTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HeadMeta
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HeadMetaTest");
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
        $regKey = Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_HeadMeta();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadMeta')) {
            $registry->deleteContainer('Zend_View_Helper_HeadMeta');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadMeta'));
        $helper = new Zend_View_Helper_HeadMeta();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadMeta'));
    }

    public function testHeadMetaReturnsObjectInstance()
    {
        $placeholder = $this->helper->headMeta();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_HeadMeta);
    }

    public function testAppendPrependAndSetThrowExceptionsWhenNonMetaValueProvided()
    {
        try {
            $this->helper->append('foo');
            $this->fail('Non-meta value should not append');
        } catch (Exception $e) {
        }
        try {
            $this->helper->prepend('foo');
            $this->fail('Non-meta value should not prepend');
        } catch (Exception $e) {
        }
        try {
            $this->helper->set('foo');
            $this->fail('Non-meta value should not set');
        } catch (Exception $e) {
        }
    }

    protected function _inflectAction($type)
    {
        $type = str_replace('-', ' ', $type);
        $type = ucwords($type);
        $type = str_replace(' ', '', $type);
        return $type;
    }

    protected function _testOverloadAppend($type)
    {
        $action = 'append' . $this->_inflectAction($type);
        $string = 'foo';
        for ($i = 0; $i < 3; ++$i) {
            $string .= ' foo';
            $this->helper->$action('keywords', $string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $this->assertContains('<meta ', $values[$i]);
            $this->assertContains($type . '="keywords"', $values[$i]);
            $this->assertContains('content="' . $string . '"', $values[$i]);
        }
    }

    protected function _testOverloadPrepend($type)
    {
        $action = 'prepend' . $this->_inflectAction($type);
        $string = 'foo';
        for ($i = 0; $i < 3; ++$i) {
            $string .= ' foo';
            $this->helper->$action('keywords', $string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $first = array_shift($values);
            $this->assertContains('<meta ', $first);
            $this->assertContains($type . '="keywords"', $first);
            $this->assertContains('content="' . $string . '"', $first);
        }
    }

    protected function _testOverloadSet($type)
    {
        $action = 'set' . $this->_inflectAction($type);
        $string = 'foo';
        for ($i = 0; $i < 3; ++$i) {
            $this->helper->appendName('keywords', $string);
            $string .= ' foo';
        }
        $this->helper->$action('keywords', $string);
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $this->assertContains('<meta ', $values[0]);
        $this->assertContains($type . '="keywords"', $values[0]);
        $this->assertContains('content="' . $string . '"', $values[0]);
    }

    public function testOverloadingAppendNameAppendsMetaTagToStack()
    {
        $this->_testOverloadAppend('name');
    }

    public function testOverloadingPrependNamePrependsMetaTagToStack()
    {
        $this->_testOverloadPrepend('name');
    }

    public function testOverloadingSetNameOverwritesMetaTagStack()
    {
        $this->_testOverloadSet('name');
    }

    public function testOverloadingAppendHttpEquivAppendsMetaTagToStack()
    {
        $this->_testOverloadAppend('http-equiv');
    }

    public function testOverloadingPrependHttpEquivPrependsMetaTagToStack()
    {
        $this->_testOverloadPrepend('http-equiv');
    }

    public function testOverloadingSetHttpEquivOverwritesMetaTagStack()
    {
        $this->_testOverloadSet('http-equiv');
    }

    public function testOverloadingThrowsExceptionWithFewerThanTwoArgs()
    {
        try {
            $this->helper->setName('foo');
            $this->fail('Overloading should require at least two arguments');
        } catch (Exception $e) {
        }
    }

    public function testOverloadingThrowsExceptionWithInvalidMethodType()
    {
        try {
            $this->helper->setFoo('foo');
            $this->fail('Overloading should only work for (set|prepend|append)(Name|HttpEquiv)');
        } catch (Exception $e) {
        }
    }

    public function testCanBuildMetaTagsWithAttributes()
    {
        $this->helper->setName('keywords', 'foo bar', array('lang' => 'us_en', 'scheme' => 'foo', 'bogus' => 'unused'));
        $value = $this->helper->getValue();
        $this->assertContains('lang="us_en"', $value);
        $this->assertContains('scheme="foo"', $value);
        $this->assertNotContains('bogus', $value);
        $this->assertNotContains('unused', $value);
    }
}

// Call Zend_View_Helper_HeadMetaTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadMetaTest::main") {
    Zend_View_Helper_HeadMetaTest::main();
}
