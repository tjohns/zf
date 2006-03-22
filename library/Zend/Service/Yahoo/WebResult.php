<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * @todo coding standards: naming of instance variables
 * @package    Zend_Service
 * @subpackage Yahoo
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
class Zend_Service_Yahoo_WebResult extends Zend_Service_Yahoo_Result {
    /**
     * @var string $Summary a summary of the result
     */
    public $Summary;
    
    /**
     * @var string MimeType the file type of the result (text, html, pdf, etc.)
     */
    public $MimeType;
    
    /**
     * @var string $ModificationDate the modification time of the result (as a unix timestamp)
     */
    public $ModificationDate;
    
    /**
     * @var string $CacheUrl the URL for the Yahoo cache of this page, if it exists
     */
    public $CacheUrl;
    
    /**
     * @var int $CacheSize the size of the cache entry
     */
    public $CacheSize;

    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:srch";

    
    /**
     * @todo dockblock
     */
    public function __construct(DomElement $result) {
        $this->_fields = array('Summary','MimeType','ModificationDate');
        parent::__construct($result);

        $this->_xpath = new DOMXPath($result->ownerDocument);
    	$this->_xpath->registerNamespace("yh", $this->_namespace);

        $this->CacheUrl = (string) $this->_xpath->query("//yh:Cache/yh:Url/text()")->item(0)->data;
        $this->CacheSize = (string) $this->_xpath->query("//yh:Cache/yh:Size/text()")->item(0)->data;
    }
}
