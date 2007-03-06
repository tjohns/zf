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
 */

/** PHPUnit_Framework_TestCase */
require_once 'PHPUnit/Framework/TestCase.php';

/** Zend_Log */
require_once 'Zend/Log.php';

/** Zend_Log_Filter_Level */
require_once 'Zend/Log/Filter/Level.php';

/**
 * @category   Zend
 * @package    Zend_Log
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Log_Filter_LevelTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        // accept at or below level 2
        $this->filter = new Zend_Log_Filter_Level(2);
    }

    public function testLevelFilterAccept()
    {
        $this->assertTrue($this->filter->accept('', 2));
        $this->assertTrue($this->filter->accept('', 1));
    }

    public function testLevelFilterReject()
    {
        $this->assertFalse($this->filter->accept('', 3));
    } 

    public function testConstructorThrowsOnInvalidLevel()
    {
        try {
            new Zend_Log_Filter_Level('foo');
            $this->fail();
        } catch (Exception $e) {
            $this->assertType('Zend_Log_Exception', $e);
            $this->assertRegExp('/must be an integer/i', $e->getMessage());
        }
    }

}
