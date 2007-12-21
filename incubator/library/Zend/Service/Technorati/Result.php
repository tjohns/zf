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
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_Result
{
    /**
     * An associative array of 'fieldName' => 'xmlfieldtag'
     *
     * @var     array
     * @access  protected
     */
    protected $_fields;

    /**
     * The ReST fragment for this result object
     *
     * @var     DomElement
     * @access  protected
     */
    protected $_result;

    /**
     * Object for $this->_dom
     *
     * @var     DOMXpath
     * @access  protected
     * @todo    XPath and DOM elements cannot be serialized, don't cache them
     */
    protected $_xpath;


    /**
     * Constructs a new object from DOM Element.
     * Properties are automatically fetched from XML
     * according to array of $_fields to be read.
     *
     * @param   DomElement $result  the ReST fragment for this object
     */
    public function __construct(DomElement $result)
    {
        $this->_xpath = new DOMXPath($result->ownerDocument);

        // default fields for all search results
        // actually empty
        $fields = array();

        // merge with child's object fields
        $this->_fields = array_merge($this->_fields, $fields);

        // add results to appropriate fields
        foreach($this->_fields as $phpName => $xmlName) {
            $query = "./$xmlName/text()";
            $node = $this->_xpath->query($query, $result);
            if ($node->length == 1) {
                // @todo convert to string?
                $this->{$phpName} = $node->item(0)->data;
            }
        }

        $this->_result = $result;
    }
}
