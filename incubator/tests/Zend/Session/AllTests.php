<?php

Zend_Session_AllTests::setIncludePath();
define('TESTS_GENERATE_REPORT_TARGET', '/var/www/html/tests');

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Session_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting ( E_ALL | E_STRICT );

class Zend_Session_AllTests
{

    protected static $pathCwd;

    protected static $pathIncubator;

    protected static $pathLibrary;
    
    protected static $pathIncubatorLibrary;

    protected static $pathIncubatorTests;

    public static function setIncludePath()
    {

        self::$pathCwd = dirname(__FILE__);

        self::$pathIncubator = dirname(dirname(dirname(self::$pathCwd)));

        self::$pathLibrary = dirname(self::$pathIncubator) . DIRECTORY_SEPARATOR . 'library';
    
        self::$pathIncubatorLibrary = self::$pathIncubator . DIRECTORY_SEPARATOR . 'library';

        self::$pathIncubatorTests = self::$pathIncubator . DIRECTORY_SEPARATOR . 'tests';

        set_include_path( self::$pathCwd . PATH_SEPARATOR
            . self::$pathIncubatorTests . PATH_SEPARATOR
            . self::$pathIncubatorLibrary .  PATH_SEPARATOR
            . self::$pathLibrary .  PATH_SEPARATOR
            . get_include_path()
        );

    }

    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        self::buildSessionTestFile('Core.php');

        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Session');

        require_once 'SessionTest.php';
        //require_once 'CoreTest.php';

        $suite->addTestSuite('Zend_SessionTest');
        #$suite->addTestSuite('Zend_Session_CoreTest');
        #$suite->addTestSuite('Zend_Session_Test');

        return $suite;
    }

    protected static function buildSessionTestFile($filename)
    {
        $filename = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Core.php';

    	if (false === ($out = fopen($filename, 'w'))) {
            return false;
        }

    	$src = self::$pathIncubatorLibrary
            . DIRECTORY_SEPARATOR . 'Zend'
            . DIRECTORY_SEPARATOR . 'Session'
            . DIRECTORY_SEPARATOR . 'Core.php';
    	
    	echo "$src\n";
    	$lines = file($src);
    	
    	foreach ($lines as $line) {
    	
    	    $line = rtrim($line);
    	
    	    if ($line === 'final class Zend_Session_Core') {
    	
    	        fputs($out, "class Zend_Session_Core\n");
    	
    	    } else {
    
    	        if (false === fputs($out, (preg_replace('/^(\s*)static private\s/', '\1static protected ', $line)."\n"))) {

                    return false;
    	
    	        }
    	    }
    	
        }

        return true;
    }

}

if (PHPUnit_MAIN_METHOD == 'Zend_Session_AllTests::main') {
    Zend_Session_AllTests::main();
}
