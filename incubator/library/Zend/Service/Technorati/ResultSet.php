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
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** 
 * @see Zend_Service_Technorati_Result 
 */
require_once 'Zend/Service/Technorati/Result.php';


/**
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @todo       Standardize with Zend_Service_Yahoo and Zend_Service_Flickr
 */
class Zend_Service_Technorati_ResultSet implements SeekableIterator
{
    /**
     * The total number of results available
     *
     * @var int
     */
    public $totalResultsAvailable;

    /**
     * The number of results in this result set
     *
     * @var int
     */
    public $totalResultsReturned;

    /**
     * The offset in the total result set of this search set
     *
     * @var int
     */
    public $firstResultPosition;


    /**
     * A DomNodeList of results
     *
     * @var     DomNodeList
     * @access  protected
     */
    protected $_results;

    /**
     * Technorati API response document
     *
     * @var     DomDocument
     * @access  protected
     * @todo    XPath and DOM elements cannot be serialized, don't cache them
     */
    protected $_dom;

    /**
     * Object for $this->_dom
     *
     * @var     DOMXpath
     * @access  protected
     * @todo    XPath and DOM elements cannot be serialized, don't cache them
     */
    protected $_xpath;

    /**
     * Current Item
     *
     * @var     int
     * @access  protected
     */
    protected $_currentItem = 0;


    /**
     * Parse the search response and retrieve the results for iteration
     *
     * @param   DomDocument $dom    The ReST fragment for this object
     * @param   array $options      Query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        $this->_dom = $dom;
        $this->_xpath = new DOMXPath($dom);

        // Technorati loves to make developer's life really hard
        // I must read query options in order to normalize a single way
        // to display start and limit.
        // The value is printed out in XML using many different tag names,
        // too hard to get it from XML

        // @todo Use constants
        $start = isset($options['start']) ? $options['start'] : 1;
        // $limit = isset($options['limit']) ? $options['limit'] : 20;

        $this->firstResultPosition = $start;
        $this->totalResultsAvailable = 0;   // tag name depends on query type, relayed to child classes
        $this->totalResultsReturned = (int) $this->_xpath->evaluate("count(/tapi/document/item)");

        // @todo Improve xpath expression
        $this->_results = $this->_xpath->query("//item");
    }


    /**
     * Total Number of results returned
     *
     * @return  int     Total number of results returned
     */
    public function totalResults()
    {
        return (int) $this->totalResultsReturned;
    }


    /**
     * Implement SeekableIterator::current
     *
     * @return  void
     * @throws  Zend_Service_Exception
     */
    public function current()
    {
        throw new Zend_Service_Exception("Zend_Service_Technorati_ResultSet::current() should be overwritten in child classes");
    }


    /**
     * Implement SeekableIterator::key
     *
     * @return  int
     */
    public function key()
    {
        return $this->_currentItem;
    }


    /**
     * Implement SeekableIterator::next
     *
     * @return  void
     */
    public function next()
    {
        $this->_currentItem += 1;
    }


    /**
     * Implement SeekableIterator::rewind
     *
     * @return  bool
     */
    public function rewind()
    {
        $this->_currentItem = 0;
        return true;
    }


    /**
     * Implement SeekableIterator::seek
     *
     * @param   int     $item
     * @return  Zend_Service_Technorati_Result
     * @throws  Zend_Service_Exception
     */
    public function seek($item)
    {
        if ($this->valid($item)) {
            $this->_currentItem = $item;
            return $this->current();
        } else {
            // @todo Should be an OutOfBoundsException but that was only added in PHP 5.1
            throw new Zend_Service_Exception('Item not found');
        }
    }


    /**
     * Implement SeekableIterator::valid
     *
     * @param   int     $item
     * @return  bool
     */
    public function valid($item = null)
    {
        if (null === $item && $this->_currentItem < $this->_results->length) {
            return true;
        } elseif (null !== $item && $item <= $this->_results->length) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Return the response XML document
     *
     * @return string   The response document converted into XML format
     */
    function getXML()
    {
        return $this->_dom->saveXML($this->_dom);
    }
}
