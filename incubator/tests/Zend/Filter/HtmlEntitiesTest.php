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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_HtmlEntitiesTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_HtmlEntities object
     *
     * @var Zend_Filter_HtmlEntities
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_HtmlEntities object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_HtmlEntities();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            'string' => 'string',
            '<'      => '&lt;',
            '>'      => '&gt;',
            '\''     => '\'',
            '"'      => '&quot;',
            '&'      => '&amp;'
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
        }
    }
}
