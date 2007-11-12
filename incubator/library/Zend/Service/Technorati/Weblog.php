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
 * @version    $Id:$
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
class Zend_Service_Technorati_Weblog
{
    /**
     * Blog name as written in the feed
     *
     * @var string
     */
    public $name;

    /**
     * Base blog URL
     *
     * @var Zend_Uri_Http
     */
    public $url;

    /**
     * RSS feed URL, if any
     *
     * @var null|Zend_Uri_Http
     */
    public $rssUrl;

    /**
     * Atom feed URL, if any
     *
     * @var null|Zend_Uri_Http
     */
    public $atomUrl;

    /**
     * Number of unique blogs linking this blog
     *
     * @var int
     */
    public $inboundBlogs;

    /**
     * Number of incoming links to this blog
     *
     * @var int
     */
    public $inboundLinks;

    /**
     * Technorati rank value for this blog
     *
     * @var int
     */
    public $rank;

    /**
     * Last blog update timestamp
     *
     * @var int
     */
    public $lastUpdate;

    /**
     * Blog latitude coordinate
     *
     * @var float
     */
    public $lat;

    /**
     * Blog longitude coordinate
     *
     * @var float
     */
    public $lon;

    /**
     * Whether the author who claimed this weblog has a photo
     *
     * @var int
     * @see Zend_Service_Technorati_Author::$thumbnailPicture
     */
    public $hasPhoto;

    /**
     * A list of Zend_Service_Technorati_Author who claimed this blog
     *
     * @var array
     */
    public $authors = array();


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
         * @see Zend_Uri
         */
        require_once 'Zend/Uri.php';        
    	
        $this->name         = (string) $xpath->query('./name/text()', $dom)->item(0)->data;
        $this->url          = Zend_Uri::factory($xpath->query('./url/text()', $dom)->item(0)->data);
        $this->inboundBlogs = (int) $xpath->query('./inboundblogs/text()', $dom)->item(0)->data;
        $this->inboundLinks = (int) $xpath->query('./inboundlinks/text()', $dom)->item(0)->data;
        $this->lastUpdate   = strtotime($xpath->query('./lastupdate/text()', $dom)->item(0)->data);

        /* The following elements needs more attention */

        $result = $xpath->query('./rssurl/text()', $dom);
        $this->rssUrl       = $result->length == 1
                            ? Zend_Uri::factory($result->item(0)->data)
                            : null;

        $result = $xpath->query('./atomurl/text()', $dom);
        $this->atomUrl      = $result->length == 1
                            ? Zend_Uri::factory($result->item(0)->data)
                            : null;

        /**
         * @see Zend_Service_Technorati_Author
         */
        require_once 'Zend/Service/Technorati/Author.php';        
                            
        $result = $xpath->query('./author', $dom);
        if ($result->length >= 1) {
            foreach ($result as $author) {
                $this->authors[] = new Zend_Service_Technorati_Author($author);
            }
        }

        /* The following are optional elements */
        
        $result = $xpath->query('./rank/text()', $dom);
        $this->rank         = $result->length == 1
                            ? (int) $result->item(0)->data
                            : null;        
        
        $result = $xpath->query('./lat/text()', $dom);
        $this->lat          = $result->length == 1
                            ? (float) $result->item(0)->data
                            : null;

        $result = $xpath->query('./lon/text()', $dom);
        $this->lon          = $result->length == 1
                            ? (float) $result->item(0)->data
                            : null;

       $result = $xpath->query('./hasphoto/text()', $dom);
       $this->hasPhoto      = $result->length == 1
                            ? (int) $result->item(0)->data
                            : 0;
    }
}
