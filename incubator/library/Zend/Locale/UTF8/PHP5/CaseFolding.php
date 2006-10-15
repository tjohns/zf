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

class Zend_Locale_UTF8_PHP5_CaseFolding
{
	/**
	 * Path to folder that contains the Unicode Character Database
	 * @see http://www.unicode.org/Public/UNIDATA/
	 */
	const folderUnicode 	= 'Zend/Locale/UTF8/PHP5/Data/Unicode';
	
	/**
	 * Filename of http://www.unicode.org/Public/UNIDATA/CaseFolding.txt
	 *
	 */
	const fileCaseFolding	= 'CaseFolding.txt';
	
	/**
	 * Filename of the Case Folding cache file
	 */
	const cacheCaseFolding	= 'CaseFolding.cache';
	
	/**
	 * Conversion table
	 *
	 * @access private
	 * @var array
	 */
	private $_tableCaseFolding = array();
	
	/**
	 * Loads the Case Folding Table either from the cache or directly depending on $chache
	 *
	 * @access public
	 * @param boolean $cache
	 */
	public function __construct( $cache = true ) {
		
		$fileCaseFolding 	= self::folderUnicode.'/'.self::fileCaseFolding;
		$cacheCaseFolding 	= self::folderUnicode.'/'.self::cacheCaseFolding;
		
		if ( !$cache || !file_exists($cacheCaseFolding)  ) {
			
			$this->_loadCaseTable($fileCaseFolding);
			
			if ( $cache ) {
				$this->_writeCaseTableToCache($cacheCaseFolding);
			}
				
		} else {
			$this->_loadCaseTableFromCache($cacheCaseFolding);
		}
	}
	
	/**
	 * Converts a char to lower using its char point
	 *
	 * @access public
	 * @param integer $char
	 * @return integer
	 */
	public function charToLower( $char )
	{
		return isset($this->_tableCaseFolding[$char]) ? $this->_tableCaseFolding[$char] : $char;
	}
	
	/**
	 * Converts a char to upper using its char point
	 *
	 * @access public
	 * @param integer $char
	 * @return integer
	 */
	public function charToUpper( $char )
	{
		return in_array($char, $this->_tableCaseFolding) ? array_search($char, $this->_tableCaseFolding) : $char;
	}
	
	/**
	 * Loads the Case Folding Table
	 * 
	 * @access private
	 * @param string $fileCaseFolding
	 */
	private function _loadCaseTable( $fileCaseFolding )
	{
		if ( file_exists($fileCaseFolding) ) {
			$content = file_get_contents($fileCaseFolding, true);
			
			preg_match_all('/^([0-9a-fA-f]*); [C|F];[\\s]*([0-9a-fA-F]*);/m', $content, $result, PREG_SET_ORDER);

			for( $i=0; $i<count($result); $i++ ) {
				$this->_tableCaseFolding[ hexdec($result[$i][1]) ] = hexdec($result[$i][2]);
			}			
			
		} else {
			throw new Zend_Locale_UTF8_Exception('Unable to load Unicode Character Database.');
		}
	}
	
	/**
	 * Loads Case Folding Table from the cache
	 *
	 * @access private
	 * @param string $cacheFile
	 * @return boolean
	 */
	private function _loadCaseTableFromCache( $cacheFile )
	{
		if ( file_exists($cacheFile) ) {
			$cacheData	= file_get_contents($cacheFile, true);
			
			if ( $cacheData ) {
				$this->_tableCaseFolding = unserialize($cacheData);
				return true;
			}
		}
		
		throw new Zend_Locale_UTF8_Exception('Unable to load Unicode Character Database from the cache.');
	}

	/**
	 * Writes the Case Folding Table to the cache
	 *
	 * @access private
	 * @param string $cacheFile
	 */
	private function _writeCaseTableToCache ( $cacheFile )
	{
		file_put_contents($cacheFile, serialize($this->_tableCaseFolding));
	}
	
}