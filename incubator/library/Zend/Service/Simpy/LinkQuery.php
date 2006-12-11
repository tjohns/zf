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
 * @subpackage Simpy
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Simpy
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Simpy_LinkQuery
{
    /**
     * Query string for the query
     * 
     * @var string
     */
    protected $_query;
    
    /**
     * Maximum number of search results to return
     * 
     * @var int
     */
    protected $_limit;
    
    /**
     * Date on which search results must have been added
     * 
     * @var string
     */
    protected $_date;
    
    /**
     * Date after which search results must have been added
     * 
     * @var string
     */
    protected $_afterDate;
    
    /**
     * Date before which search results must have been added
     * 
     * @var string
     */
    protected $_beforeDate;
    
    /**
     * Constructor to initialize class members
     */
    public function __construct()
    {
        $this->_clauses = array();
        $this->_limit = null;
        $this->_date = null;
        $this->_afterDate = null;
        $this->_beforeDate = null;
    }
    
    /**
     * Sets the query string for the query
     * 
     * @param string $query Query string in valid Simpy syntax
     * @see http://www.simpy.com/faq#searchSyntax
     * @see http://www.simpy.com/faq#searchFieldsLinks
     */
    public function setQueryString($query)
    {
        $this->_query = $query;
    }
    
    /**
     * Returns the query string set for this query
     * 
     * @return string
     */
    public function getQueryString()
    {
        return $this->_query;
    }
    
    /**
     * Sets the maximum number of search results to return
     * 
     * @param int $limit
     */
    public function setLimit($limit)
    {
        $this->_limit = intval($limit);
        
        if ($this->_limit == 0) {
            $this->_limit = null;
        }
    }
    
    /**
     * Returns the maximum number of search results to return
     * 
     * @return int
     */
    public function getLimit()
    {
        return $this->_limit;
    }
    
    /**
     * Sets the date on which search results must have been added, which will 
     * override any existing values set using setAfterDate() and setBeforeDate()
     * 
     * @param string $date
     * @see setAfterDate()
     * @see setBeforeDate()
     */
    public function setDate($date)
    {
        $this->_date = $date;
        $this->_afterDate = null;
        $this->_beforeDate = null;
    }
   
    /**
     * Returns the date on which search results must have been added
     * 
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }
    
    /**
     * Sets the date after which search results must have been added, which will 
     * override any existing values set using setDate()
     * 
     * @param string $date
     * @see setDate()
     */
    public function setAfterDate($date)
    {
        $this->_afterDate = $date;
        $this->_date = null;
    }
    
    /**
     * Returns the date after which search results must have been added
     * 
     * @return string
     */
    public function getAfterDate()
    {
        return $this->_afterDate;
    }
    
    /**
     * Sets the date before which search results must have been added, which
     * will override any existing values set using setDate()
     * 
     * @param string $date
     * @see setDate()
     */
    public function setBeforeDate($date)
    {
        $this->_beforeDate = $date;
        $this->_date = null;
    }
    
    /**
     * Returns the date before which search results must have been added
     * 
     * @return string
     */
    public function getBeforeDate()
    {
        return $this->_beforeDate;
    }
}