<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Imap
 */
require_once 'Zend/Mail/Imap.php';

/**
 * Zend_Mail_Transport_Imap
 */
require_once 'Zend/Mail/Transport/Imap.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_ImapTest extends PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        $this->_params = array('host'     => TESTS_ZEND_MAIL_IMAP_HOST,
                               'user'     => TESTS_ZEND_MAIL_IMAP_USER,
                               'password' => TESTS_ZEND_MAIL_IMAP_PASSWORD);
    }

    public function testConnectOk()
    {
        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to imap server');
        }
    }

    public function testConnectFailure()
    {
        $this->_params['host'] = 'example.example';
        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        // I can only hope noone installs a imap server there
        $this->fail('no exception raised while connecting to example.example');
    }

    public function testNoParams()
    {
        try {
            $mail = new Zend_Mail_Imap(array());
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }


    public function testConnectSSL()
    {
        if(!TESTS_ZEND_MAIL_IMAP_SSL) {
            return;
        }

        $this->_params['ssl'] = 'SSL';
        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to imap server with SSL');
        }
    }

    public function testConnectTLS()
    {
        if(!TESTS_ZEND_MAIL_IMAP_TLS) {
            return;
        }

        $this->_params['ssl'] = 'TLS';
        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading connection to imap server with TLS');
        }
    }

    public function testInvalidService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_IMAP_INVALID_PORT;

        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while connection to invalid port');
    }

    public function testWrongService()
    {
        $this->_params['port'] = TESTS_ZEND_MAIL_IMAP_WRONG_PORT;

        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while connection to wrong port');
    }

    public function testWrongUsername()
    {
        // this also triggers ...{chars}<NL>token for coverage
        $this->_params['user'] = "there is no\nnobody";

        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception while using wrong username');
    }

    public function testWithInstanceConstruction()
    {
        $transport = new Zend_Mail_Transport_Imap($this->_params['host']);
        $transport->login($this->_params['user'], $this->_params['password']);
        // if $transport is invalid the constructor fails while selecting INBOX
        $mail = new Zend_Mail_Imap($transport);
    }

    public function testWrongFolder()
    {
        $this->_params['folder'] = 'this folder does not exist on your server';

        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception with not existing folder');
    }


    public function testClose()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        try {
            $mail->close();
        } catch (Exception $e) {
            $this->fail('exception raised while closing imap connection');
        }
    }
/*
    currently imap has no top

    public function testHasTop()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $this->assertTrue($mail->hasTop);
    }
*/
    public function testHasCreate()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $this->assertFalse($mail->hasCreate);
    }

/*
    noop not yet supported

    public function testNoop()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        try {
            $mail->noop();
        } catch (Exception $e) {
            $this->fail('exception raised while doing nothing (noop)');
        }
    }
*/
    public function testCount()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        $shouldSizes = array(1 => 397, 89, 709, 452, 497);


        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);
    }

    public function testSingleSize()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $size = $mail->getSize(2);
        $this->assertEquals(89, $size);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $subject = $mail->getHeader(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

/*
    currently imap has no top

    public function testFetchTopBody()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $content = $mail->getHeader(3, 1)->getContent();
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }
*/
    public function testFetchMessageHeader()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);
    }

    public function testFetchMessageBody()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $content = $mail->getMessage(3)->getContent();
        list($content, ) = explode("\n", $content, 2);
        $this->assertEquals('Fair river! in thy bright, clear flow', trim($content));
    }

/*
    public function testFailedRemove()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        try {
            $mail->removeMessage(1);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while deleting message (mbox is read-only)');
    }
*/
    public function testTooLateCount()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        $mail->close();
        // after closing we can't count messages

        try {
            $mail->countMessages();
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while counting messages on closed connection');
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';
        try {
            $mail = new Zend_Mail_Imap($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown folder');
    }

    public function testChangeFolder()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        try {
            $mail->selectFolder('subfolder/test');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder');
        }

        $this->assertEquals($mail->getCurrentFolder(), 'subfolder/test');
    }

    public function testUnknownFolder()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        try {
            $mail->selectFolder('/Unknown/Folder/');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while selecting unknown folder');
    }

    public function testGlobalName()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        try {
            $this->assertEquals((string)$mail->getFolders()->subfolder, 'subfolder');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting global name');
        }
    }

    public function testLocalName()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        try {
            $this->assertEquals($mail->getFolders()->subfolder->key(), 'test');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting local name');
        }
    }

    public function testKeyLocalName()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder'      => 'subfolder',
                                'subfolder/test' => 'test',
                                'INBOX'          => 'INBOX');
        $found_folders = array();

        foreach($iterator as $localName => $folder) {
            if(!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            $found_folders[(string)$folder] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testSelectable()
    {
        $mail = new Zend_Mail_Imap($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);

        foreach($iterator as $localName => $folder) {
            $this->assertEquals($localName, $folder->getLocalName());
        }
    }


    public function testCountFolder()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $count = $mail->countMessages();
        $this->assertEquals(1, $count);
    }

    public function testSizeFolder()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $sizes = $mail->getSize();
        $this->assertEquals(array(1 => 410), $sizes);
    }

    public function testFetchHeaderFolder()
    {
        $mail = new Zend_Mail_Imap($this->_params);

        $mail->selectFolder('subfolder/test');
        $subject = $mail->getHeader(1)->subject;
        $this->assertEquals('Message in subfolder', $subject);
    }

}
