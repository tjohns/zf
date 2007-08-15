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
 * @package    Zend_OpenId
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * Zend_OpenId
 */
require_once 'Zend/OpenId/Provider.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework.php';


/**
 * Zend_OpenId_ResponseHelper
 */
require_once 'Zend/OpenId/ResponseHelper.php';


/**
 * @package    Zend_OpenId
 * @subpackage UnitTests
 */
class Zend_OpenId_ProviderTest extends PHPUnit_Framework_TestCase
{
    const USER     = "http://test_user.myopenid.com/";
    const PASSWORD = "01234567890abcdef";

    private $_user;

    public function __construct()
    {
        $this->_user = new Zend_OpenId_Provider_User_Session();
    }

    /**
     * testing register
     *
     */
    public function testRegister()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);
        $this->assertFalse( $storage->checkUser(self::USER, self::PASSWORD) );

        // wrong ID
        $this->assertFalse( $provider->register("", self::PASSWORD) );
        // registration of new user
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        // registration of existent user
        $this->assertFalse( $provider->register(self::USER, self::PASSWORD) );

        $this->assertTrue( $storage->checkUser(self::USER, md5(self::USER . self::PASSWORD)) );
        $storage->delUser(self::USER);
    }

    /**
     * testing hasUser
     *
     */
    public function testHasUser()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);

        // wrong ID
        $this->assertFalse( $provider->hasUser("", self::PASSWORD) );
        // check for non existent
        $this->assertFalse( $provider->hasUser(self::USER) );
        // check for existent user
        $this->assertTrue( $storage->addUser(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->hasUser(self::USER) );

        $storage->delUser(self::USER);
    }

    /**
     * testing login
     *
     */
    public function testLogin()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);

        // wrong ID
        $this->assertFalse( $provider->login("", self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        // login as non existent user
        $this->assertFalse( $provider->login(self::USER, self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        // login as existent user with wrong password
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertFalse( $provider->login(self::USER, self::PASSWORD . "x") );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        // login as existent user with proper password
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertSame( self::USER, $this->_user->getLoggedInUser() );

        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
    }

    /**
     * testing logout
     *
     */
    public function testLogout()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);

        $this->assertFalse( $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertFalse( $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertSame( self::USER, $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->logout() );
        $this->assertFalse( $this->_user->getLoggedInUser() );

        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
    }

    /**
     * testing logout
     *
     */
    public function testLoggedInUser()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);

        $this->assertFalse( $provider->getLoggedInUser() );
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertFalse( $provider->getLoggedInUser() );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertSame( self::USER, $this->_user->getLoggedInUser() );
        $this->assertTrue( $provider->logout() );
        $this->assertFalse( $provider->getLoggedInUser() );

        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
    }

    /**
     * testing getSiteRoot
     *
     */
    public function testGetSiteRoot()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user);

        $params = array(
            'openid_realm'      => "http://wrong/",
            'openid_trust_root' => "http://root/",
            'openid_return_to'  => "http://wrong/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_realm'      => "http://wrong/",
            'openid_return_to'  => "http://root/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_realm'      => "http://wrong/",
        );
        $this->assertFalse( $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => Zend_OpenId::NS_2_0,
            'openid_realm'      => "http://root/",
            'openid_trust_root' => "http://wrong/",
            'openid_return_to'  => "http://wrong/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => Zend_OpenId::NS_2_0,
            'openid_trust_root' => "http://root/",
            'openid_return_to'  => "http://wrong/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => Zend_OpenId::NS_2_0,
            'openid_return_to'  => "http://root/",
        );
        $this->assertSame( "http://root/", $provider->getSiteRoot($params) );

        $params = array(
            'openid_ns'         => Zend_OpenId::NS_2_0,
        );
        $this->assertFalse( $provider->getSiteRoot($params) );

        $params = array(
            'openid_trust_root' => "",
        );
        $this->assertFalse( $provider->getSiteRoot($params) );
    }

    /**
     * testing allowSite
     *
     */
    public function testAllowSite()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);

        // not logged in
        $this->assertFalse( $provider->allowSite("http://www.test.com/") );
        // logged in
        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test.com/") );

        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( "http://www.test.com/", key($trusted) );
        $this->assertSame( true, current($trusted) );

        // duplicate
        $this->assertTrue( $provider->allowSite("http://www.test.com/") );

        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( "http://www.test.com/", key($trusted) );
        $this->assertSame( true, current($trusted) );

        // extensions
        $sreg = new Zend_OpenId_Extension_Sreg(array("nickname"=>"test_id"));
        $this->assertTrue( $provider->allowSite("http://www.test.com/", $sreg) );

        $trusted = $storage->getTrustedSites(self::USER);
        $this->assertTrue( is_array($trusted) );
        $this->assertSame( 1, count($trusted) );
        reset($trusted);
        $this->assertSame( "http://www.test.com/", key($trusted) );
        $this->assertSame( array('Zend_OpenId_Extension_Sreg'=>array('nickname'=>'test_id')), current($trusted) );

        $this->_user->delLoggedInUser();
        $storage->delUser(self::USER);
    }

    /**
     * testing denySite
     *
     */
    public function testDenySite()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);
        $sreg = new Zend_OpenId_Extension_Sreg(array("nickname"=>"test_id"));

        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test1.com/") );
        $this->assertTrue( $provider->allowSite("http://www.test2.com/", $sreg) );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->denySite("http://www.test3.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               ),
                               'http://www.test3.com/' => false
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->denySite("http://www.test1.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => false,
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               ),
                               'http://www.test3.com/' => false
                           ),
                           $storage->getTrustedSites(self::USER) );
        
        $this->assertTrue( $provider->denySite("http://www.test2.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => false,
                               'http://www.test2.com/' => false,
                               'http://www.test3.com/' => false
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->_user->delLoggedInUser();
        $storage->delUser(self::USER);
    }

    /**
     * testing delSite
     *
     */
    public function testDelSite()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);
        $sreg = new Zend_OpenId_Extension_Sreg(array("nickname"=>"test_id"));

        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test1.com/") );
        $this->assertTrue( $provider->allowSite("http://www.test2.com/", $sreg) );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->delSite("http://www.test3.com/") );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->assertTrue( $provider->delSite("http://www.test1.com/") );
        $this->AssertSame( array(
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $storage->getTrustedSites(self::USER) );
        
        $this->assertTrue( $provider->delSite("http://www.test2.com/") );
        $this->AssertSame( array(
                           ),
                           $storage->getTrustedSites(self::USER) );

        $this->_user->delLoggedInUser();
        $storage->delUser(self::USER);
    }

    /**
     * testing getTrustedSites
     *
     */
    public function testGetTrustedSites()
    {
        $storage = new Zend_OpenId_Provider_Storage_File();
        $storage->delUser(self::USER);
        $this->_user->delLoggedInUser();
        $provider = new Zend_OpenId_Provider(null, null, $this->_user, $storage);
        $sreg = new Zend_OpenId_Extension_Sreg(array("nickname"=>"test_id"));

        $this->assertTrue( $provider->register(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->login(self::USER, self::PASSWORD) );
        $this->assertTrue( $provider->allowSite("http://www.test1.com/") );
        $this->assertTrue( $provider->allowSite("http://www.test2.com/", $sreg) );
        $this->AssertSame( array(
                               'http://www.test1.com/' => true,
                               'http://www.test2.com/' => array(
                                   'Zend_OpenId_Extension_Sreg' => array(
                                       'nickname' => 'test_id'
                                   )
                               )
                           ),
                           $provider->getTrustedSites() );

        $this->_user->delLoggedInUser();
        $this->AssertFalse( $provider->getTrustedSites() );

        $storage->delUser(self::USER);
    }

    /**
     * testing handle
     *
     */
    public function testHandle()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }

    /**
     * testing respondToConsumer
     *
     */
    public function testRespondToConsumer()
    {
        // Remove the following line when you implement this test.
        $this->markTestIncomplete(
          "This test has not been implemented yet."
        );
    }
}
