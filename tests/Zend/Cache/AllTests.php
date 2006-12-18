<?php

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Cache_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/Paris'); // to avoid an E_STRICT notice
require_once 'FactoryTest.php';
require_once 'CoreTest.php';
require_once 'FileBackendTest.php';
require_once 'SqliteBackendTest.php';
require_once 'OutputFrontendTest.php';
require_once 'FunctionFrontendTest.php';
require_once 'ClassFrontendTest.php';
require_once 'FileFrontendTest.php';
require_once 'ApcBackendTest.php';
require_once 'MemcachedBackendTest.php';
require_once 'PageFrontendTest.php';
require_once 'ZendPlatformBackendTest.php';

// Zend_Log        
require_once 'Zend/Log.php';
require_once 'Zend/Log/Adapter/Null.php';
Zend_Log::registerLogger(new Zend_Log_Adapter_Null());

class Zend_Cache_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Cache');
		$suite->addTestSuite('Zend_Cache_FactoryTest');
		$suite->addTestSuite('Zend_Cache_CoreTest');
        $suite->addTestSuite('Zend_Cache_FileBackendTest');
        $suite->addTestSuite('Zend_Cache_OutputFrontendTest');
        $suite->addTestSuite('Zend_Cache_FunctionFrontendTest');
        $suite->addTestSuite('Zend_Cache_ClassFrontendTest');
        $suite->addTestSuite('Zend_Cache_FileFrontendTest');
        $suite->addTestSuite('Zend_Cache_PageFrontendTest');
        if (!defined('TESTS_ZEND_CACHE_SQLITE_ENABLED') || (defined('TESTS_ZEND_CACHE_SQLITE_ENABLED') && TESTS_ZEND_CACHE_SQLITE_ENABLED)) {
            $suite->addTestSuite('Zend_Cache_SqliteBackendTest');
        }
        if (!defined('TESTS_ZEND_CACHE_APC_ENABLED') || (defined('TESTS_ZEND_CACHE_APC_ENABLED') && TESTS_ZEND_CACHE_APC_ENABLED)) {
            $suite->addTestSuite('Zend_Cache_ApcBackendTest');
        }
        if (!defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') || (defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') && TESTS_ZEND_CACHE_MEMCACHED_ENABLED)) {
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) { 
                define('TESTS_ZEND_CACHE_MEMCACHED_HOST', '127.0.0.1');
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PORT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PORT', 11211);
            }
            if (!defined('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT')) {
                define('TESTS_ZEND_CACHE_MEMCACHED_PERSISTENT', true);
            }
            $suite->addTestSuite('Zend_Cache_MemcachedBackendTest');
        }
        if (!defined('TESTS_ZEND_CACHE_PLATFORM_ENABLED') || (defined('TESTS_ZEND_CACHE_PLATFORM_ENABLED') && TESTS_ZEND_CACHE_PLATFORM_ENABLED)) {
            $suite->addTestSuite('Zend_Cache_ZendPlatformBackendTest');
        }
        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Cache_AllTests::main') {
    Zend_Cache_AllTests::main();
}
