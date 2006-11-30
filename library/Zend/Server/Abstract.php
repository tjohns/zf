<?php
require_once 'Zend/Server/Interface.php';
abstract class Zend_Server_Abstract implements Zend_Server_Interface {
	/**
     * @var array PHP's Magic Methods, these are ignored
     */
    static protected $magic_methods = array(
                                '__construct',
                                '__destruct',
                                '__get',
                                '__set',
                                '__call',
                                '__sleep',
                                '__wakeup',
                                '__isset',
                                '__unset',
                                '__tostring',
                                '__clone',
                                '__set_state',
                                );

   	/**
	 * Lowercase a string
	 *
	 * @param string $value
	 * @param string $key
	 * @return string Lower cased string
	 */
	static public function lowerCase(&$value, &$key)
	{
		return $value = strtolower($value);
	}
}