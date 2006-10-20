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

require_once 'Zend/Locale/UTF8/Exception.php';
/**
 * @category Zend
 * @package Zend_Locale
 * @subpackage UTF8
 * @copyright Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale_UTF8
{
	const CURRENT	= 0;
	const AUTO 		= 1;
	const PHP5 		= 2;
	const PHP6 		= 3;
	
	/**
	 * current version
	 *
	 * @access private
	 * @staticvar integer
	 */
	protected static $_current 	= null;
	
	/**
	 * Contains instances of the UTF-8 libraries. (PHP5 and PHP6)
	 *
	 * @access protected
	 * @staticvar array
	 */
	protected static $_libraries 	= array();
	
	/**
	 * If called without parameter getLibrary() returns the current library,
	 * otherwise the library of the stated version.
	 *
	 * @access public
	 * @static 
	 * @param integer $version
	 * @return Zend_Locale_UTF8_Interface
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public static function &getLibrary( $version = self::CURRENT )
	{
		$_version = self::PHP5;
		
		switch ( $version ) {
			case self::AUTO:
				$_version = self::_determineVersion();
				break;
				
			case self::CURRENT:
				$_version = self::$_current;
				break;
				
			case self::PHP5:
			case self::PHP6:
				self::$_version = $version;
				break;
				
			default:
				throw new Zend_Locale_UTF8_Exception('Version is not supported by Zend_Locale_UTF8.');
		}
		
		/**
		 * @todo implement PHP6
		 */
		if ( !isset(self::$_libraries[$_version]) )	{
			switch ( $_version ) {
				case self::PHP6:
					Zend::loadClass('Zend_Locale_UTF8_PHP6');
					self::$_libraries[$_version] = new Zend_Locale_UTF8_PHP6();
					break;
				
				case self::PHP5:
				default:
					Zend::loadClass('Zend_Locale_UTF8_PHP5');
					self::$_libraries[$_version] = new Zend_Locale_UTF8_PHP5();
			}
		}
		
		return self::$_libraries[$_version];
	}
	
	/**
	 * Overrides predetermined version.
	 *
	 * @access public
	 * @param int $version
	 * @static 
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public static function setVersion( $version )
	{
		
		switch ( $version ) {
			case self::AUTO:
				self::$_current = self::_determineVersion();
				break;
				
			case self::CURRENT:
				break;
				
			case self::PHP5:
			case self::PHP6:
				self::$_current = $version;
				break;
				
			default:
				throw new Zend_Locale_UTF8_Exception('Version not supported by Zend_Locale_UTF8.');
		}
	}
	
	/**
	 * Returns current version.
	 *
	 * @access public
	 * @return integer
	 * @static 
	 */
	public static function getVersion()
	{
		if ( is_null(self::$_current) ) {
			self::$_current	= self::_determineVersion();
		}
		
		return self::$_current;
	}
	
	/**
	 * Returns a string object(either PHP5 or 6)
	 *
	 * @access public
	 * @param strin $string
	 * @return Zend_Locale_UTF8_StringInterface
	 * @static 
	 */
	public static function string ( $string )
	{
		return self::getLibrary()->string( $string );
	}
	
	/**
	 * Determines which Version of PHP is used.
	 *
	 * @todo implement function
	 * @access protected
	 * @static 
	 * @return integer
	 */
	protected static function _determineVersion()
	{
		return self::PHP5;
	}
	
}