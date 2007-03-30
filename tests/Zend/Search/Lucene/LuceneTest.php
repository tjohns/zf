<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_LuceneTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $index = Zend_Search_Lucene::create(dirname(__FILE__) . '/_files');

        $this->assertTrue($index instanceof Zend_Search_Lucene_Interface);
    }
}
