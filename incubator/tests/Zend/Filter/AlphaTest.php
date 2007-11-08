<?php
// Call Zend_Filter_AlphaTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_AlphaTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Filter/Alpha.php';

/**
 * Test class for Zend_Filter_Alpha.
 */
class Zend_Filter_AlphaTest extends PHPUnit_Framework_TestCase 
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

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_AlphaTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testFilterStripsAllNonAlphabeticalCharacters()
    {
        $string = 'Foo123_-bla!@#Bla**';
        $filter = new Zend_Filter_Alpha();
        $filtered = $filter->filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('FooblaBla', $filtered);
    }

    public function testFilterKeepsWhitespaceWhenRequested()
    {
        $string = 'Foo123_ -bla!@# Bla**';
        $filter = new Zend_Filter_Alpha(true);
        $filtered = $filter->filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('Foo bla Bla', $filtered);
    }
}

// Call Zend_Filter_AlphaTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Filter_AlphaTest::main") {
    Zend_Filter_AlphaTest::main();
}
