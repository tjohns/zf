<?php
require_once 'PHPUnit/Framework/TestCase.php';
require_once 'Zend/Navigation/Page/Uri.php';

/**
 * Tests the class Zend_Navigation_Page_Uri
 *
 */
class Zend_Navigation_Page_UriTest extends PHPUnit_Framework_TestCase
{
    public function testSetAndGetUri()
    {
        $page = new Zend_Navigation_Page_Uri(array(
            'label' => 'foo',
            'uri' => '#'
        ));

        $this->assertEquals('#', $page->getUri());
        $page->setUri('bar');
        $this->assertEquals('bar', $page->getUri());

        $invalids = array(42, (object) null, -1);
        foreach ($invalids as $invalid) {
            try {
                $page->setUri($invalid);
                $msg = $invalid . ' is invalid, but no ';
                $msg .= 'Zend_Navigation_Exception was thrown';
                $this->fail($msg);
            } catch (Zend_Navigation_Exception $e) {

            }
        }
    }
}