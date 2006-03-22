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
class Zend_Service_Yahoo_LocalResultSet extends Zend_Service_Yahoo_ResultSet {
    /**
     * @var string resultSetMapUrl the URL of a webpage containing a map
     *      graphic with all returned results plotted on it.
     */
    public $resultSetMapURL;

    /**
     * @todo docblock
     */
    protected $_namespace = "urn:yahoo:lcl";

    
    /**
     * @todo docblock
     */
    public function __construct(DomDocument $dom) {
        parent::__construct($dom);

        $this->resultSetMapURL = $this->_xpath->query('//yh:ResultSetMapUrl/text()')->item(0)->data;
    }

    
    /**
     * @todo docblock
     */
    public function current()
    {
        return new Zend_Service_Yahoo_LocalResult($this->_results->item($this->_currentItem));
    }
}
