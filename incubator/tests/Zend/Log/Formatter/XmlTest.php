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
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log_Formatter_Xml */
require_once 'Zend/Log/Formatter/Xml.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Log_Formatter_XmlTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultFormat()
    {
        $f = new Zend_Log_Formatter_Xml();
        $line = $f->format($message = 'message', $priority = 1);

        $this->assertContains($message, $line);
        $this->assertContains((string)$priority, $line);
    }
    
    public function testXmlDeclarationIsStripped()
    {
        $f = new Zend_Log_Formatter_Xml();
        $line = $f->format($message = 'message', $priority = 1);
        
        $this->assertNotContains('<\?xml version=', $line);
    }
    
    public function testXmlValidates()
    {
        $f = new Zend_Log_Formatter_Xml();
        $line = $f->format($message = 'message', $priority = 1);

        $sxml = @simplexml_load_string($line);
        $this->assertType('SimpleXMLElement', $sxml, 'Formatted XML is invalid');
    }

}
