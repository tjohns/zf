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
class Zend_Service_Yahoo_LocalResult extends Zend_Service_Yahoo_Result {
    /**
     * @var string $Address Street address of the result
     */
    public $Address;
    
    /**
     * @var string $City City in which the result resides
     */
    public $City;
    
    /**
     * @var string $State State in which the result resides
     */
    public $State;
    
    /**
     * @var string $Phone Phone number for the result
     */
    public $Phone;
    
    /**
     * @var int $Rating User-submitted rating for the result
     */
    public $Rating;
    
    /**
     * @var float $Distance The distance to the result from your specified location
     */
    public $Distance;
    
    /**
     * @var string $MapUrl A URL of a map for the result.
     */
    public $MapUrl;
    
    /**
     * @var string $BusinessUrl The URL for the business website, if known
     */
    public $BusinessUrl;
    
    /**
     * @var string $BusinessClickUrl The URL for linking to the business website, if known
     */
    public $BusinessClickUrl;
    
    
    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:lcl";

    
    /**
     * @todo docblock
     */
    public function __construct(DomElement $result) {
        $this->_fields = array('Address','City', 'City', 'State', 'Phone','Rating','Distance','MapUrl',
                            'BusinessUrl', 'BusinessClickUrl');
        parent::__construct($result);
    }
}
