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
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Db_Xml_XmlContent
 */
require_once('Zend/Db/Xml/XmlContent.php');

/**
 * The Zend_Db_Xml_XmlContentStore is an abstract class which represents a repository
 * for XML Documents.
 *
 * Zend_Db_Xml_XmlContentStore abstracts database persistence via convenience methods.
 * XML Data is represented by Zend_Db_Xml_XmlContent objects.  Activities to and from
 * the persistence layer are operated on these objects.
 *
 * @category   Zend
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

abstract class Zend_Db_Xml_XmlContentStore
{
    /**
     * Represents a connection handle to the persistence layer
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_conn;

    /**
     * Zend_Db_Xml_XmlContentStore constructor
     * 
     * @param Zend_Db_Adapter_Abstract
     */
    public abstract function __construct($conn);

    /**
     * Saves the XML data represented by an Zend_Db_Xml_XmlContent object
     * into the persistence layer.  An array of Zend_Db_Xml_XmlContent
     * objects can also be passed in.
     *
     * @param mixed $doc
     *
     * @return integer
     */
    public abstract function insert($doc);

    /**
     * Replaces existing XML data represented by an Zend_Db_Xml_XmlContent object
     * in the persistence layer. An array of Zend_Db_Xml_XmlContent
     * objects can also be passed in.
     *
     * @param mixed $doc
     *
     * @return integer $numUpdated, the number of documents updated
     */
    public abstract function update($doc);


    /**
     * Removes the XML data represented by an Zend_Db_Xml_XmlContent object
     * from the persistence layer. An array of Zend_Db_Xml_XmlContent
     * objects can also be passed in.
     *
     * @param mixed $doc
     *
     * @return integer the number of rows deleted
     */
    public abstract function delete($doc);

    /**
     * Removes the XML data given the id of the XML document
     *
     * @param integer $id
     *
     * @return integer the number of rows deleted
     */
    public abstract function deleteById($id);

    /**
     * Returns all documents in the content store
     *
     * @return Zend_Db_Xml_XmlIterator
     */
    public abstract function selectAll();

    /**
     * Finds Zend_Db_Xml_XmlContent documents that match items in array
     *
     * @param array $searchParam is an associative array representing element name
     * and element text pairs.  This is used to perform a simple xpath
     * search in the persistence layer for docs that match the xpath
     * expression
     * 
     * @param $where, search in DATA or ABOUT
     * @param array $options, type of search
     * 
     * @return Zend_Db_Xml_XmlIterator
     */
    public abstract function find($searchParam, $where, $options);

    /**
     * Returns the Zend_Db_Xml_XmlContent object associated with given id
     *
     * @param integer $id
     *
     * @return Zend_Db_Xml_XmlIterator
     */
    public abstract function findById($id);
	
    /**
     * Convenience method to insert a DOM
     * directly into the persistence layer
     * 
     * @param DOMDocument $doc
     * 
     * @return Zend_Db_Xml_XmlContent
     */
    public function insertDOM(DOMDocument $doc)
    {
        $xmlDoc = new Zend_Db_Xml_XmlContent($doc);
        $this->insert($xmlDoc);
        return $xmlDoc;
    }
}
