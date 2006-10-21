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

class Zend_Locale_UTF8_PHP5_String implements Zend_Locale_UTF8_StringInterface
{

	/**
	 * Contains the actual string which is represented as an array of UTF-8 hex codes.
	 *
	 * @access private
	 * @var array
	 */
	private $sequence;
	
	/**
	 * Contais the string itself.
	 *
	 * @var string
	 */
	private $string = null;
	
	/**
	 *
	 * @staticvar Zend_Locale_UTF8_PHP5_CaseFolding
	 * @see Zend_Locale_UTF8_PHP5_CaseFolding
	 * 
	 */
	private static $_caseFoldingTable = null;
	
	/**
	 * Constructor that takes either a string, an array of UTF-8 codes
	 * or another Zend_Locale_UTF8_PHP5_String object to create a new
	 * string.
	 *
	 * @access public
	 * @param mixed $string
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public function __construct( $string )
	{
		if ( is_array($string) ) {
			$this->sequence = $string;
			$this->string	= $this->__toString();
		} elseif ( $string instanceof Zend_Locale_UTF8_PHP5_String ) {
			$this->sequence = $string->getSequence();
			$this->string	= $string->__toString();
		} elseif ( is_string($string) || is_int($string) || is_float($string) ) {
			$this->sequence = $this->_decode($string);
			$this->string	= $string;
		} else {
			throw new Zend_Locale_UTF8_Exception(gettype($string).' is not supported by Zend_Locale_UTF8_PHP5_String.');
		}
	}
	
	/**
	 * Returns the string.
	 * 
	 * @access public
	 * @return string
	 */
	public function __toString()
	{
		if ( is_null($this->string) ) {
			$this->string = '';
			for ( $i=0; $i<count($this->sequence); $i++ ) {
				$this->string .= $this->_chr($this->sequence[$i]);
			}
		}
		return $this->string;
	}
	
	/**
	 * Returns the char value at the specified index.
	 *
	 * @access public
	 * @param integer $index
	 * @return string
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public function charAt( $index )
	{
		if ( isset($this->sequence[$index]) )
			return $this->_chr(  $this->sequence[$index] );
			
		throw new Zend_Locale_UTF8_Exception( 'Illegal offset('.$index.').' );
	}
	
	/**
	 *  Returns the character (Unicode code point) at the specified index.
	 *
	 * @access public
	 * @param integer $index
	 * @return integer
	 * @throws Zend_Locale_UTF8_Exception
	 */
	public function codePointAt( $index )
	{
		if ( isset($this->sequence[$index]) )
			return $this->sequence[$index];
			
		throw new Zend_Locale_UTF8_Exception( 'Illegal offset('.$index.').' );
	}
	
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
	public function concat( $string )
	{		
		if ( !($string instanceof Zend_Locale_UTF8_PHP5_String) )
			$string = new Zend_Locale_UTF8_PHP5_String($string);

		if ( $string->length() < 1 )
			return $this;
			
		return new Zend_Locale_UTF8_PHP5_String( 
			array_merge(
				$this->getSequence(),
				$string->getSequence()
			)
		);
	}
	
	/**
	 * Returns true if and only if this string contains the specified sequence of char values.
	 *
	 * @param string $string
	 * @return boolean
	 */
	public function contains( $string )
	{
		if ( $this->indexOf($string) >= 0 )
			return 1;
			
		return 0;
	}
	
	/**
	 * Compares this string to another one.
	 *
	 * @access public
	 * @param mixed $string
	 * @return boolean
	 */
	public function equals( $string )
	{
		if ( !($string instanceof Zend_Locale_UTF8_PHP5_String) )
			$string = new Zend_Locale_UTF8_PHP5_String($string);
		
		if ( $this->sequence === $string->getSequence() )
			return true;
			
		return false;
	}
	
	/**
	 * Returns $sequence.
	 * (used to construct new string objects)
	 *
	 * @access public
	 * @return array
	 */
	public function getSequence()
	{
		return $this->sequence;
	}	
	
