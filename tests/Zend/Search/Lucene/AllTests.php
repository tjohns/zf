<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Search_Lucene_AllTests::main');
}

require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

require_once 'Zend/Search/Lucene/LuceneTest.php';

require_once 'Zend/Search/Lucene/DocumentTest.php';
require_once 'Zend/Search/Lucene/FSMTest.php';
require_once 'Zend/Search/Lucene/FieldTest.php';
require_once 'Zend/Search/Lucene/PriorityQueueTest.php';

require_once 'Zend/Search/Lucene/Storage/DirectoryTest.php';


class Zend_Search_Lucene_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Search_Lucene');

        $suite->addTestSuite('Zend_Search_Lucene_LuceneTest');

        $suite->addTestSuite('Zend_Search_Lucene_DocumentTest');
        $suite->addTestSuite('Zend_Search_Lucene_FSMTest');
        $suite->addTestSuite('Zend_Search_Lucene_FieldTest');
        $suite->addTestSuite('Zend_Search_Lucene_PriorityQueueTest');

        $suite->addTestSuite('Zend_Search_Lucene_Storage_DirectoryTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Search_Lucene_AllTests::main') {
    Zend_Search_Lucene_AllTests::main();
}
