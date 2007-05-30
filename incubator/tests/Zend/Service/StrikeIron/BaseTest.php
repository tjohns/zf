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
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Service_StrikeIron_BaseTest */
require_once 'Zend/Service/StrikeIron/BaseTest.php';

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_StrikeIron_BaseTest extends PHPUnit_Framework_TestCase 
{
    public function setUp()
    {
        $this->soapClient = new Zend_Service_StrikeIron_BaseTest_MockSoapClient;
        $this->base = new Zend_Service_StrikeIron_Base('user', 'pass', null, $this->soapClient);
    }

    public function testHasNoPredefinedWsdl()
    {
        $this->assertSame(null, $this->base->getWsdl());
    }
    
    public function testSettingWsdl()
    {
        $wsdl = 'http://example.com/foo';
        $base = new Zend_Service_StrikeIron_Base('user', 'pass', null, $this->soapClient, $wsdl);
        $this->assertEquals($wsdl, $base->getWsdl());
    }
    
    public function testSoapClientDependencyInjection()
    {
        $this->assertSame($this->soapClient, $this->base->getSoapClient());
    }
    

    public function testDefaultSoapHeadersHasTheLicenseInfoHeader()
    {
        $this->base->foo();
        $headers = $this->soapClient->calls[0]['headers'];
        
        $this->assertType('array', $headers);
        $this->assertEquals(1, count($headers));
        $header = $headers[0];
        
        $this->assertType('SoapHeader', $header);
        $this->assertEquals('LicenseInfo', $header->name);
        $this->assertEquals('user', $header->data['RegisteredUser']['UserID']);
        $this->assertEquals('pass', $header->data['RegisteredUser']['Password']);
    }
    
    public function testAddingInvalidSoapHeaderThrows()
    {
        $invalidHeaders = 'foo';
        try {
            $base = new Zend_Service_StrikeIron_Base('user', 'pass', $invalidHeaders, $this->soapClient);
            $this->fail();
        } catch (Zend_Service_StrikeIron_Exception $e) {
            $this->assertRegExp('/instance of soapheader/i', $e->getMessage());
        }
    }

    public function testAddingInvalidSoapHeaderArrayThrows()
    {
        $invalidHeaders = array('foo');
        try {
            $base = new Zend_Service_StrikeIron_Base('user', 'pass', $invalidHeaders, $this->soapClient);
            $this->fail();
        } catch (Zend_Service_StrikeIron_Exception $e) {
            $this->assertRegExp('/instance of soapheader/i', $e->getMessage());
        }
    }
    
    public function testAddingScalarSoapHeaderNotLicenseInfo()
    {
        $header = new SoapHeader('foo', 'bar');
        $base = new Zend_Service_StrikeIron_Base('user', 'pass', $header, $this->soapClient);
        $base->foo();
        
        $headers = $this->soapClient->calls[0]['headers'];
        $this->assertEquals(2, count($headers));
        $this->assertEquals($header->name, $headers[0]->name);
        $this->assertEquals('LicenseInfo', $headers[1]->name);
    }
    
    public function testAddingScalarSoapHeaderThatOverridesLicenseInfo()
    {
        $soapHeaders = new SoapHeader('http://ws.strikeiron.com', 
                                      'LicenseInfo', 
                                      array('RegisteredUser' => array('UserID'   => 'foo',
                                                                      'Password' => 'bar')));
        $base = new Zend_Service_StrikeIron_Base('user', 'pass', $soapHeaders, $this->soapClient);
        $base->foo();
        
        $headers = $this->soapClient->calls[0]['headers'];
        
        $this->assertType('array', $headers);
        $this->assertEquals(1, count($headers));
        $header = $headers[0];
        
        $this->assertType('SoapHeader', $header);
        $this->assertEquals('LicenseInfo', $header->name);
        $this->assertEquals('foo', $header->data['RegisteredUser']['UserID']);
        $this->assertEquals('bar', $header->data['RegisteredUser']['Password']);        
    }

    public function testAddingArrayOfSoapHeaders()
    {
        $headers = array(new SoapHeader('foo', 'bar'),
                         new SoapHeader('baz', 'qux'));

        $base = new Zend_Service_StrikeIron_Base('user', 'pass', $headers, $this->soapClient);
        $base->foo();

        $headers = $this->soapClient->calls[0]['headers'];
        
        $this->assertType('array', $headers);
        $this->assertEquals(3, count($headers));  // these 2 + default LicenseInfo  
    }
    
    public function testMethodInflection()
    {
        $this->base->foo();
        $this->assertEquals('Foo', $this->soapClient->calls[0]['method']);
    }

    public function testMethodResultNotWrappingNonObject()
    {
        $this->assertEquals(42, $this->base->returnThe42());
    }

    public function testMethodResultWrappingAnyObject()
    {
        $this->assertType('Zend_Service_StrikeIron_ResultDecorator', 
                          $this->base->returnTheObject());
    }
    
    public function testMethodResultWrappingAnObjectAndSelectingDefaultResultProperty()
    {
        $this->assertEquals('unwraped', $this->base->wrapThis());
    }
    
    public function testMethodExceptionsAreWrapped()
    {
        try {
            $this->base->throwTheException();
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Service_StrikeIron_Exception', $e);
            $this->assertEquals('Exception: foo', $e->getMessage());
            $this->assertEquals(43, $e->getCode());
        }
    }
    
    public function testGettingSubscriptionInfo()
    {
        $this->assertEquals(0, count($this->soapClient->calls));
        $info = $this->base->getSubscriptionInfo();
        $this->assertEquals(1, count($this->soapClient->calls));
        $this->assertEquals(3, $info->remainingHits);
    }

    public function testGettingSubscriptionInfoWithCaching()
    {
        $this->assertEquals(0, count($this->soapClient->calls));
        $this->base->foo();
        $this->base->getSubscriptionInfo();
        $this->assertEquals(1, count($this->soapClient->calls));
    }

    public function testGettingSubscriptionOverridingCache()
    {
        $this->assertEquals(0, count($this->soapClient->calls));
        $this->base->getSubscriptionInfo();
        $this->assertEquals(1, count($this->soapClient->calls));
        $this->base->getSubscriptionInfo(true);
        $this->assertEquals(2, count($this->soapClient->calls));
    }
}

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_StrikeIron_BaseTest_MockSoapClient
{
    public $calls = array();
    
    public function __soapCall($method, $params, $options, $headers, &$outputHeaders)
    {
        $outputHeaders = array('SubscriptionInfo' => array('RemainingHits' => 3));

        $this->calls[] = array('method'  => $method, 
                               'params'  => $params, 
                               'options' => $options, 
                               'headers' => $headers);
        
        if ($method == 'ReturnTheObject') {
            return new stdclass();
        } else if ($method == 'WrapThis') {
            return (object)array('WrapThisResult' => 'unwraped');
        } else if ($method == 'ThrowTheException') {
            throw new Exception('foo', 43);
        } else {
            return 42;
        }
    }
}