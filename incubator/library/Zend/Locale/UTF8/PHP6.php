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
 * @category Zend
 * @package Zend_Locale
 * @subpackage UTF8
 * @copyright Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Locale/UTF8/StringInterface.php'; 
require_once 'Zend/Locale/UTF8/Exception.php'; 

/**
 * @category Zend
 * @package Zend_Locale
 * @subpackage UTF8
 * @copyright Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 * @see http://www.cl.cam.ac.uk/~mgk25/ucs/examples/UTF-8-test.txt
 */

class Zend_Locale_UTF8_PHP6 implements Zend_Locale_UTF8_Interface
{

	/**
	 * Returns the string.
	 * 
	 * @access public
	 * @return string
	 */
	public function __toString()
	{}

	/**
	 * Returns the char value at the specified index.
	 *
	 * @access public
	 * @param integer $index
	 * @return string
	 */
	public function charAt( $index )
	{}
	
	/**
	 *  Returns the character (Unicode code point) at the specified index.
	 *
	 * @access public
	 * @param integer $index
	 * @return integer
	 */
	public function codePointAt( $index )
	{}
	
	/**
	 * Returns true if and only if this string contains the specified sequence of char values.
	 *
	 * @param string $string
	 * @return boolean
	 */
	public function contains( $string )
	{}
	
	/**
	 * Returns the index within this string of the first occurrence of the specified string.
	 *
	 * @access public
	 * @param string $string
	 * @return integer
	 */
	public function indexOf( $string )
	{}
	
	/**
	 * Returns the length of this string.
	 *
	 * @access public
	 * @return integer
	 */
	public function length()
	{}
	
	/**
	 * Converts all of the characters in this String to upper case.
	 *
	 * @access public
	 * @return string
	 */
	public function toUpperCase()
	{}
	
	/**
	 * Converts all of the characters in this String to lower case.
	 *
	 * 
	 * @access public
	 * @return string
	 */
	public function toLowerCase()
	{}
}