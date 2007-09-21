<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * The Zend_Db_Xml_XmlContent class is a class representing an XML Document.
 *
 * @category   Zend
 * @package    Zend_Db_Xml
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Salvador Ledezma <ledezma@us.ibm.com>
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
