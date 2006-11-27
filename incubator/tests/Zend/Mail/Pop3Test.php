<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Pop3
 */
require_once 'Zend/Mail/Pop3.php';

/**
 * Zend_Mail_Transport_Pop3
 */
require_once 'Zend/Mail/Transport/Pop3.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_Pop3Test extends PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        $this->_params = array('host'     => TESTS_ZEND_MAIL_POP3_HOST,
                               'user'     => TESTS_ZEND_MAIL_POP3_USER,
                               'password' => TESTS_ZEND_MAIL_POP3_PASSWORD);
    }

    public function testConnectOk()
    {
        try {
            $mail = new Zend_Mail_Pop3($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server');
        }
    }

    public function testConnectFailure()
    {
        $this->_params['host'] = 'example.example';
        try {
            $mail = new Zend_Mail_Pop3($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        // I can only hope noone installs a POP3 server there
        $this->fail('no exception raised while connecting to example.example');
    }

    public function testNoParams()
    {
        try {
            $mail = new Zend_Mail_Pop3(array());
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }

    public function testConnectSSL()
    {
        if(!TESTS_ZEND_MAIL_POP3_SSL) {
            return;
        }

        $this->_params['ssl'] = 'SSL';
        try {
            $mail = new Zend_Mail_Pop3($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server with SSL');
        }
    }

    public function testConnectTLS()
    {
        if(!TESTS_ZEND_MAIL_POP3_TLS) {
            return;
        }

        $this->_params['ssl'] = 'TLS';
        try {
            $mail = new Zend_Mail_Pop3($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to pop3 server with TLS');
        }
    }

    public function testInvalidService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_POP3_INVALID_PORT;

        try {
            $mail = new Zend_Mail_Pop3($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while connection to invalid port');
    }

    public function testWrongService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_POP3_WRONG_PORT;

        try {
            $mail = new Zend_Mail_Pop3($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while connection to wrong port');
    }

    public function testClose()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing pop3 connection');
        }
    }

    public function testHasTop()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $this->assertTrue($mail->hasTop);
    }

    public function testHasCreate()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $this->assertFalse($mail->hasCreate);
    }

    public function testNoop()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }

    public function testCount()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Pop3($this->_params);
        $shouldSizes = array(1 => 397, 89, 709, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $subject = $mail->getHeader(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

/*
    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Pop3($this->_params);

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (mbox is read-only)');
    }
*/

    public function testWithInstanceConstruction()
    {
        $transport = new Zend_Mail_Transport_Pop3($this->_params['host']);
        $mail = new Zend_Mail_Pop3($transport);
        try {
            // because we did no login this has to throw an exception
            $mail->getMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while fetching with wrong transport');
    }

    public function testRequestAfterClose()
    {
        $mail = new Zend_Mail_Pop3($this->_params);
        $mail->close();
        try {
            $mail->getMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while requesting after closing connection');
    }
}
