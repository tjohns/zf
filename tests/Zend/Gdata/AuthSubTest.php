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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/AuthSub.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_AuthSubTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function testNormalGetAuthSubTokenUri()
    {
        $uri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
                'http://www.example.com/foo.php', //next
                'http://www.google.com/calendar/feeds', //scope
                0, //secure 
                1); //session
   
        // Note: the scope here is not encoded.  It should be encoded, 
        // but the method getAuthSubTokenUri calls urldecode($scope).  
        // This currently works (no reported bugs) as web browsers will
        // handle the encoding in most cases.
       $this->assertEquals('https://www.google.com/accounts/AuthSubRequest?next=http%3A%2F%2Fwww.example.com%2Ffoo.php&scope=http://www.google.com/calendar/feeds&secure=0&session=1', $uri);
    }

    public function testGetAuthSubTokenUriModifiedBase()
    {
        $uri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
                'http://www.example.com/foo.php', //next
                'http://www.google.com/calendar/feeds', //scope
                0, //secure 
                1, //session
                'http://www.otherauthservice.com/accounts/AuthSubRequest');
   
        // Note: the scope here is not encoded.  It should be encoded, 
        // but the method getAuthSubTokenUri calls urldecode($scope).  
        // This currently works (no reported bugs) as web browsers will
        // handle the encoding in most cases.
       $this->assertEquals('http://www.otherauthservice.com/accounts/AuthSubRequest?next=http%3A%2F%2Fwww.example.com%2Ffoo.php&scope=http://www.google.com/calendar/feeds&secure=0&session=1', $uri);
    }

}
