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
     * Technorati weblog url, if queried URL is a valid weblog.
     *
     * @var     Zend_Uri_Http
     * @access  protected
     */
    protected $_url;

    /**
     * Technorati weblog, if queried URL is a valid weblog.
     *
     * @var     Zend_Service_Technorati_Weblog
     * @access  protected
     */
    protected $_weblog;
    
    /**
     * Number of unique blogs linking this blog
     *
     * @var     integer
     * @access  protected
     */
    protected $_inboundBlogs;

    /**
     * Number of incoming links to this blog
     *
     * @var     integer
     * @access  protected
     */
    protected $_inboundLinks;
    
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
        $this->totalResultsAvailable = $this->_queryCount !== null ? $this->_queryCount : 0;
        
        /**
         * @todo    Dear Technorati,
         *          why don't you decide to clean your api with a few standard tags.
         *          Don't use <rankingstart> here and <start> somewhere else.
         *          I have to decide a few standard $vars to describe keys, queries and options.
         */
    }
    
    
    /**
     * Returns the weblog URL.
     * 
     * @return  Zend_Uri_Http
     */
    public function getUrl() {
        return $this->_url;
    }
    
    /**
     * Returns the weblog.
     * 
     * @return  Zend_Service_Technorati_Weblog
     */
    public function getWeblog() {
        return $this->_weblog;
    }
    
    /**
     * Returns number of unique blogs linking this blog.
     * 
     * @return  integer the number of inbound blogs
     */
    public function getInboundBlogs() 
    {
        return $this->_inboundBlogs;
    }
    
    /**
     * Returns number of incoming links to this blog.
     * 
     * @return  integer the number of inbound links
     */
    public function getInboundLinks() 
    {
        return $this->_inboundLinks;
    }

    /**
     * Implements SeekableIterator::current and
     * overwrites Zend_Service_Technorati_ResultSet::current()
     *
     * @return Zend_Service_Technorati_CosmosResult current result
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
