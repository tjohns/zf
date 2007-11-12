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
class Zend_Service_Technorati_Author
{
    /**
     * Author first name
     *
     * @var string
     */
    public $firsName;

    /**
     * Author last name
     *
     * @var string
     */
    public $lastName;

    /**
     * Technorati account description
     *
     * @var string
     */
    public $description;

    /**
     * Technorati account biography
     *
     * @var string
     */
    public $bio;

    /**
     * Technorati account thumbnail picture URL, if any
     *
     * @var null|Zend_Uri_Http
     */
    public $thumbnailPicture;


    /**
     * Parse given Author Element
     *
     * @param   DomElement $dom The ReST fragment for this author object
     * @return  void
     * 
     * @todo    Check which elements are optional
     */
    public function __construct(DomElement $dom)
    {
    	$xpath = new DOMXPath($dom->ownerDocument);

        $this->firstName    = (string) $xpath->query('./firstname/text()', $dom)->item(0)->data;
        $this->lastName     = (string) $xpath->query('./lastname/text()', $dom)->item(0)->data;
        $this->username     = (string) $xpath->query('./username/text()', $dom)->item(0)->data;
        $this->description  = (string) $xpath->query('./description/text()', $dom)->item(0)->data;
        $this->bio          = (string) $xpath->query('./bio/text()', $dom)->item(0)->data;

        /* 
         * The following elements need more attention 
         */

        /**
         * @see Zend_Uri
         */
        require_once 'Zend/Uri.php';        
        
        $result = $xpath->query('./thumbnailpicture/text()', $dom);
        $this->thumbnailPicture = $result->length == 1
                                ? Zend_Uri::factory($result->item(0)->data)
                                : null;
    }
}
