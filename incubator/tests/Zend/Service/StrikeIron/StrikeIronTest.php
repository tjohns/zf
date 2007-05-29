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

/** Zend_Service_StrikeIron */
require_once 'Zend/Service/StrikeIron.php';

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_StrikeIron_StrikeIronTest extends PHPUnit_Framework_TestCase 
{    
    public function setUp()
    {
        // stub out SOAPClient instance
        $this->soapClient = new stdclass();

        $this->strikeIron = new Zend_Service_StrikeIron('user', 'pass', null, $this->soapClient);
    }
    
    public function testFactoryThrowsOnDisallowedNames()
    {
        $disallowed = array('Exception', 'ResultDecorator');
        foreach ($disallowed as $class) {
            try {
                $this->strikeIron->getService($class);
                $this->fail();
            } catch (Zend_Service_StrikeIron_Exception $e) {
                $this->assertRegExp('/not a valid strikeiron/i', $e->getMessage());
            }
        }
    }
    
    public function testFactoryThrowsOnBadName()
    {
        try {
            $this->strikeIron->getService('BadServiceNameHere');
            $this->fail();
        } catch (Zend_Service_StrikeIron_Exception $e) {
            $this->assertRegExp('/could not be loaded/i', $e->getMessage());
            $this->assertRegExp('/not found/i', $e->getMessage());
        }
    }
    
    public function testFactoryReturnsService()
    {
        $base = $this->strikeIron->getService('Base');
        $this->assertType('Zend_Service_StrikeIron_Base', $base);
        $this->assertSame(null, $base->getWsdl());
        $this->assertSame($this->soapClient, $base->getSoapClient());
    }

    public function testFactoryReturnsServiceWithCustomWsdl()
    {
        $wsdl = 'http://strikeiron.com/foo';
        $base = $this->strikeIron->getService('Base', $wsdl);
        $this->assertEquals($wsdl, $base->getWsdl());
    }
}