	/**
	 * Returns the index within this string of the first occurrence of the specified string.
	 *
	 * @access public
	 * @param mixed $string
	 * @return integer
	 */
	public function indexOf( $string )
	{
		return $this->_indexOf($string);
	}
	
	
	/**
	 * Returns the index within this string of the last occurrence of the specified string.
	 *
	 * @access public
	 * @param mixed $string
	 * @return integer
	 */
	public function lastIndexOf( $string )
	{
		if ( !($string instanceof Zend_Locale_UTF8_PHP5_String) )
			$string = new Zend_Locale_UTF8_PHP5_String($string);
				
		$length 	= $string->length();
		$ownLength	= $this->length();
		
		if ( $length < 1 || $length > $ownLength )
			return -1;
			
		if ( $length === 1  ) {
			//search for a single character
			$char 	= $string->codePointAt(0);

			for ( $i = $ownLength-1; $i >= 0; $i-- ) {
				if ( $char === $this->sequence[$i] )
					return $i;
			}
			
		} else {
			
			//search for a string
			$char 	= $string->codePointAt(0);
			
			for ( $i = $ownLength-1; $i >= 0; $i-- ) {
				
				if ( $char === $this->sequence[$i] ) {
					//first character matches
					for ( $j = $length-1; $j >= 0; $j-- ) {
						
						$nextCode = $string->codePointAt($j);
						
						if ( $nextCode !== $this->sequence[$j+$i] )
							break;
						
						if ( $j < 1 )
							return $i;
					}
				}
			}
		}
		
		
		return -1;
	}
	
	/**
	 * Returns the length of this string.
	 *
	 * @access public
	 * @return integer
	 */
	public function length()
	{
		return count( $this->sequence );
	}

	/**
	 * Replace all occurrences of the search string with the replacement string
	 *
	 * @access public
	 * @param mixed $needle
	 * @param mixed $replacement
	 * @return Zend_Locale_UTF8_PHP5_String
	 */
	public function replace( $needle, $replacement )
	{
		$i = 0;
		
		if ( !($needle instanceof Zend_Locale_UTF8_PHP5_String) )
			$needle = new Zend_Locale_UTF8_PHP5_String($needle);
			
		if ( !($replacement instanceof Zend_Locale_UTF8_PHP5_String) )
			$replacement = new Zend_Locale_UTF8_PHP5_String($replacement);
			
		$sequence = $this->getSequence();
		
		while ( ($index = $this->_indexOf($needle, $i)) >= 0 ) {
			$sequence = array_merge(
				array_slice($sequence, 0, $index),
				$replacement->getSequence(),
				array_slice($sequence, $index+$needle->length()+1)
			);
			$i = $index + $needle->length();
		}
		
		return new Zend_Locale_UTF8_PHP5_String($sequence);
	}
	
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
	public function substring($beginIndex, $endIndex=null)
	{
		if ( $beginIndex > 0 && $beginIndex >= $this->length() )
			throw new Zend_Locale_UTF8_Exception('$beginIndex('.$beginIndex.') is out of bounds.');
			
		if ( $endIndex > 0 && $endIndex >= $this->length() )
			throw new Zend_Locale_UTF8_Exception('$endIndex('.$endIndex.') is out of bounds.');
		
		if ( is_null($endIndex) )
			return new Zend_Locale_UTF8_PHP5_String(
				array_slice(
					$this->getSequence(),
					$beginIndex
				)
			);
		return new Zend_Locale_UTF8_PHP5_String(
			array_slice(
				$this->getSequence(),
				$beginIndex,
				($endIndex >= 0) ? $endIndex-$beginIndex : $endIndex
			)
		);
	}
	
	/**
	 * Converts all of the characters in this String to upper case.
	 *
	 * @access public
	 * @return string
	 */
	public function toUpperCase()
	{
		$this->_initCaseFoldingTable();
		
		$sequence = array();
		for ( $i=0; $i<count($this->sequence); $i++ ) {
			$sequence[] = self::$_caseFoldingTable->charToUpper($this->sequence[$i]);
		}
		
		return new Zend_Locale_UTF8_PHP5_String($sequence);
	}
	
	/**
	 * Converts all of the characters in this String to lower case.
	 *
	 * 
	 * @access public
	 * @return string
	 */
	public function toLowerCase()
	{
		$this->_initCaseFoldingTable();
		
		$sequence = array();
		for ( $i=0; $i<count($this->sequence); $i++ ) {
			$sequence[] = self::$_caseFoldingTable->charToLower($this->sequence[$i]);
		}
		
		return new Zend_Locale_UTF8_PHP5_String($sequence);
	}
	
	/**
	 * Returns the character of a URF-8 character code.
	 *
	 * @access protected
	 * @param int $char
	 * @return string
	 */
	protected function _chr( $char )
	{
		if ($char > 0) {
			if ( $char < 0x0080 )
				//1 byte	
				return chr($char);
				
			else if ( $char < 0x0800 ) 
				//2 bytes
				return 	 chr(0xc0  | ($char >> 6))
						.chr(0x80  | ($char & 0x003f));
				
			else if ($char < 0x10000)
				//3 bytes
				return 	 chr(0xe0  | ($char  >> 12))
						.chr(0x80  | (($char >> 6) 	& 0x003f))
						.chr(0x80  | ($char & 0x003f));
        		
			else if ($char < 0x200000)
				//4 bytes
				return 	 chr(0xf0  | ($char  >> 18))
            			.chr(0x80  | (($char >> 12)	& 0x3f))
            			.chr(0x80  | (($char >> 6) 	& 0x3f))
            			.chr(0x80  | ($char & 0x3f));
		}
		
		return '?';
	}
	
