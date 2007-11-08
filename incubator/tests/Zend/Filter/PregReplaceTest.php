<?php
// Call Zend_Filter_PregReplaceTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_PregReplaceTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/PregReplace.php';

/**
 * Test class for Zend_Filter_PregReplace.
 */
class Zend_Filter_PregReplaceTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_PregReplaceTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->filter = new Zend_Filter_PregReplace();
    }

    public function testIsUnicodeSupportEnabledReturnsSaneValue()
    {
        $enabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        $this->assertEquals($enabled, $this->filter->isUnicodeSupportEnabled());
    }

    public function testMatchPatternInitiallyNull()
    {
        $this->assertNull($this->filter->getMatchPattern());
    }

    public function testMatchPatternAccessorsWork()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $this->filter->setMatchPattern($pattern);
        $this->assertEquals($pattern, $this->filter->getMatchPattern());
    }

    public function testReplacementInitiallyEmpty()
    {
        $replacement = $this->filter->getReplacement();
        $this->assertTrue(empty($replacement));
    }

    public function testReplacementAccessorsWork()
    {
        $replacement = 'foo/bar';
        $this->filter->setReplacement($replacement);
        $this->assertEquals($replacement, $this->filter->getReplacement());
    }

    public function testFilterPerformsRegexReplacement()
    {
        $string = 'controller/action';
        $this->filter->setMatchPattern('#^controller/(?P<action>[a-z_-]+)#')
             ->setReplacement('foo/bar');
        $filtered = $this->filter->filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('foo/bar', $filtered);
    }
}

// Call Zend_Filter_PregReplaceTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_PregReplaceTest::main") {
    Zend_Filter_PregReplaceTest::main();
}
