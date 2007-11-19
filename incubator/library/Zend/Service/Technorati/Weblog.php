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
 * @see Zend_Service_Technorati_Author
 */
require_once 'Zend/Service/Technorati/Author.php';
      
/**
 * @see Zend_Service_Technorati_Utils
 */
require_once 'Zend/Service/Technorati/Utils.php';        


/**
 * TODO: phpdoc
 * 
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_Weblog
{
    /**
     * Blog name as written in the feed
     *
     * @var     string
     * @access  protected
     */
    protected $_name;

    /**
     * Base blog URL
     *
     * @var     Zend_Uri_Http
     * @access  protected
     */
    protected $_url;

    /**
     * RSS feed URL, if any
     *
     * @var     null|Zend_Uri_Http
     * @access  protected
     */
    protected $_rssUrl;

    /**
     * Atom feed URL, if any
     *
     * @var     null|Zend_Uri_Http
     * @access  protected
     */
    protected $_atomUrl;

    /**
     * Number of unique blogs linking this blog
     *
     * @var     int
     * @access  protected
     */
    protected $_inboundBlogs;

    /**
     * Number of incoming links to this blog
     *
     * @var     int
     * @access  protected
     */
    protected $_inboundLinks;

    /**
     * Last blog update timestamp
     *
     * @var     int
     * @access  protected
     */
    protected $_lastUpdate;

    /**
     * Technorati rank value for this blog
     *
     * @var     int
     * @access  protected
     */
    protected $_rank;
    
    /**
     * Blog latitude coordinate
     *
     * @var     float
     * @access  protected
     */
    protected $_lat;

    /**
     * Blog longitude coordinate
     *
     * @var     float
     * @access  protected
     */
    protected $_lon;

    /**
     * Whether the author who claimed this weblog has a photo
     *
     * @var     bool
     * @access  protected
     * @see     Zend_Service_Technorati_Author::$thumbnailPicture
     */
    protected $_hasPhoto = false;

    /**
     * A list of Zend_Service_Technorati_Author who claimed this blog
     *
     * @var     array
     * @access  protected
     */
    protected $_authors = array();


    /**
     * Parse given Weblog Element
     *
     * @param   DomElement $dom The ReST fragment for this weblog object
     * @return  void
     */
    public function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);

        /**
         * @todo   Create accessor method
         */

    	$result = $xpath->query('./name/text()', $dom);
        if ($result->length == 1) $this->setName($result->item(0)->data);
    	
        $result = $xpath->query('./url/text()', $dom);
        if ($result->length == 1) $this->setUrl($result->item(0)->data);
        
        $result = $xpath->query('./inboundblogs/text()', $dom);
        if ($result->length == 1) $this->setInboundBlogs($result->item(0)->data);
        
        $result = $xpath->query('./inboundlinks/text()', $dom);
        if ($result->length == 1) $this->setInboundBlogs($result->item(0)->data);
        
        $result = $xpath->query('./lastupdate/text()', $dom);
        if ($result->length == 1) $this->setLastUpdate($result->item(0)->data);

        /* The following elements needs more attention */

        $result = $xpath->query('./rssurl/text()', $dom);
        if ($result->length == 1) $this->setRssUrl($result->item(0)->data);
        
        $result = $xpath->query('./atomurl/text()', $dom);
        if ($result->length == 1) $this->setAtomUrl($result->item(0)->data);
                            
        $result = $xpath->query('./author', $dom);
        if ($result->length >= 1) {
            foreach ($result as $author) {
                $this->authors[] = new Zend_Service_Technorati_Author($author);
            }
        }

        /**
         * The following are optional elements
         * 
         * I can't find any official documentation about the following properties
         * however they are included in response DTD and/or test responses.
         */
        
        $result = $xpath->query('./rank/text()', $dom);
        if ($result->length == 1) $this->setRank($result->item(0)->data);

        $result = $xpath->query('./lat/text()', $dom);
        if ($result->length == 1) $this->setLat($result->item(0)->data);
        
        $result = $xpath->query('./lon/text()', $dom);
        if ($result->length == 1) $this->setLon($result->item(0)->data);

        $result = $xpath->query('./hasphoto/text()', $dom);
        if ($result->length == 1) $this->setHasPhoto($result->item(0)->data);
    }
    
    
    /**
     * Return weblog Name
     * 
     * @return  string  Weblog Name
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Return weblog URL
     * 
     * @return  null|Zend_Uri_Http  Weblog URL
     */
    public function getUrl() {
        return $this->_url;
    }
    
    /**
     * Return number of Inbound Blogs
     * 
     * @return  int     Inbound Blogs
     */
    public function getInboundBlogs() {
        return $this->_inboundBlogs;
    }
    
    /**
     * Return number of Inbound Links
     * 
     * @return  int     Inbound Links
     */
    public function getInboundLinks() {
        return $this->_inboundLinks;
    }
    
    /**
     * Return weblog Rss URL
     * 
     * @return  null|Zend_Uri_Http  Weblog Rss URL
     */
    public function getRssUrl() {
        return $this->_rssUrl;
    }
    
    /**
     * Return weblog Atom URL
     * 
     * @return  null|Zend_Uri_Http  Weblog Atom URL
     */
    public function getAtomUrl() {
        return $this->_atomUrl;
    }
    
    /**
     * Return weblog Last Update timestamp
     * 
     * @return  timestamp   Last Update timestamp
     */
    public function getLastUpdate() {
        return $this->_lastUpdate;
    }
    
    /**
     * Return weblog Rank value
     * 
     * Note. This property has no official documentation.
     * 
     * @return  int     Weblog rank value
     */
    public function getRank() {
        return $this->_rank;
    }
        
    /**
     * Return weblog latitude coordinate
     * 
     * Note. This property has no official documentation.
     * 
     * @return  float   Weblog latitude coordinate
     */
    public function getLat() {
        return $this->_lat;
    }
        
    /**
     * Return weblog longitude coordinate
     * 
     * Note. This property has no official documentation.
     * 
     * @return  float   Weblog longitude coordinate
     */
    public function getLon() {
        return $this->_lon;
    }
    
    /**
     * Return true whether the author who claimed this weblog has a photo
     * 
     * Note. This property has no official documentation.
     * 
     * @return  bool    TRUE if the author who claimed this weblog has a photo,
     *                  FALSE otherwise.
     */
    public function hasPhoto() {
        return (bool) $this->_hasPhoto;
    }
    
    /**
     * Return weblog authors
     * 
     * @return  array   List of Zend_Service_Technorati_Author authors
     */
    public function getAuthors() {
        return $this->_authors;
    }
    
    
    /**
     * Set weblog Name
     * 
     * @param   string $input   Weblog Name input value
     * @return  void
     */
    public function setName($input) {
        $this->_name = (string) $input;
    }

    /**
     * Set weblog URL
     * 
     * @param   string|Zend_Uri_Http $input Weblog URL
     * @return  void
     * @throws  Zend_Service_Technorati_Exception if $input is an invalid URI
     *          (via Zend_Service_Technorati_Utils::setUriHttp)
     */
    public function setUrl($input) {
        $this->_url = Zend_Service_Technorati_Utils::setUriHttp($input);
    }
    
    /**
     * Set number of Inbound Blogs
     * 
     * @param   int $input      Number of Inbound Blogs
     * @return  void
     */
    public function setInboundBlogs($input) {
        $this->_inboundBlogs = (int) $input;
    }
    
    /**
     * Set number of Inbound Links
     * 
     * @param   int $input      Number of Inbound Links
     * @return  void
     */
    public function setInboundLinks($input) {
        $this->_inboundLinks = (int) $input;
    }

    /**
     * Set weblog Rss URL
     * 
     * @param   string|Zend_Uri_Http $input Weblog Rss URL
     * @return  void
     * @throws  Zend_Service_Technorati_Exception if $input is an invalid URI
     *          (via Zend_Service_Technorati_Utils::setUriHttp)
     */
    public function setRssUrl($input) {
        $this->_rssUrl = Zend_Service_Technorati_Utils::setUriHttp($input);
    }

    /**
     * Set weblog Atom URL
     * 
     * @param   string|Zend_Uri_Http $input Weblog Atom URL
     * @return  void
     * @throws  Zend_Service_Technorati_Exception if $input is an invalid URI
     *          (via Zend_Service_Technorati_Utils::setUriHttp)
     */
    public function setAtomUrl($input) {
        $this->_atomUrl = Zend_Service_Technorati_Utils::setUriHttp($input);
    }
    
    /**
     * Set weblog Last Update timestamp
     * 
     * @param   string $input   last update timestamp
     * @return  void
     */
    public function setLastUpdate($input) {
        $this->_lastUpdate = strtotime($input);
    }
    
    /**
     * Set weblog Rank
     * 
     * Note. This property has no official documentation.
     * 
     * @param   int $input
     * @return  void
     */
    public function setRank($input) {
        $this->_rank = (int) $input;
    }
        
    /**
     * Set weblog latitude coordinate
     * 
     * Note. This property has no official documentation.
     *  
     * @param   float
     * @return  void
     */
    public function setLat($input) {
        $this->_lat = (float) $input;
    }
        
    /**
     * Set weblog longitude coordinate
     * 
     * Note. This property has no official documentation.
     * 
     * @param   float
     * @return  void
     */
    public function setLon($input) {
        $this->_lon = (float) $input;
    }
        
    /**
     * Set hasPhoto property
     * 
     * Note. This property has no official documentation.
     * 
     * @param   bool
     * @return  void
     */
    public function setHasPhoto($input) {
        $this->_hasPhoto = (bool) $input;
    }
    
}
