<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Message
 */
require_once 'Zend/Mail/Message.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MessageTest extends PHPUnit_Framework_TestCase
{
    protected $_file;

    public function setUp()
    {
        $this->_file = dirname(__FILE__) . '/_files/mail.txt';
    }

    public function testInvalidFile()
    {
        try {
            $message = new Zend_Mail_Message(array('file' => '/this/file/does/not/exists'));
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while loading unknown file');
    }

    public function testIsMultipart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertTrue($message->isMultipart());
    }

    public function testGetHeader()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetDecodedHeader()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->from, '"Peter Müller" <peter-mueller@example.com>');
    }

    public function testGetHeaderAsArray()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals($message->getHeader('subject', 'array'), array('multipart'));
    }

    public function testGetHeaderFromOpenFile()
    {
        $fh = fopen($this->_file, 'r');
        $message = new Zend_Mail_Message(array('file' => $fh));

        $this->assertEquals($message->subject, 'multipart');
    }

    public function testGetFirstPart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }

    public function testGetFirstPartTwice()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        $message->getPart(1);
        $this->assertEquals(substr($message->getPart(1)->getContent(), 0, 14), 'The first part');
    }


    public function testGetWrongPart()
    {
        $message = new Zend_Mail_Message(array('file' => $this->_file));

        try {
            $message->getPart(-1);
        } catch (Exception $e) {
            return; // ok
        }

        $this->fail('no exception raised while fetching unknown part');
    }

    public function testNoHeaderMessage()
    {
        $message = new Zend_Mail_Message(array('file' => __FILE__));

        $this->assertEquals(substr($message->getContent(), 0, 5), '<?php');
    }

}
