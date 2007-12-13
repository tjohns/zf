<?php
// Call Zend_View_Helper_HeadScriptTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HeadScriptTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_HeadScript */
require_once 'Zend/View/Helper/HeadScript.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_HeadScript.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_HeadScriptTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_HeadScript
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HeadScriptTest");
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
        $this->helper = new Zend_View_Helper_HeadScript();
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
        if ($registry->containerExists('Zend_View_Helper_HeadScript')) {
            $registry->deleteContainer('Zend_View_Helper_HeadScript');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadScript'));
        $helper = new Zend_View_Helper_HeadScript();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadScript'));
    }

    public function testHeadScriptReturnsObjectInstance()
    {
        $placeholder = $this->helper->headScript();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_HeadScript);
    }

    public function testPrependFileCreatesCorrectOrdering()
    {
        $this->helper->prependFile('foobar')
                     ->prependFile('barbaz');
        $values   = $this->helper->getArrayCopy();
        $expected = array('barbaz', 'foobar');
        $received = array();
        foreach ($values as $value) {
            $received[] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::FILE, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testAppendFileCreatesCorrectOrdering()
    {
        $this->helper->appendFile('foobar')
                     ->appendFile('barbaz');
        $values   = $this->helper->getArrayCopy();
        $expected = array('foobar', 'barbaz');
        $received = array();
        foreach ($values as $value) {
            $received[] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::FILE, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testPrependScriptCreatesCorrectOrdering()
    {
        $this->helper->prependScript('foobar')
                     ->prependScript('barbaz');
        $values   = $this->helper->getArrayCopy();
        $expected = array('barbaz', 'foobar');
        $received = array();
        foreach ($values as $value) {
            $received[] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testAppendScriptCreatesCorrectOrdering()
    {
        $this->helper->appendScript('foobar')
                     ->appendScript('barbaz');
        $values   = $this->helper->getArrayCopy();
        $expected = array('foobar', 'barbaz');
        $received = array();
        foreach ($values as $value) {
            $received[] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testOffsetSetFileCreatesCorrectOrdering()
    {
        $this->helper->appendFile('foobar')
                     ->offsetSetFile(5, 'barbaz');
        $values   = $this->helper->getArrayCopy();
        $expected = array(0 => 'foobar', 5 => 'barbaz');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::FILE, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testOffsetSetScriptCreatesCorrectOrdering()
    {
        $this->helper->appendScript('foobar')
                     ->offsetSetScript(5, 'barbaz');
        $values   = $this->helper->getArrayCopy();
        $expected = array(0 => 'foobar', 5 => 'barbaz');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testCaptureAppendsScriptByDefault()
    {
        $this->helper->appendScript('foobar');
        $this->helper->captureStart();
        echo 'bazbat';
        $this->helper->captureEnd();
        $values   = $this->helper->getArrayCopy();
        $expected = array('foobar', 'bazbat');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testCapturePrependsScriptWhenRequested()
    {
        $this->helper->appendScript('foobar');
        $this->helper->captureStart('PREPEND');
        echo 'bazbat';
        $this->helper->captureEnd();
        $values   = $this->helper->getArrayCopy();
        $expected = array('bazbat', 'foobar');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testCaptureOverwritesValuesWhenSetRequested()
    {
        $this->helper->appendScript('foobar');
        $this->helper->captureStart('SET');
        echo 'bazbat';
        $this->helper->captureEnd();
        $values   = $this->helper->getArrayCopy();
        $expected = array('bazbat');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode']);
        }
        $this->assertSame($expected, $received);
    }

    public function testToStringSerializesScriptProperly()
    {
        $this->helper->appendScript('foobar');
        $string = $this->helper->toString();
        $expected = '<script type="text/javascript">foobar</script>';
        $this->assertContains($expected, $string);
    }

    public function testToStringSerializesFileProperly()
    {
        $this->helper->appendFile('foobar');
        $string = $this->helper->toString();
        $expected = '<script type="text/javascript" src="foobar"></script>';
        $this->assertContains($expected, $string);
    }

    public function testToStringSerializesScriptProperlyWhenCdataRequsted()
    {
        $this->helper->useCdata = true;
        $this->helper->appendScript('foobar');
        $string = $this->helper->toString();
        $expected = '<script type="text/javascript">' . PHP_EOL . '//<![CDATA[' . PHP_EOL . 'foobar' . PHP_EOL . '//]]>' . PHP_EOL . '</script>';
        $this->assertContains($expected, $string);
    }

    public function testHeadScriptAllowsSettingScript()
    {
        $this->helper->appendScript('foobar');
        $this->helper->headScript('bazbat', 'SCRIPT', 'SET');
        $values   = $this->helper->getArrayCopy();
        $expected = array('bazbat');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode'], var_export($value, 1));
        }
        $this->assertSame($expected, $received);
    }

    public function testHeadScriptAllowsSettingFile()
    {
        $this->helper->appendScript('foobar');
        $this->helper->headScript('bazbat', 'FILE', 'SET');
        $values   = $this->helper->getArrayCopy();
        $expected = array('bazbat');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::FILE, $value['mode'], var_export($value, 1));
        }
        $this->assertSame($expected, $received);
    }

    public function testHeadScriptAllowsAppendingScript()
    {
        $this->helper->appendScript('foobar');
        $this->helper->headScript('bazbat', 'SCRIPT', 'APPEND');
        $values   = $this->helper->getArrayCopy();
        $expected = array('foobar', 'bazbat');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode'], var_export($value, 1));
        }
        $this->assertSame($expected, $received);
    }

    public function testHeadScriptAllowsAppendingFile()
    {
        $this->helper->appendFile('foobar');
        $this->helper->headScript('bazbat', 'FILE', 'APPEND');
        $values   = $this->helper->getArrayCopy();
        $expected = array('foobar', 'bazbat');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::FILE, $value['mode'], var_export($value, 1));
        }
        $this->assertSame($expected, $received);
    }

    public function testHeadScriptAllowsPrependingScript()
    {
        $this->helper->prependScript('foobar');
        $this->helper->headScript('bazbat', 'SCRIPT', 'prepend');
        $values   = $this->helper->getArrayCopy();
        $expected = array('bazbat', 'foobar');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::SCRIPT, $value['mode'], var_export($value, 1));
        }
        $this->assertSame($expected, $received);
    }

    public function testHeadScriptAllowsPrependingFile()
    {
        $this->helper->prependFile('foobar');
        $this->helper->headScript('bazbat', 'FILE', 'prepend');
        $values   = $this->helper->getArrayCopy();
        $expected = array('bazbat', 'foobar');
        $received = array();
        foreach ($values as $key => $value) {
            $received[$key] = $value['content'];
            $this->assertEquals(Zend_View_Helper_HeadScript::FILE, $value['mode'], var_export($value, 1));
        }
        $this->assertSame($expected, $received);
    }
}

// Call Zend_View_Helper_HeadScriptTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadScriptTest::main") {
    Zend_View_Helper_HeadScriptTest::main();
}
