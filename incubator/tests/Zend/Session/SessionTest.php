<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Session
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Session.php 2060 2006-12-02 19:41:07Z gavin $
 * @since      Preview Release 0.2
 */

// http://en.wikipedia.org/wiki/Black_box_testing

require_once 'Zend.php';
Zend::loadClass('Zend_Session');

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Session
 * @subpackage UnitTests
 */
class Zend_SessionTest extends PHPUnit_Framework_TestCase
{
	private $error_list = array();

    public function setUp()
    {
		$this->old_error_reporting_level = error_reporting( E_ALL | E_STRICT );
		set_error_handler ( array ( &$this, 'errorHandler' ) );
	}

    public function tearDown()
    {
		$old = error_reporting( E_ALL | E_STRICT );
        $this->assertTrue ( $old === (error_reporting( E_ALL | E_STRICT )), 'something associated with a particular test altered error_reporting to something other than E_STRICT');
		restore_error_handler();
        Zend_Session::unLockAll();
        // Zend_Session_Core::removeInstance();
        if (count($this->error_list)) {
            echo "**** Errors: ";
            var_dump($this->error_list);
        }

        // unset all namespaces
        $core = Zend_Session_Core::getInstance();
        foreach(Zend_Session_Core::getIterator() as $space) {
            $core->namespaceUnset($space);
        }
	}

    public function errorHandler ( $errno, $errstr, $errfile, $errline )
    {
		$this->error_list[] = array ( 'number' => $errno, 'string' => $errstr, 'file' => $errfile, 'line' => $errline );
	}

    /**
     * test for initialisation without parameter
     * expected instance
     */
    public function testInit()
    {
        $s = new Zend_Session();
        $this->assertTrue($s instanceof Zend_Session,'Zend_Session Object not returned');
    }

