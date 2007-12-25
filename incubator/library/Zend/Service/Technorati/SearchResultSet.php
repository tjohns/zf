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
 * @version    $Id: CosmosResultSet.php 7243 2007-12-23 20:55:55Z weppos $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** 
 * @see Zend_Service_Technorati_ResultSet 
 */
require_once 'Zend/Service/Technorati/ResultSet.php';


/**
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_SearchResultSet extends Zend_Service_Technorati_ResultSet
{
    /**
     * Number of query results.
     *
     * @var     int
     * @access  protected
     */
    protected $_queryCount;
    
    /**
     * Parses the search response and retrieve the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        // @todo    Improve xpath expressions
        
        $result = $this->_xpath->query('//result/querycount/text()');
        if ($result->length == 1) $this->_queryCount = (int) $result->item(0)->data;
        
        $this->totalResultsReturned  = (int) $this->_xpath->evaluate("count(/tapi/document/item)");
        $this->totalResultsAvailable = (int) $this->_queryCount;
    }

    /**
     * Implements SeekableIterator::current and
     * overwrites Zend_Service_Technorati_ResultSet::current()
     *
     * @return Zend_Service_Technorati_SearchResult current result
     */
    public function current()
    {
        /**
         * @see Zend_Service_Technorati_SearchResult
         */
        require_once 'Zend/Service/Technorati/SearchResult.php';
        return new Zend_Service_Technorati_SearchResult($this->_results->item($this->_currentItem));
    }
}
