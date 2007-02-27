<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Storage_Folder_Maildir
 */
require_once 'Zend/Mail/Storage/Folder/Maildir.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MaildirFolderTest extends PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        $this->_params = array();
        $this->_params['dirname'] = dirname(__FILE__) . '/_files/test.maildir';
		if (!is_dir($this->_params['dirname'] . '/cur/')) {
			$this->markTestSkipped('You have to unpack maildir.tar in Zend/Mail/_files/test.maildir/ '
			                     . 'directory before enabling the maildir tests');
		}
    }

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading Maildir folder');
        }
    }

    public function testNoParams()
    {
        try {
            $mail = new Zend_Mail_Storage_Folder_Maildir(array());
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }

    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Storage_Folder_Maildir(array('dirname' => 'This/Folder/Does/Not/Exist'));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown dirname');
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';
        try {
            $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown folder');
    }

    public function testChangeFolder()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        try {
            $mail->selectFolder('subfolder.test');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder');
        }

        $this->assertEquals($mail->getCurrentFolder(), 'subfolder.test');
    }

    public function testUnknownFolder()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        try {
            $mail->selectFolder('/Unknown/Folder/');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while selecting unknown folder');
    }

    public function testGlobalName()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        try {
            // explicit call of __toString() needed for PHP < 5.2
            $this->assertEquals($mail->getFolders()->subfolder->__toString(), 'subfolder');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting global name');
        }
    }

    public function testLocalName()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        try {
            $this->assertEquals($mail->getFolders()->subfolder->key(), 'test');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting local name');
        }
    }

    public function testIterator()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder'      => 'subfolder',
                                'subfolder.test' => 'test',
                                'INBOX'          => 'INBOX');
        $found_folders = array();

        foreach ($iterator as $localName => $folder) {
            if (!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            // explicit call of __toString() needed for PHP < 5.2
            $found_folders[$folder->__toString()] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testKeyLocalName()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder'      => 'subfolder',
                                'subfolder.test' => 'test',
                                'INBOX'          => 'INBOX');
        $found_folders = array();

        foreach ($iterator as $localName => $folder) {
            if (!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            // explicit call of __toString() needed for PHP < 5.2
            $found_folders[$folder->__toString()] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testInboxEquals()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders('INBOX.subfolder'), RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array('subfolder.test' => 'test');
        $found_folders = array();

        foreach ($iterator as $localName => $folder) {
            if (!isset($search_folders[$folder->getGlobalName()])) {
                continue;
            }

            // explicit call of __toString() needed for PHP < 5.2
            $found_folders[$folder->__toString()] = $localName;
        }

        $this->assertEquals($search_folders, $found_folders);
    }

    public function testSelectable()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $localName => $folder) {
            $this->assertEquals($localName, $folder->getLocalName());
        }
    }


    public function testCount()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);

        $mail->selectFolder('subfolder.test');
        $count = $mail->countMessages();
        $this->assertEquals(1, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);

        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);

        $mail->selectFolder('subfolder.test');
        $sizes = $mail->getSize();
        $this->assertEquals(array(1 => 467), $sizes);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Folder_Maildir($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);

        $mail->selectFolder('subfolder.test');
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Message in subfolder', $subject);
    }

}
