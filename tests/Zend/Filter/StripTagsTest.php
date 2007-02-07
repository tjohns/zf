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
 * @see Zend_Filter_StripTags
 */
require_once 'Zend/Filter/StripTags.php';


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
class Zend_Filter_StripTagsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_StripTags object
     *
     * @var Zend_Filter_StripTags
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_StripTags object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_StripTags();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertEquals('foo', $this->_filter->filter('<a href="example.com">foo</a>'));
    }

    /**
     * Ensures that getTagsAllowed() returns expected default value
     *
     * @return void
     */
    public function testGetTagsAllowed()
    {
        $this->assertEquals(array(), $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that setTagsAllowed() follows expected behavior when provided a single tag
     *
     * @return void
     */
    public function testSetTagsAllowedString()
    {
        $this->_filter->setTagsAllowed('b');
        $this->assertEquals(array('b' => array()), $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that setTagsAllowed() follows expected behavior when provided an array of tags
     *
     * @return void
     */
    public function testSetTagsAllowedArray()
    {
        $tagsAllowed = array(
            'b',
            'a'   => 'href',
            'div' => array('id', 'class')
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $tagsAllowedExpected = array(
            'b'   => array(),
            'a'   => array('href' => null),
            'div' => array('id' => null, 'class' => null)
            );
        $this->assertEquals($tagsAllowedExpected, $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that getAttributesAllowed() returns expected default value
     *
     * @return void
     */
    public function testGetAttributesAllowed()
    {
        $this->assertEquals(array(), $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that setAttributesAllowed() follows expected behavior when provided a single tag
     *
     * @return void
     */
    public function testSetAttributesAllowedString()
    {
        $this->_filter->setAttributesAllowed('class');
        $this->assertEquals(array('class' => null), $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that an unclosed tag is stripped in its entirety
     *
     * @return void
     */
    public function testStripUnclosedTag()
    {
        $input    = '<a href="http://example.com" Some Text';
        $expected = '';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that unallowed tags and attributes are stripped and that tags are backward-compatible XHTML
     *
     * @return void
     */
    public function testBasicBehaviors()
    {
        $input    = '<a href="http://example.com" style="color: #ffffff"><b>Some Text</b></a><br/>';
        $expected = '<a href="http://example.com">Some Text</a><br />';
        $tagsAllowed = array(
            'a' => 'href',
            'br'
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }
}
