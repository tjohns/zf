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
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * include needed classes
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Currency/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Currency { 
 
    
    /**
     * constants for enabling and disabling the use of currency Symbols
     */
    const USESYMBOL = 0;
    const NOSYMBOL  = 1;
    
    
    /**
     * constants for enabling and disabling the use of currency Names
     */
    const USENAME  = 2;
    const NONAME   = 4;

    const STANDARD = 8;
    const RIGHT    = 16;
    const LEFT     = 32;
    
    
    /**
     * the locale name of the region that uses the currency
     * 
     * @var string|Zend_Locale
     */
    private $_CurrencyLocale = null ;
    
    
    /**
     * the short name of the currency
     * 
     * @var string
     */
    private $_CurrencyShortName = null ;
    
    
    /**
     * the script name which used to format the outputed numbers
     * 
     * @var string
     */
    private $_NumberScript;
    
    //TODO declare the other needed properties
    
    
    /**
     * Constructor 
     *
     * @param  string              $currency  OPTIONAL currency short name
     * @param  string              $script    OPTIONAL script name
     * @param  string|Zend_Locale  $locale    OPTIONAL locale name
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */ 
    public function __construct($currency = NULL, $script = NULL, $locale = NULL)
    {
        /*
         * supporting flexible parameters
         */
        $params = array(
                      1 => $currency,
                      2 => $locale,
                      3 => $script
                        );
                        
         $currency = $locale = $script = false ;
                        
        foreach ($params as $num => $param){
            
            //get the locale
            if ( (is_object($param) && $param instanceof Zend_Locale) || 
                 (is_string($param) && preg_match('/^[a-z]{2}_[A-Z]{2}$/',$param)) ) {
                
                if (empty($this->_CurrencyLocale)){
                    $this->_setCurrencyLocale($param);
                } else {
                    throw new Zend_Currency_Exception('many locales passed');
                }
            //get the currency short name   
            } else if (is_string($param) && strlen($param) == 3) {
                
                if(empty($this->_CurrencyShortName)) {
                    $this->_setCurrencyShortName($param);
                } else {
                    throw new Zend_Currency_Exception('many currency names passed');
                }
            //get the script name
            } else if (is_string($param) && strlen($param) == 4) {
                
                if (empty($this->_NumberScript)) {
                    $this->_setNumberScript($param);
                } else {
                    throw new Zend_Currency_Exception('many number script names passed');
                }
            //unknown data passed in this param  
            } else if ($param !== false){
                throw new Zend_Currency_Exception('unknown value passed at param #' . $num);
            }
            
        }
        
        
        //make sure that the locale is passed
        if (empty($this->_CurrencyLocale)) {
            throw new Zend_Currency_Exception('you should pass the locale of the currency');
        }
        
        
        //TODO create another method to get the data from Zend_Locale_Data
    }

    
    /**
     * Returns a localized currency string
     *
     * @param  int|float           $value   Currency value
     * @param  string              $script  OPTIONAL Number script to use for output
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for output formatting
     * @return string
     */ 
    public function toCurrency($value, $script = NULL, $locale = NULL) 
    {
        //TODO finish this method
    } 
    
    
    /**
     * Sets the formating options of the localized currency string
     * If no parameter is passed, the standard setting of the
     * actual set locale will be used
     *
     * @param  const|string        $rules   OPTIONAL formating rules for currency
     *                  - SYMBOL|NOSYMBOL : display currency symbol
     *                  - NAME|NONAME     : display currency name
     *                  - DEFAULT|RIGHT|LEFT : where to display currency symbol/name
     *                  - string: gives the currency string/name/sign to set
     * @param  string              $script  OPTIONAL Number script to use for output
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for output formatting
     * @return Zend_Currency
     */
    public function setFormat($rules = NULL, $script = NULL, $locale = NULL)
    {
        //TODO finish this method
    }
    
    
    /**
     * Returns the actual or details of other currency symbols, 
     * when no symbol is avaiable it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param  string              $currency   OPTIONAL Currency name
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale to display informations
     * @return string
     */ 
    public static function getSign($currency, $locale) 
    {
        //TODO finish this method
    } 

 
    /**
     * Returns the actual or details of other currency names
     *
     * @param  string              $currency   OPTIONAL Currency's short name
     * @param  string|Zend_Locale  $locale     OPTIONAL the locale
     * @return string
     */ 
    public static function getName($currency, $locale) 
    {
        //TODO finish this method
    } 


    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param  string  $currency  Currency's short name
     * @return array              List of regions
     */ 
    public static function getRegionList($currency) 
    {
        //TODO finish this method
    } 
    
 
    /**
     * Returns a list of currencies which are used in this region
     * a region name should be 2 charachters only (f.e. EG, DE, US)
     *
     * @param  string  $region  Currency Type
     * @return array            List of currencys
     */ 
    public static function getCurrencyList($region) 
    {
        //TODO finish this method
    }


    /**
     * Returns the actual currency name 
     *
     * @return string
     */ 
    public function toString()
    {
        //TODO finish this method
    }


    /**
     * Returns the currency name 
     *
     * @return string
     */ 
    public function __toString()
    {
        return $this->toString();
    }
 
    
    /**
     * sets the locale of the currency
     * 
     * @param string|Zend_Locale  $locale  the locale
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setCurrencyLocale($locale)
    {
        if (is_object($locale) && $locale instanceof Zend_Locale){
            $this->_CurrencyLocale = $locale ;
        } else if (is_string($locale)) {
            $this->_CurrencyLocale = new Zend_Locale($locale);
        } else {
            throw new Zend_Currency_Exception('invalid locale');
        }
    }

    
    /**
     * sets the short name of the currency
     * 
     * @param string  $currency  currency short name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setCurrencyShortName($currency)
    {
        if (is_string($currency) && strlen($currency) == 3) {
            $this->_CurrencyShortName = strtoupper($currency);
        } else {
            throw new Zend_Currency_Exception('invalid currency short name');
        }
    }
    
    
    /**
     * sets the script name which used for formatting the outputed numbers
     * 
     * @param string $script	script name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setNumberScript($script)
    {
        if (is_string($script) && strlen($script) == 4) {
            $this->_NumberScript = $script ;
        } else {
            throw new Zend_Currency_Exception('invalid script name');
        }
    }
}

/** PROPOSER COMMENT FOR SHREEF (from Thomas):
 * 1.)
 * The serialize/unserialize functions should not be needed
 * The standard PHP useage should be enough
 * 2.)
 * All functions - script and locale are switched.
 * Standard behavior is locale rightest parameter.
 * If not set all params are parsed from right to left, like in Zend_Date
 * Script has to be use the "script" string and not the locale identifier, 
 * so duplication is not given.
 * 3.) I reworked some functions to work as the user expects them, but not complete for now
 * 4.) Please only use SPACES instead of TAB... intend 4 spaces
 * 5.) Please no ending sign after closing a function
 * 6.) Please UTF8 coding
 * 7.) Please code like described in the coding standard... I fixed some issues but not all
 * 
 * More to come after 0.7
 */