<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Maildir
 */
require_once 'Zend/Mail/Maildir.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MaildirTest extends PHPUnit_Framework_TestCase
{
    protected $_maildir;

    public function setUp()
    {
        $this->_maildir = dirname(__FILE__) . '/_files/test.maildir/';
    }

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));
        } catch (Exception $e) {
            $this->fail('exception raised while loading maildir');
        }
    }
    
    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Maildir(array('dirname' => '/This/Dir/Does/Not/Exist'));
        } catch (Exception $e) {
            return; // test ok
        }
        
        $this->fail('no exception raised while loading unknown dir');
    }

    public function testLoadInvalid()
    {
        try {
            $mail = new Zend_Mail_Maildir(array('dirname' => dirname(__FILE__)));
        } catch (Exception $e) {
            return; // test ok
        }
        
        $this->fail('no exception while loading invalid dir');
    }
    
    public function testClose()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing maildir');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);
    }
    
    public function testSize()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }
    
    public function testSingleSize()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));
        
        $subject = $mail->getHeader(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));
        
        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));
        
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));
        
        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
    
    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Maildir(array('dirname' => $this->_maildir));

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }
        
        $this->fail('no exception raised while deleting message (maildir is read-only)');
    }
}
