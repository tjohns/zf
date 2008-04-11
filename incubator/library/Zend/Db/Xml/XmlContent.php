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
 * The Zend_Db_Xml_XmlContent class is a class representing an XML Document.
 *
 * @category   Zend
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Xml_XmlContent
{
    /**
     * @var integer
     */
    public $id;

    /**
     *
     */
    public $attachment;

    /**
     * @var DOMDocument
     */
    public $data;

    /**
     * @var DOMDocument
     */
    public $about;


    /**
     * Zend_Db_Xml_XmlContent constructor
     *
     * @param DOMDocument $element The DOMDocument we're encapsulating.
     */
    public function __construct($element = null)
    {
        $numArgs = func_num_args();

        if ($numArgs == 0 || $numArgs == 1) {
            $this->data = $element;
        } else if ($numArgs == 4) {
            $args = func_get_args();
            $this->id = $args[0];
            $this->data = $args[1];
            $this->about = $args[2];
            $this->attachment = $args[3];
        } else {
            throw new Zend_Db_Xml_XmlException('Wrong number of arguments passed in to constructor');
        }
    }

    /**
     * Retrieve the attachment associated with this XML content object
     */
    public function getAttachment()
    {
        return $this->attachment;
    }

    /**
     * set the attachment associated with
     * this XML content object
     *
     * @param var $attach
     */
    public function setAttachment($attach)
    {
        $this->attachment = $attach;
    }

    /**
     * returns true if an attachment has been set for this XML content
     * object, false otherwise
     *
     * @return boolean
     */
    public function hasAttachment()
    {
        return !is_null($this->attachment);
    }

    /**
     * returns the $about XML document
     *
     * @return DOMDocument
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * replaces the existing $about document with the
     * DOMDocument parameter
     *
     * @param DOMDocument $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }

    /**
     * return $data
     *
     * @return DOMDocument
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * set the $data document for
     * this object
     *
     * @param DOMDocument
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * set the XML content document id with $id
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
