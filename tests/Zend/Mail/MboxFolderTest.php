<?php

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */


/**
 * Zend_Mail_Storage_Folder_Mbox
 */
require_once 'Zend/Mail/Storage/Folder/Mbox.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 */
class Zend_Mail_MboxFolderTest extends PHPUnit_Framework_TestCase
{
    protected $_params;

    public function setUp()
    {
        $this->_params = array();
        $this->_params['dirname'] = dirname(__FILE__) . '/_files/';
        $this->_params['folder']  = 'test.mbox';
    }

    public function testLoadOk()
    {
        try {
            $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        } catch (Exception $e) {
            $this->fail('exception raised while loading mbox folder');
        }
    }

    public function testNoParams()
    {
        try {
            $mail = new Zend_Mail_Storage_Folder_Mbox(array());
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with empty params');
    }

    public function testFilenameParam()
    {
        try {
            // filename is not allowed in this subclass
            $mail = new Zend_Mail_Storage_Folder_Mbox(array('filename' => 'foobar'));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised with filename as param');
    }

    public function testLoadFailure()
    {
        try {
            $mail = new Zend_Mail_Storage_Folder_Mbox(array('dirname' => 'This/Folder/Does/Not/Exist'));
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown dirname');
    }

    public function testLoadUnkownFolder()
    {
        $this->_params['folder'] = 'UnknownFolder';
        try {
            $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while loading unknown folder');
    }

    public function testChangeFolder()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        try {
            $mail->selectFolder('/subfolder/test.mbox');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder');
        }

        $this->assertEquals($mail->getCurrentFolder(), '/subfolder/test.mbox');
    }

    public function testChangeFolderUnselectable()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        try {
            $mail->selectFolder('/subfolder');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while selecting unselectable folder');
    }

    public function testUnknownFolder()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        try {
            $mail->selectFolder('/Unknown/Folder/');
        } catch (Exception $e) {
            return; // test ok
        }

        $this->fail('no exception raised while selecting unknown folder');
    }

    public function testGlobalName()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        try {
            // explicit call of __toString() needed for PHP < 5.2
            $this->assertEquals($mail->getFolders()->subfolder->__toString(), DIRECTORY_SEPARATOR . 'subfolder');
        } catch (Zend_Mail_Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting global name');
        }
    }

    public function testLocalName()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        try {
            $this->assertEquals($mail->getFolders()->subfolder->key(), 'test.mbox');
        } catch (Exception $e) {
            $this->fail('exception raised while selecting existing folder and getting local name');
        }
    }

    public function testIterator()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array(DIRECTORY_SEPARATOR . 'subfolder'                                     => 'subfolder',
                                DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test.mbox' => 'test.mbox',
                                DIRECTORY_SEPARATOR . 'test.mbox'                                     => 'test.mbox');
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
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
        // we search for this folder because we can't assume a order while iterating
        $search_folders = array(DIRECTORY_SEPARATOR . 'subfolder'                                     => 'subfolder',
                                DIRECTORY_SEPARATOR . 'subfolder' . DIRECTORY_SEPARATOR . 'test.mbox' => 'test.mbox',
                                DIRECTORY_SEPARATOR . 'test.mbox'                                     => 'test.mbox');
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
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        $iterator = new RecursiveIteratorIterator($mail->getFolders(), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $localName => $folder) {
            $this->assertEquals($localName, $folder->getLocalName());
        }
    }


    public function testCount()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);

        $count = $mail->countMessages();
        $this->assertEquals(5, $count);

        $mail->selectFolder('/subfolder/test.mbox');
        $count = $mail->countMessages();
        $this->assertEquals(1, $count);
    }

    public function testSize()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);
        $shouldSizes = array(1 => 397, 89, 694, 452, 497);

        $sizes = $mail->getSize();
        $this->assertEquals($shouldSizes, $sizes);

        $mail->selectFolder('/subfolder/test.mbox');
        $sizes = $mail->getSize();
        $this->assertEquals(array(1 => 410), $sizes);
    }

    public function testFetchHeader()
    {
        $mail = new Zend_Mail_Storage_Folder_Mbox($this->_params);

        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Simple Message', $subject);

        $mail->selectFolder('/subfolder/test.mbox');
        $subject = $mail->getMessage(1)->subject;
        $this->assertEquals('Message in subfolder', $subject);
    }
}
