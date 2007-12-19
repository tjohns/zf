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
class Zend_Service_Technorati_CosmosResultSet extends Zend_Service_Technorati_ResultSet
{
    /**
     * Parses the search response and retrieve the results for iteration.
     *
     * @param   DomDocument $dom    the ReST fragment for this object
     * @param   array $options      query options as associative array
     */
    public function __construct(DomDocument $dom, $options = array())
    {
        parent::__construct($dom, $options);

        /**
         * @todo    Parse result tags:
         *          url ->
         * x        inboundlinks ($totalResultsAvailable)
         *          rankingstart
         *          inboundblogs    -> url == a weblog
         *          weblog          -> url == a weblog
         */
        // @todo    Improve xpath expression
        $this->totalResultsAvailable = (int) $this->_xpath->query("//result/inboundlinks/text()")->item(0)->data;

        /**
         * @todo    Dear Technorati,
         *          why don't you decide to clean your api with a few standard tags.
         *          Don't use <rankingstart> here and <start> somewhere else.
         *          I have to decide a few standard $vars to describe keys, queries and options.
         */
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
         * @see Zend_Service_Technorati_CosmosResult
         */
        require_once 'Zend/Service/Technorati/CosmosResult.php';
        return new Zend_Service_Technorati_CosmosResult($this->_results->item($this->_currentItem));
    }
}
