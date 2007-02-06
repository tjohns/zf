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
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Rest
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Rest_Client_Result implements IteratorAggregate {
	/**
	 * @var SimpleXMLElement
	 */
	private $_sxml;
	
	/**
	 * Constructor
	 *
	 * @param string $data XML Result
	 */
	public function __construct($data)
	{
		$this->_sxml = simplexml_load_string($data);
	}

    /**
     * Casts a node to its appropriate PHP value
     *
     * @param SimpleXMLElement $value 
     * @return mixed
     */
    protected function _castReturnValue(SimpleXMLElement $value)
    {
        $node = dom_import_simplexml($value);
        return $node->nodeValue;
    }
	
	/**
	 * Get Property Overload
	 *
	 * @param string $name
	 * @return mixed Null if not found, PHP value if only one value found, array of SimplXMLElement nodes otherwise
	 */
	public function __get($name)
	{
		if (isset($this->_sxml->{$name})) {
			return $this->_castReturnValue($this->_sxml->{$name});
		}
		
		$result = $this->_sxml->xpath("//$name");
        $count  = count($result);
		
		if ($count == 0) {
			return null;
		} elseif ($count == 1) {
			return $this->_castReturnValue($result[0]);
		} else {
			return $result;
		}
	}
	
	/**
	 * Isset Overload
	 *
	 * @param string $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		if (isset($this->_sxml->{$name})) {
			return true;
		}
		
		$result = $this->_sxml->xpath("//$name");
		
		if (sizeof($result) > 0) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Implement IteratorAggregate::getIterator()
	 *
	 * @return SimpleXMLIterator
	 */
	public function getIterator()
	{
		return $this->_sxml;
	}

	/**
	 * Method Call overload
	 *
	 * @param string $method Method Name
	 * @param array $args Method Args
	 * @return mixed
	 */
	public function __call($method, $args)
	{
        if (method_exists($this->_sxml, $method)) {
            return call_user_func_array(array($this->_sxml, $method), $args);
        }

        require_once 'Zend/Rest/Client/Exception.php';
        throw new Zend_Rest_Client_Exception('Invalid method requested');
	}
	
	/**
	 * Get Request Status
	 *
	 * @return boolean
	 */
	public function getStatus()
	{
		$status = $this->_sxml->xpath('//status/text()');
		
		$status = strtolower($status[0]);
		
		if (ctype_alpha($status) && $status == 'success') {
			return true;
		} elseif (ctype_alpha($status) && $status != 'success') {
			return false;
		} else {
			return (bool) $status;
		}
	}
	
	public function isError()
	{
		$status = $this->getStatus();
		if ($status) {
			return false;
		} else {
			return true;
		}
	}
	
	public function isSuccess()
	{
		$status = $this->getStatus();
		if ($status) {
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * toString overload
	 *
	 * Be sure to only call this when the result is a single value!
	 * 
	 * @return string
	 */
	public function __toString()
	{
		if (!$this->getStatus()) {
			$message = $this->_sxml->xpath('//message');
			return (string) $message[0]; 
		} else {
			$result = $this->_sxml->xpath('//response');
			if (sizeof($result) > 1) {
				return (string) "An error occured.";
			} else {
				return (string) $result[0];
			}
		}
	}
}
