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

/**
 * @category Zend
 * @package Zend_Locale
 * @subpackage UTF8
 * @copyright Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */

interface Zend_Locale_UTF8_StringInterface {
	
	/**
	 * Returns the string.
	 * 
	 * @access public
	 * @return string
	 */
	public function __toString();

	/**
	 * Returns the char value at the specified index.
	 *
	 * @access public
	 * @param integer $index
	 * @return string
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public function charAt( $index );
	
	/**
	 *  Returns the character (Unicode code point) at the specified index.
	 *
	 * @access public
	 * @param integer $index
	 * @return integer
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public function codePointAt( $index );

	/**
	 * Concatenates the specified string to the end of this string.
	 * 
	 * If the length of the argument string is 0, then this String object is returned.
	 * Otherwise, a new String object is created, representing a character sequence that
	 * is the concatenation of the character sequence represented by this String object
	 * and the character sequence represented by the argument string.
	 *
	 * @access public
	 * @param mixed $string
	 */
	public function concat( $string );
	
	/**
	 * Returns true if and only if this string contains the specified sequence of char values.
	 *
	 * @param string $string
	 * @return boolean
	 */
	public function contains( $string );
	
	/**
	 * Compares this string to another one.
	 *
	 * @access public
	 * @param mixed $string
	 * @return boolean
	 */
	public function equals( $string );
	
	/**
	 * Returns the index within this string of the first occurrence of the specified string.
	 *
	 * @access public
	 * @param string $string
	 * @return integer
	 */
	public function indexOf( $string );
		
	/**
	 * Returns the index within this string of the last occurrence of the specified string.
	 *
	 * @access public
	 * @param mixed $string
	 * @return integer
	 */
	public function lastIndexOf( $string );
	
	/**
	 * Returns the length of this string.
	 *
	 * @access public
	 * @return integer
	 */
	public function length();
	
	/**
	 * Returns a new string that is a substring of this string.
	 * 
	 * The substring begins at the specified beginIndex and extends to the
	 * character at index endIndex - 1.
	 * Thus the length of the substring is endIndex-beginIndex.
	 * 
	 * If called without $endIndex substring() returns a new string from $beginIndex
	 * to the end of the string
	 *
	 * @param integer $beginIndex
	 * @param integer $endIndex
	 */
	public function substring($beginIndex, $endIndex=null);
	
	/**
	 * Converts all of the characters in this String to upper case.
	 *
	 * @access public
	 * @return string
	 */
	public function toUpperCase();
	
	/**
	 * Converts all of the characters in this String to lower case.
	 *
	 * 
	 * @access public
	 * @return string
	 */
	public function toLowerCase();
}