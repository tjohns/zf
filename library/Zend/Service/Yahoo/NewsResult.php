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
class Zend_Service_Yahoo_NewsResult extends Zend_Service_Yahoo_Result {
    /**
     * @var string $Summary Sumamry text associated with the result article
     */
    public $Summary;
    
    /**
     * @var string $NewsSource the company who distributed the article
     */
    
    public $NewsSource;
    /**
     *  @var string $NewsSourceUrl the URL for the company who distributed the article
     */
    public $NewsSourceUrl;

    /**
     * @var string $Language the language the article is in
     */
    public $Language;
    
    /**
     * @var string $PublishDate the date the article was published (in unix timestamp format)
     */
    public $PublishDate;
    
    /**
     * @var string $ModificationDate the date the article was modified (in unix timestamp format)
     */
    public $ModificationDate;
    
    /**
     * @var Zend_Service_Yahoo_Image $Thumbnail the thubmnail image for the article, if it exists
     */
    public $Thumbnail;

    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:yn";


    /**
     * @todo docblock
     */
    public function __construct(DomElement $result) {
        $this->_fields = array('Summary','NewsSource','NewsSourceUrl','Language','PublishDate',
                        'ModificationDate','Thumbnail');
        parent::__construct($result);

        $this->setThumbnail();
    }
}
