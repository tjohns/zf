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
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * Zend_Service_Amazon
 */
require_once 'Zend/Service/Amazon.php';


/**
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2006 Zend Technologies Inc. (http://www.zend.com)
 * @license    Zend Framework License version 1.0
 */
class Zend_Service_Amazon_Query extends Zend_Service_Amazon
{
	private $_search = array();
	private $_searchIndex = null;

	function __call($method, $args)
	{
	    /**
	     * @todo revisit this - also add some bounds checking for $args
	     */

		if (strtolower($method) == 'asin') {
			$this->_searchIndex = 'asin';
			$this->_search['itemId'] = $args[0];
			return $this;
		}

		if (strtolower($method) == 'category') {
			if (isset(self::$_searchParams[$args[0]])) {
			    $this->_searchIndex = $args[0];
				$this->_search['SearchIndex'] = $args[0];
			} else {
				throw new Zend_Service_Exception('Unknown Search Category');
			}
		} else if ($this->_search['SearchIndex'] !== null || $this->_searchIndex !== null || $this->_searchIndex == 'asin') {
			$this->_search[$method] = $args[0];
		} else {
			throw new Zend_Service_Exception('You must set a category before setting the search parameters');
		}

		return $this;
	}

    /**
     * Search using the prepared query
     *
     * @return Zend_Service_Amazon_Item|Zend_Service_Amazon_ResultSet
     */
	function search()
	{
		if ($this->_searchIndex == 'asin') {
			return $this->itemLookup($this->_searchIndex['itemId'], $this->_search);
		}
		return $this->itemSearch($this->_search);
	}
}

