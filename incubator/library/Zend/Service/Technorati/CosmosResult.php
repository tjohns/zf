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
class Zend_Service_Technorati_CosmosResult extends Zend_Service_Technorati_Result
{
    /**
     * TODO: phpdoc
     * 
     * @var
     */
    public $weblog;

    /**
     * TODO: phpdoc
     * 
     * @var
     */
    public $nearestPermalink;

    /**
     * TODO: phpdoc
     * 
     * @var
     */
    public $excerpt;

    /**
     * TODO: phpdoc
     * 
     * @var
     */
    public $linkCreated;

    /**
     * TODO: phpdoc
     * 
     * @var
     */
    public $linkUrl;


    /**
     * Constructs a new object object from DOM Element.
     *
     * @param   DomElement $dom the ReST fragment for this object
     */
    public function __construct(DomElement $result)
    {
        $this->_fields = array( 'nearestPermalink'  => 'nearestpermalink',
                                'excerpt'  => 'excerpt',
                                'linkCreated'  => 'linkcreated',
                                'linkUrl'  => 'linkurl',
                                );
        parent::__construct($result);

        /**
         * @todo    Consider to use an utility method to set weblog,
         *          see Zend_Service_Yahoo_ImageResult
         */
        $xpath = new DOMXPath($result->ownerDocument);

        // weblog object field
        $result = $xpath->query('./weblog', $result);
        if ($result->length == 1) {
            /**
             * @see Zend_Service_Technorati_Weblog
             */
            require_once 'Zend/Service/Technorati/Weblog.php';
            $this->weblog = new Zend_Service_Technorati_Weblog($result->item(0));
        }

        // filter fields
        /** @todo Each field needs to be filtered */
    }
}
