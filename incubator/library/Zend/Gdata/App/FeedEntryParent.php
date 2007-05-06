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
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Gdata_Data
 */
require_once 'Zend/Gdata/Data.php';

/**
 * @see Zend_Gdata_App_Exception
 */
require_once 'Zend/Gdata/App/Exception.php';

/**
 * @see Zend_Gdata_App_Extension_Element
*/
require_once 'Zend/Gdata/App/Extension/Element.php';

/**
 * @see Zend_Gdata_App_Extension_Element
*/
require_once 'Zend/Gdata/App/Extension/Element.php';

/**
 * @see Zend_Gdata_App_Extension_Id
 */
require_once 'Zend/Gdata/App/Extension/Id.php';

/**
 * @see Zend_Gdata_App_Extension_Title
 */
require_once 'Zend/Gdata/App/Extension/Title.php';

/**
 * @see Zend_Gdata_App_Extension_Content
 */
require_once 'Zend/Gdata/App/Extension/Content.php';

/**
 * @see Zend_Gdata_App_Extension_Link
 */
require_once 'Zend/Gdata/App/Extension/Link.php';

/**
 * Abstract class for common functionality in entries and feeds
 *
 * @category   Zend
 * @package    Zend_Gdata_App
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Gdata_App_FeedEntryParent extends Zend_Gdata_App_Base
{

    /**
     * HTTP client object to use for retrieving feeds
     *
     * @var Zend_Http_Client
     */
    protected $_httpClient = null;

    protected $_id = null;
    protected $_title = null;
    protected $_link = array();
    protected $_content = null;

    /**
     * Constructs a Feed or Entry
     * TODO: Determine if we should have the uri param here
     */
    public function __construct($uri = null, $element = null)
    {
        if (!($element instanceof DOMElement)) {
            if ($element) {
                // Load the feed as an XML DOMDocument object
                @ini_set('track_errors', 1);
                $doc = new DOMDocument();
                $success = @$doc->loadXML($element);
                @ini_restore('track_errors');
                if (!$success) {
                    throw new Zend_Gdata_App_Exception("DOMDocument cannot parse XML: $php_errormsg");
                }
                $element = $doc->getElementsByTagName($this->_rootElement)->item(0);
                if (!$element) {
                    throw new Zend_Gdata_App_Exception('No root <' . $this->_rootElement . '> element found, cannot parse feed.');
                }
                $this->transferFromDOM($element);
            }
        } else {
	    $this->transferFromDOM($element);
        }
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feed.
     *
     * @param  Zend_Http_Client $httpClient
     * @return Zend_Gdata_App_Feed Provides a fluent interface
     */
    public function setHttpClient(Zend_Http_Client $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }


    /**
     * Gets the HTTP client object. If none is set, a new Zend_Http_Client will be used.
     *
     * @return Zend_Http_Client_Abstract
     */
    public function getHttpClient()
    {
        if (!$this->_httpClient instanceof Zend_Http_Client) {
            /**
             * @see Zend_Http_Client
             */
            require_once 'Zend/Http/Client.php';
            $this->_httpClient = new Zend_Http_Client();
        }

        return $this->_httpClient;
    }


    public function getDOM($doc = null)
    {
        $element = parent::getDOM($doc);
        if ($this->_title != null) {
            $element->appendChild($this->_title->getDOM($element->ownerDocument));
        }
        if ($this->_id != null) {
            $element->appendChild($this->_id->getDOM($element->ownerDocument));
        }
        if ($this->_content != null) {
            $element->appendChild($this->_content->getDOM($element->ownerDocument));
        }
        foreach ($this->_link as $link) {
            $element->appendChild($link->getDOM($element->ownerDocument));
        }
        return $element;
    }

    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case Zend_Gdata_Data::lookupNamespace('atom') . ':' . 'id':
            $id = new Zend_Gdata_App_Extension_Id();
            $id->transferFromDOM($child);
            $this->_id = $id;
            break;
        case Zend_Gdata_Data::lookupNamespace('atom') . ':' . 'title':
            $title = new Zend_Gdata_App_Extension_Title();
            $title->transferFromDOM($child);
            $this->_title = $title;
            break;
        case Zend_Gdata_Data::lookupNamespace('atom') . ':' . 'content':
            $content = new Zend_Gdata_App_Extension_Content();
            $content->transferFromDOM($child);
            $this->_content = $content;
            break;
        case Zend_Gdata_Data::lookupNamespace('atom') . ':' . 'link':
            $link = new Zend_Gdata_App_Extension_Link();
            $link->transferFromDOM($child);
            $this->_link[] = $link;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @param Zend_Gdata_App_Extension_Title $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setTitle($value)
    {
        $this->_title = $value;
        return $this; 
    }

    public function getContent()
    {
        return $this->_content;
    }

    /**
     * @param Zend_Gdata_App_Extension_Content $value 
     * @return Zend_Gdata_App_Entry Provides a fluent interface
     */
    public function setContent($value)
    {
        $this->_content = $value;
        return $this; 
    }

    public function getId()
    {
        return $this->_id;
    }

    public function getLink($rel = null)
    {
        if ($rel == null) {
            return $this->_link;
        } else {
            foreach ($this->_link as $link) {
                if ($link->rel == $rel) {
                    return $link;
                }
            }
            return null;
        }
    }

}
