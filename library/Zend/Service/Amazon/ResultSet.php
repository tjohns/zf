<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_Service_Amazon_ResultSet implements SeekableIterator
{
    /**
     * @var DomNodeList $_result A DomNodeList of <Item> elements
     */
    protected $_results;

    /**
     * @var DomDocument Amazon Web Service Return Document
     */
    protected $_dom;

    /**
     * @var DOMXpath Xpath Object for $this->_dom
     */
    protected $_xpath;

    /**
     * @var int Current Item
     */
    protected $_currentItem = 0;

    /**
     * Create an instance of Zend_Service_Amazon_ResultSet and create the necessary data objects
     *
     * @param DOMDocument $dom
     */
    public function __construct(DOMDocument $dom)
    {
    	$this->_dom = $dom;
    	$this->_xpath = new DOMXPath($dom);
    	$this->_xpath->registerNamespace('az', 'http://webservices.amazon.com/AWSECommerceService/2005-10-05');
    	$this->_results = $this->_xpath->query('//az:Item');
    }

    /**
     * Total Number of results returned
     *
     * @return int Total number of results returned
     */
    public function totalResults()
    {
		$result = $this->_xpath->query('//az:TotalResults/text()');
		return (int) $result->item(0)->data;
    }

    /**
     * Implement SeekableIterator::current
     *
     * @return Zend_Service_Amazon_Item
     */
    public function current()
    {
    	return new Zend_Service_Amazon_Item($this->_results->item($this->_currentItem));
    }

    /**
     * Implement SeekableIterator::key
     *
     * @return int
     */
    public function key()
    {
    	return $this->_currentItem;
    }

    /**
     * Implement SeekableIterator::next
     */
    public function next()
    {
    	$this->_currentItem += 1;
    }

    /**
     * Implement SeekableIterator::rewind
     *
     * @return boolean
     */
    public function rewind()
    {
    	$this->_currentItem = 0;
    	return true;
    }

    /**
     * Implement SeekableIterator::sek
     *
     * @param int $item
     * @return Zend_Service_Amazon_Item
     * @throws Zend_Service_Exception
     */
    public function seek($item)
    {
    	if ($this->valid($item)) {
    		$this->_currentItem = $item;
    		return $this->current();
    	} else {
    		/* @todo Should be an OutOfBoundsException but that was only added in PHP 5.1 */
    		throw new Zend_Service_Exception('Item not found');
    	}
    }

    /**
     * Implement SeekableIterator::valid
     *
     * @param int $item
     * @return boolean
     */
    public function valid($item = null)
    {
    	if ($item === null && $this->_currentItem < $this->_results->length) {
    		return true;
    	} else if ($item !== null && $item <= $this->_results->length) {
    		return true;
    	} else {
    		return false;
    	}
    }
}

