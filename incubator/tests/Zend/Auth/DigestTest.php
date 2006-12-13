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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * Zend_Auth_Digest_Adapter
 */
require_once 'Zend/Auth/Digest/Adapter.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_DigestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . '/_files';
    }

    /**
     * Ensures that an exception is thrown upon authenticating against a nonexistent file
     *
     * @return void
     */
    public function testFileNonExistentException()
    {
        $options = array(
            'filename' => 'nonexistent',
            'realm'    => 'realm',
            'username' => 'username',
            'password' => 'password'
            );
        try {
            $token = Zend_Auth_Digest_Adapter::staticAuthenticate($options);
            $this->fail('Expected Zend_Auth_Digest_Exception not thrown upon authenticating against nonexistent '
                      . 'file');
        } catch (Zend_Auth_Digest_Exception $e) {
            $this->assertContains('Cannot open', $e->getMessage());
        }
    }

    /**
     * Ensures that static digest authentication succeeds as expected
     *
     * @return void
     */
    public function testStaticAuthenticate()
    {
        $options = array(
            'filename' => "$this->_filesPath/.htdigest.1",
            'realm'    => 'Some Realm',
            'username' => 'someUser',
            'password' => 'somePassword'
            );

        $token = Zend_Auth_Digest_Adapter::staticAuthenticate($options);
        $this->assertTrue($token->isValid());
        $identity = $token->getIdentity();
        $this->assertTrue($identity['realm'] === $options['realm']);
        $this->assertTrue($identity['username'] === $options['username']);
    }

    /**
     * Ensures expected behavior upon incorrect password
     *
     * @return void
     */
    public function testIncorrectPassword()
    {
        $options = array(
            'filename' => "$this->_filesPath/.htdigest.1",
            'realm'    => 'Some Realm',
            'username' => 'someUser',
            'password' => 'incorrectPassword'
            );

        $token = Zend_Auth_Digest_Adapter::staticAuthenticate($options);
        $this->assertFalse($token->isValid());
        $this->assertContains('Password incorrect', $token->getMessage());
        $identity = $token->getIdentity();
        $this->assertTrue($identity['realm'] === $options['realm']);
        $this->assertTrue($identity['username'] === $options['username']);
    }

    /**
     * Ensures expected behavior upon user not found in existing realm
     *
     * @return void
     */
    public function testUserNonexistentRealmExists()
    {
        $options = array(
            'filename' => "$this->_filesPath/.htdigest.1",
            'realm'    => 'Some Realm',
            'username' => 'nonexistentUser',
            'password' => 'somePassword'
            );

        $token = Zend_Auth_Digest_Adapter::staticAuthenticate($options);
        $this->assertFalse($token->isValid());
        $this->assertContains('combination not found', $token->getMessage());
        $identity = $token->getIdentity();
        $this->assertTrue($identity['realm'] === $options['realm']);
        $this->assertTrue($identity['username'] === $options['username']);
    }

    /**
     * Ensures expected behavior upon realm not found for existing user
     *
     * @return void
     */
    public function testUserExistsRealmNonexistent()
    {
        $options = array(
            'filename' => "$this->_filesPath/.htdigest.1",
            'realm'    => 'Nonexistent Realm',
            'username' => 'someUser',
            'password' => 'somePassword'
            );

        $token = Zend_Auth_Digest_Adapter::staticAuthenticate($options);
        $this->assertFalse($token->isValid());
        $this->assertContains('combination not found', $token->getMessage());
        $identity = $token->getIdentity();
        $this->assertTrue($identity['realm'] === $options['realm']);
        $this->assertTrue($identity['username'] === $options['username']);
    }

    /**
     * Ensures that the authenticate method works as expected
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $options1 = array(
            'realm'    => 'Some Realm',
            'username' => 'someUser',
            'password' => 'somePassword'
            );
        $auth1 = new Zend_Auth_Digest_Adapter("$this->_filesPath/.htdigest.1");
        $token1 = $auth1->authenticate($options1);
        $this->assertTrue($token1->isValid());
        $identity1 = $token1->getIdentity();
        $this->assertTrue($identity1['realm'] === $options1['realm']);
        $this->assertTrue($identity1['username'] === $options1['username']);

        $options2 = array(
            'realm'    => 'Another Realm',
            'username' => 'anotherUser',
            'password' => 'anotherPassword'
            );
        $auth2 = new Zend_Auth_Digest_Adapter("$this->_filesPath/.htdigest.2");
        $token2 = $auth2->authenticate($options2);
        $this->assertTrue($token2->isValid());
        $identity2 = $token2->getIdentity();
        $this->assertTrue($identity2['realm'] === $options2['realm']);
        $this->assertTrue($identity2['username'] === $options2['username']);
    }

}