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
        try {
            $token = Zend_Auth_Digest_Adapter::staticAuthenticate('nonexistent', 'realm', 'username', 'password');
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
        $realm    = 'Some Realm';
        $username = 'someUser';
        $password = 'somePassword';

        $token = Zend_Auth_Digest_Adapter::staticAuthenticate("$this->_filesPath/.htdigest.1", $realm, $username,
                                                              $password);
        $this->assertTrue($token->isValid());
        $identity = $token->getIdentity();
        $this->assertTrue($identity['realm'] === $realm);
        $this->assertTrue($identity['username'] === $username);
    }

}