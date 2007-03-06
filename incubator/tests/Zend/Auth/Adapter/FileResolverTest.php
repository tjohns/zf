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
 * @copyright  Copyright (c) 2007 Bryce Lohr
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Auth/Adapter/Http/Resolver/File.php';
require_once 'Zend/Auth/Adapter/Http/Resolver/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2007 Bryce Lohr
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_FileResolverTest extends PHPUnit_Framework_TestCase
{
    protected $_validPath;
    protected $_badPath;


    public function setUp()
    {
        chdir(dirname(__FILE__));

        $this->_validPath = './_files/htdigest.3';
        $this->_badPath = 'doesnotexist';
    }

    public function testAccessors()
    {
        $v = new Zend_Auth_Adapter_Http_Resolver_File;

        try {
            $v->setFile($this->_validPath);
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->fail('Threw exception on valid file path');
        }
        $this->assertEquals($this->_validPath, $v->getFile());
    }

    public function testBadPaths()
    {
        $v = new Zend_Auth_Adapter_Http_Resolver_File;

        try {
            $v->setFile($this->_badPath);
            $this->fail('Accepted bad path');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw an exception
        }
    }
    
    public function testConstructor()
    {
        try {
            $v = new Zend_Auth_Adapter_Http_Resolver_File($this->_validPath);
            $this->assertEquals($this->_validPath, $v->getFile());
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->fail('Constructor threw exception on valid file path');
        }
    }
    
    public function testConstructorBadPath()
    {
        try {
            $v = new Zend_Auth_Adapter_Http_Resolver_File($this->_badPath);
            $this->fail('Constructor accepted bad path');
        } catch(Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw exception
        }
    }
    
    public function testResolveValidation()
    {
        $b = new Zend_Auth_Adapter_Http_Resolver_File;
        $b->setFile($this->_validPath);
        
        try {
            $b->resolve('', '');
            $this->fail('Accepted empty credentials');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw exception
        }
        try {
            $b->resolve('bad:name', 'realm');
            $this->fail('Accepted malformed username');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw exception
        }
        try {
            $b->resolve('badname'."\n", 'realm');
            $this->fail('Accepted malformed username');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw exception
        }
        try {
            $b->resolve('username', 'bad:realm');
            $this->fail('Accepted malformed realm');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw exception
        }
        try {
            $b->resolve('username', 'badrealm'."\n");
            $this->fail('Accepted malformed realm');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            // Good, threw exception
        }
    }

    public function testResolver()
    {
        $d = new Zend_Auth_Adapter_Http_Resolver_File;
        $d->setFile($this->_validPath);

        $pw = $d->resolve('Bryce', 'Test Realm');
        $this->assertEquals(
            $pw, 'd5b7c330d5685beb782a9e22f0f20579',
            'Rejected valid credentials'
        );
        $pw = $d->resolve('Mufasa', 'No Such Realm');
        $this->assertFalse(
            $pw,
            'Accepted a valid user in the wrong realm'
        );
        $pw = $d->resolve('InvalidUser', 'DoesNotMatter');
        $this->assertFalse(
            $pw,
            'Accepted non-existant user/realm'
        );
    }
}
