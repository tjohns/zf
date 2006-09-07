<?php
/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */


/**
 * Zend_Feed
 */
require_once 'Zend/Feed.php';


/**
 * @package    Zend_Feed
 * @subpackage UnitTests
 */
class Zend_Feed_CountTest extends PHPUnit_Framework_TestCase {

    public function testCount()
    {
        $f = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeed.xml');
        $this->assertEquals($f->count(), 2, 'Feed count should be 2');
    }

}
