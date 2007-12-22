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
 */
class Zend_Service_Technorati_TagsResult extends Zend_Service_Technorati_Result
{
    /**
     * Collection of tags
     * 
     * @var     array
     * @access  protected
     * @todo    convert to array of Tag objects
     */
    protected $_tags = array();

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
     * Constructs a new object object from DOM Document.
     *
     * @param   DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomDocument $dom)
    {
        $this->_dom = $dom;
        $this->_xpath = new DOMXPath($dom);

        $this->_parseTags();
    }

    /**
     * Returns the tags collection.
     * 
     * @return  array
     */
    public function getTags() {
        return $this->_tags;
    }

    /**
     * Parses all tags from current response.
     * 
     * Tags are stored into the protected $_tags variable.
     * Invalid or null tags are skipped.
     * 
     * @return void
     */
    protected function _parseTags() 
    {
        $result = $this->_xpath->query('//item');
        foreach ($result as $item) {
            $tag = $this->_parseTagFragment($item);
            if ($tag !== null) {
                $this->_tags[] = $tag;
            }
        }
    }

    /**
     * Parses and returns a single tag.
     * 
     * Returns an associative array:
     * - 'tag'      => the name of the tag
     * - 'posts'    => number of posts matching tag
     * 
     * @param   DomElement $dom
     * @return  array
     * @todo    convert to Zend_Service_Technorati_Tag
     */
    protected function _parseTagFragment(DomElement $dom) 
    {
        /**
         * @todo performance test using childNodes() iteration
         * @todo performance test using getElementsByTagName()
         */
        $result = $this->_xpath->query('./tag/text()', $dom);
        $tag    = $result->length == 1 ? (string) $result->item(0)->data : null;
        $result = $this->_xpath->query('./posts/text()', $dom);
        $count  = $result->length == 1 ? (int) $result->item(0)->data : null;
        
        return ($tag !== null && $count !== null) ?
                array('tag' => $tag, 'posts' => $count) : null;
    }
}
