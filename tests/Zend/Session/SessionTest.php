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
 * @version    $Id$
 */

// http://en.wikipedia.org/wiki/Black_box_testing

require_once 'Zend/Session.php';

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
    private $script = null;

    private $error_list = array();

    private $savePath;

    public function __construct() {
        $this->script = "php " . (dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'SessionTestHelper.php ';
        /*
         * You should also add a *custom*:
         * Zend_Session::setOptions(array('save_path' => 'See http://framework.zend.com/wiki/x/bTU'));
         * The correct value is system dependent, and should be provided by the developer using
         * an absolute path to a directory readable and writable by the PHP process.
         */
    }

    public function setUp()
    {
        $this->old_error_reporting_level = error_reporting( E_ALL | E_STRICT );
        set_error_handler ( array ( &$this, 'errorHandler' ) );
    }

    public function tearDown()
    {
        if (isset($this->savePath)) {
            Zend_Session::setOptions(array('save_path' => $this->savePath));
            unset($this->savePath);
        }

        $old = error_reporting( E_ALL | E_STRICT );
        $this->assertTrue ( $old === (error_reporting( E_ALL | E_STRICT )),
            'something associated with a particular test altered error_reporting to something other than E_STRICT');
        restore_error_handler();
        Zend_Session_Namespace::unlockAll();
        // @todo: cleanup
        if (count($this->error_list)) {
            echo "**** Errors: ";
            print_r($this->error_list);
        }

        // unset all namespaces
        foreach(Zend_Session::getIterator() as $space) {
            try {
                Zend_Session::namespaceUnset($space);
            } catch (Zend_Session_Exception $e) {
                $this->assertRegexp('/read.only/i', $e->getMessage());
                return;
            }
        }
    }

    public function errorHandler ( $errno, $errstr, $errfile, $errline )
    {
        $this->error_list[] = array ( 'number' => $errno, 'string' => $errstr, 'file' => $errfile, 'line' => $errline );
    }

    /*
     * Sorts the compound result returned by SessionTestHelper, so that the
     * order of iteration over namespace items do not impact analysis of test results.
     *
     * @param array $result - output of exec()'ing SessionTestHelper
     * @return string - sorted alphabetically
     */
    public function sortResult($result)
    {
        $results = explode(';', array_pop($result));
        sort($results);
        return implode(';', $results);
    }

    /*
     * test session id manipulations
     * expect isRegenerated flag == true
     */
    public function testRegenerateId()
    {
        Zend_Session::setId('myid123');
        Zend_Session::regenerateId();

        $this->assertFalse(Zend_Session::isRegenerated());
        $id = Zend_Session::getId();
        $this->assertTrue($id === 'myid123',
            'getId() reported something different than set via setId("myid123")');

        Zend_Session::start();
        $this->assertTrue(Zend_Session::isRegenerated());

        try {
            Zend_Session::setId($id);
            $this->fail('No exception was returned when trying to set the session id, after session_start()');
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/already.*started/i', $e->getMessage());
        }
    }

    /*
     * test setting core options
     * expect no exceptions
     */
    public function testSetOptions()
    {
        $this->savePath = ini_get('session.save_path');

        try {
            Zend_Session::setOptions(array('foo' => 'break me'));
            $this->fail('No exception was returned when trying to set an invalid option');
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/unknown.option/i', $e->getMessage());
        }
        try {
            Zend_Session::setOptions(array('save_path' => '1;777;/tmp'));
            Zend_Session::setOptions(array('save_path' => '2;/tmp'));
            Zend_Session::setOptions(array('save_path' => '/tmp'));
        } catch (Zend_Session_Exception $e) {
            var_dump($e);
            $this->fail('No exception was expected when using a save_path of "1;777;/tmp"');
        }
        try {
            Zend_Session::setOptions(array('save_path' => '/totallybogussavepath'));
            $this->fail('No exception was returned when trying to set an invalid save_path');
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/Unwritable session/i', $e->getMessage());
        }
    }

    /**
     * test for initialisation without parameter
     * expect instance
     */
    public function testInit()
    {
        $s = new Zend_Session_Namespace();
        $this->assertTrue($s instanceof Zend_Session_Namespace,'Zend_Session Object not returned');
    }

    /**
     * test for initialisation with empty string
     * expect failure
     */
    public function testInitEmpty()
    {
        try {
            $s = new Zend_Session_Namespace('');
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when trying to create a namespace having the empty string as '
            . 'its name; expected Zend_Session_Exception'); 
    }

    /**
     * test for initialisation with Session parameter
     * expect instance
     */
    public function testInitSession()
    {
        $s = new Zend_Session_Namespace('namespace');
        $this->assertTrue($s instanceof Zend_Session_Namespace,'Zend_Session Object not returned');
    }

    /**
     * test for initialisation with single instance
     * expected instance
     */
    public function testInitSingleInstance()
    {
        $s = new Zend_Session_Namespace('single', true);
        try {
            $s = new Zend_Session_Namespace('single', true);
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/already.*exist/i', $e->getMessage());
            return;
        }
        $this->fail('No exception was returned when creating a duplicate session for the same namespace, '
            . 'even though "single instance" was specified; expected Zend_Session_Exception'); 
    }

    /**
     * test for retrieval of non-existent keys in a valid namespace
     * expected null value returned by getter for an unset key
     */
    public function testNamespaceGetNull()
    {
        try {
            $s = new Zend_Session_Namespace();
            $s->tree = 'fig';
            $dog = $s->dog;
            $this->assertTrue($dog === null, "getting value of non-existent key failed to return null ($dog)");
        } catch (Zend_Session_Exception $e) {
            $this->fail('Unexpected exception returned when attempting to fetch the value of non-existent key');
        }
    }

    /**
     * test for existence of namespace
     * expected true
     */
    public function testNamespaceIsset()
    {
        try {
            $this->assertFalse(Zend_Session::namespaceIsset('trees'), 
                'namespaceIsset() should have returned false for a namespace with no keys set');
            $s = new Zend_Session_Namespace('trees');
            $this->assertFalse(Zend_Session::namespaceIsset('trees'), 
                'namespaceIsset() should have returned false for a namespace with no keys set');
            $s->cherry = 'bing';
            $this->assertTrue(Zend_Session::namespaceIsset('trees'), 
                'namespaceIsset() should have returned true for a namespace with keys set');
        } catch (Zend_Session_Exception $e) {
            $this->fail('Unexpected exception returned when attempting to fetch the value of non-existent key');
        }
    }

    /**
     * test magic methods with improper variable interpolation
     * expect no exceptions
     */
    public function testMagicMethodsEmpty()
    {
        $s = new Zend_Session_Namespace();
        $name = 'fruit';
        $s->$name = 'apples';
        $this->assertTrue(isset($s->fruit), 'isset() failed - returned false, but should have been true');

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            $s->$name = 'pear';
            $this->fail('No exception was returned when trying to __set() a key named ""; expected '
                . 'Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            $nothing = $s->$name;
            $this->fail('No exception was returned when trying to __set() a key named ""; expected '
                . 'Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            if (isset($s->$name)) { true; }
            $this->fail('No exception was returned when trying to __set() a key named ""; expected '
                . 'Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }

        try {
            $name = ''; // simulate a common bug, where user refers to an unset/empty variable
            unset($s->$name);
            $this->fail('No exception was returned when trying to __set() a key named ""; expected '
                . 'Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/non.empty.string/i', $e->getMessage());
        }
    }

    /**
     * test for proper separation of namespace "spaces"
     * expect variables in different namespaces are different variables (i.e. not shared values)
     */
    public function testInitNamespaces()
    {
        $s1 = new Zend_Session_Namespace('namespace1');
        $s1b = new Zend_Session_Namespace('namespace1');
        $s2 = new Zend_Session_Namespace('namespace2');
        $s2b = new Zend_Session_Namespace('namespace2');
        $s3 = new Zend_Session_Namespace();
        $s3b = new Zend_Session_Namespace();
        $s1->a = 'apple';
        $s2->a = 'pear';
        $s3->a = 'orange';
        $this->assertTrue(($s1->a != $s2->a && $s1->a != $s3->a && $s2->a != $s3->a),
            'Zend_Session improperly shared namespaces');
        $this->assertTrue(($s1->a === $s1b->a),'Zend_Session namespace error');
        $this->assertTrue(($s2->a === $s2b->a),'Zend_Session namespace error');
        $this->assertTrue(($s3->a === $s3b->a),'Zend_Session namespace error');
    }

    /**
     * test for detection of illegal namespace names
     * expect exception complaining about name beginning with an underscore
     */
    public function testInitNamespaceUnderscore()
    {
        try {
            $s = new Zend_Session_Namespace('_namespace');
            $this->fail('No exception was returned when requesting a namespace having a name beginning with '
                . 'an underscore');
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/underscore/i', $e->getMessage());
        }
    }

    /**
     * test iteration
     * expect native PHP foreach statement is able to properly iterate all items in a session namespace
     */
    public function testGetIterator()
    {
        $s = new Zend_Session_Namespace();
        $s->a = 'apple';
        $s->p = 'pear';
        $s->o = 'orange';
        $result = '';
        foreach ($s->getIterator() as $key => $val)
            $result .= "$key === $val;";
        $this->assertTrue($result === 'a === apple;p === pear;o === orange;',
            'iteration over default Zend_Session namespace failed: result="'.$result.'"');
        $s = new Zend_Session_Namespace('namespace');
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';
        $result = '';
        foreach ($s->getIterator() as $key => $val)
            $result .= "$key === $val;";
        $this->assertTrue($result === 'g === guava;p === plum;',
            'iteration over named Zend_Session namespace failed');
    }

    /**
     * test locking of the Default namespace (i.e. make namespace readonly)
     * expect exceptions when trying to write to locked namespace
     */
    public function testLock()
    {
        $s = new Zend_Session_Namespace();
        $s->a = 'apple';
        $s->p = 'pear';
        $s->lock();
        try {
            $s->o = 'orange';
            $this->fail('No exception was returned when setting a variable in the "Default" namespace, '
                . 'after marking the namespace as read-only; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
        }
        $s->unlock();
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s->lock();
        try {
            $s->o = 'orange';
            $this->fail('No exception was returned when setting a variable in the "Default" namespace, '
                . 'after marking the namespace as read-only; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
        }
    }

    /**
     * test locking of named namespaces (i.e. make namespace readonly)
     * expect exceptions when trying to write to locked namespace
     */
    public function testLockNamespace()
    {
        $s = new Zend_Session_Namespace('somenamespace');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->lock();
        try {
            $s->o = 'orange';
            $this->fail('No exception was returned when setting a variable in the "Default" namespace, '
                . 'after marking the namespace as read-only; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            // session namespace 'single' already exists and is set to be the only instance of this namespace
            $this->assertRegexp('/read.only/i', $e->getMessage());
        }
        $s = new Zend_Session_Namespace('somenamespace');
        $s2 = new Zend_Session_Namespace('mayday');
        $s2->lock();
        $s->unlock();
        $s->o = 'orange';
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session_Namespace('somenamespace');
        $s->lock();
        $s2->unlock();
        try {
            $s->o = 'orange';
            $this->fail('No exception was returned when setting a variable in the "Default" namespace, '
                . 'after marking the namespace as read-only; expected Zend_Session_Exception'); 
        } catch (Zend_Session_Exception $e) {
            $this->assertRegexp('/read.only/i', $e->getMessage());
        }
    }

    /**
     * test unlocking of the Default namespace (i.e. make namespace readonly)
     * expected no exceptions
     */
    public function testUnlock()
    {
        $s = new Zend_Session_Namespace();
        try {
            $s->a = 'apple';
            $s->p = 'pear';
            $s->lock();
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'prune';
            $s->lock();
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'papaya';
            $s->c = 'cherry';
        } catch (Zend_Session_Exception $e) {
            $this->fail('Unexpected exception when writing to namespaces after unlocking it.');
        }
    }

    /**
     * test combinations of locking and unlocking of the Default namespace (i.e. make namespace readonly)
     * expected no exceptions
     */
    public function testUnLockAll()
    {
        $sessions = array('one', 'two', 'default', 'three');
        foreach($sessions as $namespace) {
            $s = new Zend_Session_Namespace($namespace);
            $s->a = 'apple';
            $s->p = 'pear';
            $s->lock();
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'prune';
            $s->lock();
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'papaya';
            $s->c = 'cherry';
            $s->lock();
            $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (not locked)');
            try {
                $s->p = 'prune';
                $s->f = 'fig';
                $this->fail('No exception was returned when setting a variable in the "Default" namespace, '
                    . 'after marking the namespace as read-only; expected Zend_Session_Exception'); 
            } catch (Zend_Session_Exception $e) {
                $this->assertRegexp('/read.only/i', $e->getMessage());
            }
        }
        $s->unlockAll();
        foreach($sessions as $namespace) {
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->p = 'pear';
            $s->f = 'fig';
            $s->l = 'lime';
        }
    }

    /**
     * test isLocked() unary comparison operator under various situations
     * expect lock status remains synchronized with last call to unlock() or lock()
     * expect no exceptions
     */
    public function testIsLocked()
    {
        try {
            $s = new Zend_Session_Namespace();
            $s->a = 'apple';
            $s->p = 'pear';
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->lock();
            $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'prune';
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->lock();
            $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
            $s->unlock();
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->o = 'orange';
            $s->p = 'papaya';
            $s->c = 'cherry';
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        } catch (Zend_Session_Exception $e) {
            $this->fail('Unexpected exception when writing to named namespaces after unlocking them.');
        }
    }

    /**
     * test unlocking of named namespaces (i.e. make namespace readonly)
     * expect no exceptions
     */
    public function testUnLockNamespace()
    {
        $s = new Zend_Session_Namespace('somenamespace');
        try {
            $s->a = 'apple';
            $s->p = 'pear';
            $s->lock();
            $s2 = new Zend_Session_Namespace('mayday');
            $s2->lock();
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'prune';
            $s->lock();
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'papaya';
            $s->c = 'cherry';
        } catch (Zend_Session_Exception $e) {
            $this->fail('Unexpected exception when writing to named namespaces after unlocking them.');
        }
    }

    /**
     * test isLocked() unary comparison operator under various situations
     * expect lock status remains synchronized with last call to unlock() or lock()
     * expect no exceptions
     */
    public function testIsLockedNamespace()
    {
        try {
            $s = new Zend_Session_Namespace('somenamespace');
            $s->a = 'apple';
            $s->p = 'pear';
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->lock();
            $s2 = new Zend_Session_Namespace('mayday');
            $s2->lock();
            $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
            $s->unlock();
            $s->o = 'orange';
            $s->p = 'prune';
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->lock();
            $s2->unlock();
            $this->assertTrue($s->isLocked(), 'isLocked() returned incorrect status (unlocked)');
            $s->unlock();
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
            $s->o = 'orange';
            $s->p = 'papaya';
            $s->c = 'cherry';
            $this->assertFalse($s->isLocked(), 'isLocked() returned incorrect status (locked)');
        } catch (Zend_Session_Exception $e) {
            $this->fail('Unexpected exception when writing to named namespaces after unlocking them.');
        }
    }

    /**
     * test unsetAll keys in default namespace
     * expect namespace contains only keys not unset()
     */
    public function testUnsetAll()
    {
        $s = new Zend_Session_Namespace();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "tearDown failure, found keys in default namespace: '$result'");
        $s->a = 'apple';
        $s->lock();
        $s->unlock();
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session_Namespace();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue($result === 'a === apple;p === papaya;c === cherry;',
            "unsetAll() setup for test failed: '$result'");
        $s->unsetAll();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "unsetAll() did not remove keys from namespace: '$result'");
    }

    /**
     * test unset() keys in default namespace
     * expect namespace contains only keys not unset()
     */
    public function testUnset()
    {
        $s = new Zend_Session_Namespace();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "tearDown failure, found keys in default namespace: '$result'");
        $s->a = 'apple';
        $s->lock();
        $s->unlock();
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session_Namespace();
        foreach ($s->getIterator() as $key => $val) {
            unset($s->$key);
        }
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "unsetAll() did not remove keys from namespace: '$result'");
    }


    /**
     * test unset() keys in non-default namespace
     * expect namespace contains only keys not unset()
     */
    public function testUnsetNamespace()
    {
        $s = new Zend_Session_Namespace('foobar');
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "tearDown failure, found keys in default namespace: '$result'");
        $s->a = 'apple';
        $s->lock();
        $s->unlock();
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session_Namespace('foobar');
        foreach ($s->getIterator() as $key => $val) {
            unset($s->$key);
        }
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "unsetAll() did not remove keys from namespace: '$result'");
    }

    /**
     * test unsetAll keys in default namespace
     * expect namespace will contain no keys
     */
    public function testUnsetAllNamespace()
    {
        $s = new Zend_Session_Namespace('somenamespace');
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "tearDown failure, found keys in 'somenamespace' namespace: '$result'");
        $s->a = 'apple';
        $s->lock();
        $s->unlock();
        $s->p = 'papaya';
        $s->c = 'cherry';
        $s = new Zend_Session_Namespace('somenamespace');
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue($result === 'a === apple;p === papaya;c === cherry;',
            "unsetAll() setup for test failed: '$result'");
        $s->unsetAll();
        $result = '';
        foreach ($s->getIterator() as $key => $val) {
            $result .= "$key === $val;";
        }
        $this->assertTrue(empty($result), "unsetAll() did not remove keys from namespace: '$result'");
    }

    /**
     * test expiration of namespaces and namespace variables by seconds
     * expect expiration of specified keys/namespace
     */
    public function testSetExpirationSeconds()
    {
        $s = new Zend_Session_Namespace('expireAll');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->o = 'orange';
        $s->setExpirationSeconds(5);

        Zend_Session::regenerateId();
        $id = Zend_Session::getId();
        session_write_close(); // release session so process below can use it
        sleep(4); // not long enough for things to expire
        exec($this->script . "expireAll $id expireAll", $result);
        $result = $this->sortResult($result);
        $expect = ';a === apple;o === orange;p === pear';
        $this->assertTrue($result === $expect, 
            "iteration over default Zend_Session namespace failed; expecting result === '$expect', but got '$result'");

        sleep(2); // long enough for things to expire (total of 6 seconds waiting, but expires in 5)
        exec($this->script . "expireAll $id expireAll", $result);
        $result = array_pop($result);
        $this->assertTrue($result === '',
            "iteration over default Zend_Session namespace failed; expecting result === '', but got '$result')");
        session_start(); // resume artificially suspended session

        // We could split this into a separate test, but actually, if anything leftover from above
        // contaminates the tests below, that is also a bug that we want to know about.
        $s = new Zend_Session_Namespace('expireGuava');
        $s->setExpirationSeconds(5, 'g'); // now try to expire only 1 of the keys in the namespace
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';

        session_write_close(); // release session so process below can use it
        sleep(6); // not long enough for things to expire
        exec($this->script . "expireAll $id expireGuava", $result);
        $result = $this->sortResult($result);
        session_start(); // resume artificially suspended session
        $this->assertTrue($result === ';p === plum',
            "iteration over named Zend_Session namespace failed (result=$result)");
    }

    /**
     * test expiration of namespaces by hops
     * expect expiration of specified namespace in the proper number of hops
     */
    public function testSetExpireSessionHops()
    {
        $s = new Zend_Session_Namespace('expireAll');
        $s->a = 'apple';
        $s->p = 'pear';
        $s->o = 'orange';
        $expireBeforeHop = 3;
        $s->setExpirationHops($expireBeforeHop);

        $id = session_id();
        session_write_close(); // release session so process below can use it

        for ($i = 1; $i <= ($expireBeforeHop+2); $i++) {
            exec($this->script . "expireAll $id expireAll", $result);
            $result = $this->sortResult($result);
            if ($i > $expireBeforeHop) {
                $this->assertTrue($result === '',
                    "iteration over default Zend_Session namespace failed (result='$result'; hop #$i)");
            } else {
                $this->assertTrue($result === ';a === apple;o === orange;p === pear',
                    "iteration over default Zend_Session namespace failed (result='$result'; hop #$i)");
            }
        }
        session_start(); // resume artificially suspended session
    }

    /**
     * test expiration of namespace variables by hops
     * expect expiration of specified keys in the proper number of hops
     */
    public function testSetExpireSessionVarsByHops1()
    {
        $this->setExpireSessionVarsByHops();
    }

    /* sanity check .. we should be able to repeat this test without problems */
    public function testSetExpireSessionVarsByHops2()
    {
        $this->setExpireSessionVarsByHops();
    }

    /**
     * test expiration of namespace variables by hops
     * expect expiration of specified keys in the proper number of hops
     */
    public function setExpireSessionVarsByHops()
    {
        $s = new Zend_Session_Namespace('expireGuava');
        $expireBeforeHop = 4;
        $s->setExpirationHops($expireBeforeHop, 'g');
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';

        $id = session_id();
        session_write_close(); // release session so process below can use it

        for ($i = 1; $i <= ($expireBeforeHop +2); $i++) {
            exec($this->script . "expireAll $id expireGuava", $result);
            $result = $this->sortResult($result);
            if ($i > $expireBeforeHop) {
                $this->assertTrue($result === ';p === plum',
                    "iteration over named Zend_Session namespace failed (result='$result'; hop #$i)");
            } else {
                $this->assertTrue($result === ';g === guava;p === plum',
                    "iteration over named Zend_Session namespace failed (result='$result'; hop #$i)");
            }
        }

        session_start(); // resume artificially suspended session
    }

    public function testArrays()
    {
        $s = new Zend_Session_Namespace('aspace');
        // http://framework.zend.com/issues/browse/ZF-800
        $this->markTestIncomplete(); // PHP 5.2.1 is required (fixes a bug with magic __get() returning by reference)
        $id = Zend_Session::getId();
        $this->assertSame($id, session_id());
        $s->top = 'begin';
        session_write_close(); // release session so process below can use it
        exec($this->script . "setArray $id aspace 1 2 3 4 5", $result);
        exec($this->script . "getArray $id aspace", $result);
        $result = array_pop($result);
        $expect = 'top === begin;astring === happy;someArray === Array;(;[0]=>aspace;[1]=>1;[2]=>2;[3]=>3;[4]=>4;[5]=>5;[bee]=>honey;[ant]=>sugar;[dog]=>cat;);;serializedArray === a:8:{i:0;s:6:"aspace";i:1;s:1:"1";i:2;s:1:"2";i:3;s:1:"3";i:4;s:1:"4";i:5;s:1:"5";s:3:"ant";s:5:"sugar";s:3:"dog";s:3:"cat";};';
        $this->assertTrue($result === $expect,
            "iteration over default Zend_Session namespace failed; expecting result ===\n$expect\n, but got\n$result\n)");
        session_start(); // resume artificially suspended session
    }

    /**
     * test expiration of namespace variables by hops
     * expect expiration of specified keys in the proper number of hops
     */
    public function testSetExpireSessionVarsByHopsOnUse()
    {
        $s = new Zend_Session_Namespace('expireGuava');
        $expireBeforeHop = 2;
        $s->setExpirationHops($expireBeforeHop, 'g', true); // only count a hop, when namespace is used
        $s->g = 'guava';
        $s->p = 'peach';
        $s->p = 'plum';

        $id = session_id();
        session_write_close(); // release session so process below can use it
        // we are not accessing (using) the "expireGuava" namespace, so these hops should have no effect
        for ($i = 1; $i <= ($expireBeforeHop +2); $i++) {
            exec($this->script . "expireAll $id notused", $result);
            $result = $this->sortResult($result);
            $this->assertTrue($result === '',
                    "iteration over named Zend_Session namespace failed (result='$result'; hop #$i)");
        }

        for ($i = 1; $i <= ($expireBeforeHop +2); $i++) {
            exec($this->script . "expireAll $id expireGuava", $result);
            $result = $this->sortResult($result);
            if ($i > $expireBeforeHop) {
                $expect = ';p === plum';
                $this->assertTrue($result === $expect,
                    "unexpected results iterating over named Zend_Session namespace (result='$result'; expected '$expect'; hop #$i)");
            } else {
                $expect = ';g === guava;p === plum';
                $this->assertTrue($result === $expect,
                    "unexpected results iterating over named Zend_Session namespace (result='$result'; expected '$expect'; hop #$i)");
            }
        }

        session_start(); // resume artificially suspended session
        Zend_Session::destroy();
    }

    // DO NOT put tests below testSetExpireSessionVarsByHopsOnUse(), since the session is destroy()'d.
}