    /**
     * test for initialisation with empty string
     * expected instance
     */
    public function testInitEmpty()
    {
        try {
            $s = new Zend_Session('');
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when trying to create a namespace having the empty string as its name; expected Zend_Session_Exception'); 
    }

    /**
     * test magic methods with improper variable interpolation
     * expected instance
     */
    public function testMagicMethodsEmpty()
    {
        $s = new Zend_Session();
        $name = 'fruit';
        $s->$name = 'apples';
        $this->assertTrue(isset($s->fruit), 'isset() failed - returned false, but should have been true');

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            $s->$name = 'pear';
            $this->fail('No exception was returned when trying to __set() a key named ""; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            $nothing = $s->$name;
            $this->fail('No exception was returned when trying to __set() a key named ""; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            if (isset($s->$name)) { true; }
            $this->fail('No exception was returned when trying to __set() a key named ""; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            unset($s->$name);
            $this->fail('No exception was returned when trying to __set() a key named ""; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }
    }

    /**
     * test for initialisation with Session parameter
     * expected instance
     */
    public function testInitSession()
    {
        $s = new Zend_Session('namespace');
        $this->assertTrue($s instanceof Zend_Session,'Zend_Session Object not returned');
    }

    /**
     * test for initialisation with single instance
     * expected instance
     */
    public function testInitSingleInstance()
    {
        $s = new Zend_Session('single', true);
        try {
            $s = new Zend_Session('single', true);
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/single.*only instance/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when creating a duplicate session for the same namespace, even though "single instance" was specified; expected Zend_Session_Exception'); 
    }

    /**
     * test for namespacing
     * expected instance
     */
    public function testInitNamespaces()
    {
        $s1 = new Zend_Session('namespace1');
        $s1b = new Zend_Session('namespace1');
        $s2 = new Zend_Session('namespace2');
        $s2b = new Zend_Session('namespace2');
        $s3 = new Zend_Session();
        $s3b = new Zend_Session();
        $s1->a = 'apple';
        $s2->a = 'pear';
        $s3->a = 'orange';
        $this->assertTrue(($s1->a != $s2->a && $s1->a != $s3->a && $s2->a != $s3->a),'Zend_Session improperly shared namespaces');
        $this->assertTrue(($s1->a === $s1b->a),'Zend_Session namespace error');
        $this->assertTrue(($s2->a === $s2b->a),'Zend_Session namespace error');
        $this->assertTrue(($s3->a === $s3b->a),'Zend_Session namespace error');
    }

    /**
     * test iteration
     * expected instance
     */
    public function testGetIterator()
    {
        $s = new Zend_Session();
        $s->a = 'apple';
        $s->p = 'pear';
        $s->o = 'orange';
        $result = '';
        foreach ($s->getIterator() as $key => $val)
            $result .= "$key === $val;";
        $this->assertTrue($result === 'a === apple;p === pear;o === orange;','iteration over default Zend_Session namespace failed: result="'.$result.'"');
        $s = new Zend_Session('namespace');
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';
        $result = '';
        foreach ($s->getIterator() as $key => $val)
            $result .= "$key === $val;";
        $this->assertTrue($result === 'g === guava;p === plum;','iteration over named Zend_Session namespace failed');
    }

    /**
     * test locking of the Default namespace (i.e. make namespace readonly)
     * expected instance
     */
    public function testLock()
    {
        $s = new Zend_Session();
        $s->a = 'apple';
        $s->p = 'pear';
        $s->lock();
        try {
            $s->o = 'orange';
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when setting a variable in the "Default" namespace, after marking the namespace as read-only; expected Zend_Session_Exception'); 
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s->lock();
        try {
            $s->o = 'orange';
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when setting a variable in the "Default" namespace, after marking the namespace as read-only; expected Zend_Session_Exception'); 
    }

    /**
     * test locking of named namespaces (i.e. make namespace readonly)
     * expected instance
     */
    public function testLockNamespace()
    {
        $s = new Zend_Session('somenamespace');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->lock();
        try {
            $s->o = 'orange';
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when setting a variable in the "Default" namespace, after marking the namespace as read-only; expected Zend_Session_Exception'); 
        $s = new Zend_Session('somenamespace');
        $s2 = new Zend_Session('mayday');
        $s2->lock();
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session('somenamespace');
        $s->lock();
        $s2->unLock();
        try {
            $s->o = 'orange';
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when setting a variable in the "Default" namespace, after marking the namespace as read-only; expected Zend_Session_Exception'); 
    }

    /**
     * test unlocking of the Default namespace (i.e. make namespace readonly)
     * expected instance
     */
    public function testUnLock()
    {
        $s = new Zend_Session();
        $s->a = 'apple';
        $s->p = 'pear';
        $s->lock();
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'prune';
        $s->lock();
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
    }

    /**
     * test unlocking of the Default namespace (i.e. make namespace readonly)
     * expected instance
     */
    public function testUnLockAll()
    {
        $sessions = array('one', 'two', 'default', 'three');
        foreach($sessions as $namespace) {
            $s = new Zend_Session($namespace);
            $s->a = 'apple';
            $s->p = 'pear';
            $s->lock();
            $s->unLock();
            $s->o = 'orange';
            $s->p = 'prune';
            $s->lock();
            $s->unLock();
            $s->o = 'orange';
            $s->p = 'papaya';
            $s->c = 'cherry';
            $s->lock();
            $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (not locked)');
            try {
                $s->p = 'prune';
                $s->f = 'fig';
                $this->fail('No exception was returned when setting a variable in the "Default" namespace, after marking the namespace as read-only; expected Zend_Session_Exception'); 
            } catch (Zend_Session_Exception $e) {
                $this->assertRegexp('/read.only/i', $e->getMessage());
            }
        }
        $s->unLockAll();
        foreach($sessions as $namespace) {
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->p = 'pear';
            $s->f = 'fig';
            $s->l = 'lime';
        }
    }

    /**
     * test unlocking of the Default namespace (i.e. make namespace readonly)
     * expected instance
     */
    public function testIsLocked()
    {
        $s = new Zend_Session();
        $s->a = 'apple';
        $s->p = 'pear';
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        $s->lock();
        $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'prune';
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        $s->lock();
        $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
        $s->unLock();
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
    }

    /**
     * test unlocking of named namespaces (i.e. make namespace readonly)
     * expected instance
     */
    public function testUnLockNamespace()
    {
        $s = new Zend_Session('somenamespace');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->lock();
        $s2 = new Zend_Session('mayday');
        $s2->lock();
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'prune';
        $s->lock();
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
    }

    /**
     * test isLocked unary comparison operator
     * expected instance
     */
    public function testIsLockedNamespace()
    {
        $s = new Zend_Session('somenamespace');
        $s->a = 'apple';
        $s->p = 'pear';
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        $s->lock();
        $s2 = new Zend_Session('mayday');
        $s2->lock();
        $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
        $s->unLock();
        $s->o = 'orange';
        $s->p = 'prune';
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        $s->lock();
        $s2->unLock();
        $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
        $s->unLock();
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
        $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
    }

    /**
     * test unsetAll keys in default namespace
     * expected instance
     */
    public function testUnsetAll()
    {
        $s = new Zend_Session();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "tearDown failure, found keys in default namespace: '$result'");
        $s->a = 'apple';
        $s->lock();
        $s->unLock();
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue($result === 'a === apple;p === papaya;c === cherry;', "unsetAll() setup for test failed: '$result'");
        $s->unsetAll();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "unsetAll() did not remove keys from namespace: '$result'");
    }

    /**
     * test unsetAll keys in default namespace
     * expected instance
     */
    public function testUnsetAllNamespace()
    {
        $s = new Zend_Session('somenamespace');
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "tearDown failure, found keys in 'somenamespace' namespace: '$result'");
        $s->a = 'apple';
        $s->lock();
        $s->unLock();
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session('somenamespace');
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue($result === 'a === apple;p === papaya;c === cherry;', "unsetAll() setup for test failed: '$result'");
        $s->unsetAll();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "unsetAll() did not remove keys from namespace: '$result'");
    }

    /**
     * test expiration of namespaces and namespace variables by seconds
     * expected instance
     */
    public function testSetExpirationSeconds()
    {
        $s = new Zend_Session('expireAll');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->o = 'orange';
        $s->setExpirationSeconds(5);

        $id = session_id();
        session_write_close(); // release session so process below can use it
        sleep(4); // not long enough for things to expire
        exec("/usr/bin/php SessionTestHelper.php expireAll $id expireAll", $result);
        $result = array_pop($result);
        session_start(); // resume session
        file_put_contents('result.txt', $result);
        $this->assertTrue($result === 'a === apple;p === pear;o === orange;',"iteration over default Zend_Session namespace failed (result=$result)");

        $s = new Zend_Session('expireGuava');
        $s->setExpirationSeconds(5, 'g');
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';

        session_write_close(); // release session so process below can use it
        sleep(6); // not long enough for things to expire
        exec("/usr/bin/php SessionTestHelper.php expireAll $id expireGuava", $result);
        $result = array_pop($result);
        session_start(); // resume session
        file_put_contents('result.txt', $result);
        $this->assertTrue($result === 'p === plum;',"iteration over named Zend_Session namespace failed (result=$result)");
    }

    /**
     * test expiration of namespaces and namespace variables by hops
     * expected instance
     */
    public function testSetExpirationHops()
    {
        $s = new Zend_Session('expireAll');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->o = 'orange';
        $s->setExpirationHops(5);

        $id = session_id();
        session_write_close(); // release session so process below can use it
        sleep(4); // not long enough for things to expire
        exec("/usr/bin/php SessionTestHelper.php expireAll $id expireAll", $result);
        $result = array_pop($result);
        session_start(); // resume session
        file_put_contents('result.txt', $result);
        $this->assertTrue($result === 'a === apple;p === pear;o === orange;',"iteration over default Zend_Session namespace failed (result=$result)");

        $s = new Zend_Session('expireGuava');
        $s->setExpirationHops(5, 'g');
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';

        session_write_close(); // release session so process below can use it
        sleep(6); // not long enough for things to expire
        exec("/usr/bin/php SessionTestHelper.php expireAll $id expireGuava", $result);
        $result = array_pop($result);
        session_start(); // resume session
        file_put_contents('result.txt', $result);
        $this->assertTrue($result === 'p === plum;',"iteration over named Zend_Session namespace failed (result=$result)");
        $s = new Zend_Session('expireGuava');
        $s->setExpirationHops(5, 'g', true);
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';

        session_write_close(); // release session so process below can use it
        sleep(6); // not long enough for things to expire
        exec("/usr/bin/php SessionTestHelper.php expireAll $id expireGuava", $result);
        $result = array_pop($result);
        session_start(); // resume session
        file_put_contents('result.txt', $result);
        $this->assertTrue($result === 'p === plum;',"iteration over named Zend_Session namespace failed (result=$result)");
    }
}