	/**
	 * Returns the UTF-8 code of a character.
	 *
	 * @see http://en.wikipedia.org/wiki/UTF-8#Description
	 * @access protected
	 * @param string $string
	 * @param integer $bytes
	 * @param integer $position
	 * @return integer
	 */
	protected function _ord( &$string, $bytes = null, $pos=0 )
	{
		if ( is_null($bytes) )
			$bytes = $this->_characterBytes($string);
		
		if ( strlen($string) >= $bytes ) {
		
			switch ( $bytes ) {
				case 1:
					return ord($string[$pos]);
					break;
					
				case 2:
					return  ( (ord($string[$pos]) 	& 0x1f)	<< 6 ) +
					        ( (ord($string[$pos+1]) & 0x3f) );
					break;
					
				case 3:
					return 	( (ord($string[$pos]) 	& 0xf)	<< 12 ) + 
							( (ord($string[$pos+1]) & 0x3f) << 6 ) +
							( (ord($string[$pos+2]) & 0x3f) );
					break;
					
				case 4:
					return 	( (ord($string[$pos]) 	& 0x7) 	<< 18 ) + 
							( (ord($string[$pos+1]) & 0x3f)	<< 12 ) + 
							( (ord($string[$pos+1]) & 0x3f)	<< 6 ) +
							( (ord($string[$pos+2]) & 0x3f) );
					break;
				
				case 0:
				default:
					return false;
			}
		}
		
		return false;
    }
	
	/**
	 * Returns the UTF-8 code sequence as an array for any given $string.
	 *
	 * @access protected
	 * @param string|integer $string
	 * @return array
	 */
	protected function _decode( $string ) {
		
		$string		= (string) $string;
		$length		= strlen($string);
		$sequence	= array();

		for ( $i=0; $i<$length; ) {
			$bytes		= $this->_characterBytes($string, $i);
			$ord		= $this->_ord($string, $bytes, $i);
			
			if ( $ord !== false )
				$sequence[]	= $ord;
				
			if ( $bytes === false )
				$i++;
			else
				$i	+= $bytes;
		}
		
		return $sequence;
		
	}
	
	/**
	 * Returns the number of bytes of the $position-th character.
	 *
	 * @see http://en.wikipedia.org/wiki/UTF-8#Description
	 * @access protected
	 * @param string $string
	 * @param integer $position
	 */
	protected function _characterBytes( &$string, $position = 0 ) {
		$char 		= $string[$position];
		$charVal 	= ord($char);
		
		if ( ($charVal & 0x80) === 0 )
			return 1;
		
		elseif ( ($charVal & 0xe0) === 0xc0 )
			return 2;
		
		elseif ( ($charVal & 0xf0) === 0xe0 )
			return 3;
			
		elseif ( ($charVal & 0xf8) === 0xf0)
			return 4;
		/*
		elseif ( ($charVal & 0xfe) === 0xf8 )
			return 5;
		*/
			
		return false;
	}
				
	/**
	 * Init Zend_Locale_UTF8_PHP5_CaseFolding which is needed for upper and lower case funtions
	 * 
	 * @access protected
	 */
	protected function _initCaseFoldingTable()
	{
		if ( is_null(self::$_caseFoldingTable) ) {
			require_once 'Zend/Locale/UTF8/PHP5/CaseFolding.php';
			self::$_caseFoldingTable	= new Zend_Locale_UTF8_PHP5_CaseFolding();
		}
	}
	
	protected function _indexOf( &$string, $start=0 )
	{
		if ( !($string instanceof Zend_Locale_UTF8_PHP5_String) )
			$string = new Zend_Locale_UTF8_PHP5_String($string);
				
		$length 	= $string->length();
		$ownLength	= $this->length();
		
		if ( $length < 1 || $length > $ownLength - $start )
			return -1;
			
		if ( $length === 1  ) {
			//search for a single character
			$char 	= $string->codePointAt(0);

			for ( $i=$start; $i < $ownLength; $i++ ) {
				if ( $char === $this->sequence[$i] )
					return $i;
			}
			
		} else {
			
			//search for a string
			$char 	= $string->codePointAt(0);
			
			for ( $i=$start; $i < $ownLength; $i++ ) {
				
				if ( $char === $this->sequence[$i] ) {
					//first character matches
					for ( $j=1; $j < $length; $j++ ) {
						
						$nextCode = $string->codePointAt($j);
						
						if ( $nextCode !== $this->sequence[$j+$i] )
							break;
						
						if ( $j >= $length-1 )
							return $i;
					}
				}
			}
		}
		
		
		return -1;
	}
	
}