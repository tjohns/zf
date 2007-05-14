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

require_once 'Zend/Gdata/Spreadsheets.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Spreadsheets_CellQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->docQuery = new Zend_Gdata_Spreadsheets_CellQuery();
    }

    public function testMinRow()
    {
        $this->docQuery->setMinRow('1');
        $this->assertTrue($this->docQuery->getMinRow() == '1');
        $this->assertTrue($this->docQuery->getQueryString() == '?min-row=1');
    }
    
    public function testMaxRow()
    {
        $this->docQuery->setMaxRow('2');
        $this->assertTrue($this->docQuery->getMaxRow() == '2');
        $this->assertTrue($this->docQuery->getQueryString() == '?max-row=2');
    }
    
    public function testMinCol()
    {
        $this->docQuery->setMinCol('3');
        $this->assertTrue($this->docQuery->getMinCol() == '3');
        $this->assertTrue($this->docQuery->getQueryString() == '?min-col=3');
    }
    
    public function testMaxCol()
    {
        $this->docQuery->setMaxCol('4');
        $this->assertTrue($this->docQuery->getMaxCol() == '4');
        $this->assertTrue($this->docQuery->getQueryString() == '?max-col=4');
    }
    
    public function testRange()
    {
        $this->docQuery->setRange('A1:B4');
        $this->assertTrue($this->docQuery->getRange() == 'A1:B4');
        $this->assertTrue($this->docQuery->getQueryString() == '?range=A1%3AB4');
    }
    
    public function testReturnEmpty()
    {
        $this->docQuery->setReturnEmpty('false');
        $this->assertTrue($this->docQuery->getReturnEmpty() == 'false');
        $this->assertTrue($this->docQuery->getQueryString() == '?return-empty=false');
    }

}
