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
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/** 
 * @see Zend_Service_Technorati_Result 
 */
require_once 'Zend/Service/Technorati/Result.php';


/**
 * This is the most essential result set. 
 * The scope of this class is to be extended by a query-specific child result set class, 
 * and it should never be used to initialize a standalone object. 
 * 
 * Each of the specific result sets represents a collection of query-specific
 * Zend_Service_Technorati_Result objects.
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @abstract 
 */
abstract class Zend_Service_Technorati_ResultSet implements SeekableIterator
{
    /**
     * The total number of results available
     *
     * @var     int
     * @access  protected
     */
    protected $_totalResultsAvailable;

    /**
     * The number of results in this result set
     *
     * @var     int
     * @access  protected
     */
    protected $_totalResultsReturned;

    /**
     * The offset in the total result set of this search set
     *
     * @var     int
     * @todo
     */
    // public $firstResultPosition;


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
    protected $_currentIndex = 0;


    /**
     * Parses the search response and retrieves the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
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
        
        // Additionally, the following tags should be always available
        // according to API documentation but... this is not the truth!
        // - querytime
        // - limit
        // - start (sometimes rankingstart)
        
        // query tag is only available for some requests, the same for url. 
        // For now ignore them.

        //$start = isset($options['start']) ? $options['start'] : 1;
        //$limit = isset($options['limit']) ? $options['limit'] : 20;
        //$this->_firstResultPosition = $start;

        $this->_results = $this->_xpath->query("//item");
    }


    /**
     * Number of results returned.
     *
     * @return  int     total number of results returned
     */
    public function totalResults()
    {
        return (int) $this->_totalResultsReturned;
    }


    /**
     * Number of available results.
     *
     * @return  int     total number of available results
     */
    public function totalResultsAvailable()
    {
        return (int) $this->_totalResultsAvailable;
    }

    /**
     * Implements SeekableIterator::current().
     *
     * @return  void
     * @throws  Zend_Service_Exception
     * @abstract 
     */
    // abstract public function current();

    /**
     * Implements SeekableIterator::key().
     *
     * @return  int
     */
    public function key()
    {
        return $this->_currentIndex;
    }

    /**
     * Implements SeekableIterator::next().
     *
     * @return  void
     */
    public function next()
    {
        $this->_currentIndex += 1;
    }

    /**
     * Implements SeekableIterator::rewind().
     *
     * @return  bool
     */
    public function rewind()
    {
        $this->_currentIndex = 0;
        return true;
    }
    
    /**
     * Implement SeekableIterator::seek().
     *
     * @param   int $index
     * @return  void
     * @throws  OutOfBoundsException
     */
    public function seek($index)
    {
        $indexInt = (int) $index;
        if ($indexInt >= 0 && $indexInt < $this->_results->length) {
            $this->_currentIndex = $indexInt;
        } else {
            throw new OutOfBoundsException("Illegal index '$index'");
        }
    }

    /**
     * Implement SeekableIterator::valid().
     *
     * @return boolean
     */
    public function valid()
    {
        return null !== $this->_results && $this->_currentIndex < $this->_results->length;
    }

    /**
     * Returns the response XML document.
     *
     * @return string   the response document converted into XML format
     */
    function getXML()
    {
        return $this->_dom->saveXML($this->_dom);
    }
}
