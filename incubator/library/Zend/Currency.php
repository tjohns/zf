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
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * include needed classes
 */
require_once 'Zend/Locale.php';
require_once 'Zend/Locale/Data.php';
require_once 'Zend/Locale/Format.php';
require_once 'Zend/Currency/Exception.php';


/**
 * @category   Zend
 * @package    Zend_Currency
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Currency {

    /**
     * constants for enabling and disabling the use of currency Symbols
     */
    const NO_SYMBOL     = 0;
    const USE_SYMBOL    = 1;
    const USE_SHORTNAME = 2;
    const USE_NAME      = 4;

    /**
     * constants for enabling and disabling the use of currency Names
     */
    const STANDARD = 8;
    const RIGHT    = 16;
    const LEFT     = 32;

    /**
     * the locale name of the region that uses the currency
     *
     * @var string
     */
    private $_currencyLocale = null;

    /**
     * the short name of the currency
     *
     * @var string
     */
    private $_shortName = null;

    /**
     * the full name of the currency
     *
     * @var string
     */
    private $_fullName = null;

    /**
     * the symbol of the currency
     *
     * @var string
     */
    private $_symbol = null;

    /**
     * the position of the symbol
     *
     * @var const
     */
    private $_signPosition = null;

    /**
     * the locale of the symbol
     *
     * @var string
     */
    private $_symbolLocale = null;

    /**
     * the script name which used to format the outputed numbers
     *
     * @var string
     */
    private $_numberScript = null;

    /**
     * the locale for formating the output
     *
     * @var string
     */
    private $_formatLocale = null;

    /**
     * use symbols or not
     *
     * @var bool
     */
    private $_useSymbol = true;

    /**
     * use currency names or not
     *
     * @var bool
     */
    private $_useName = true;

    /**
     * Creates a currency instance. Every supressed parameter is used from the actual or the given locale.
     *
     * @param  string              $currency  OPTIONAL currency short name
     * @param  string              $script    OPTIONAL script name
     * @param  string|Zend_Locale  $locale    OPTIONAL locale name
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    public function __construct($currency = null, $script = null, $locale = null)
    {
         // supporting flexible parameters
        $params = array(1 => $currency, 2 => $locale, 3 => $script);

        $currency = $locale = $script = false ;
        foreach ($params as $num => $param){
            // get the locale
            if ($locale = Zend_Locale::isLocale($param) and (strlen($locale) > 4)) {
                if (empty($this->_currencyLocale)){
                    $this->_setCurrencyLocale($locale);
                } else {
                    throw new Zend_Currency_Exception("Multiple locales passed. Only one locale allowed");
                }
            // get the currency short name
            } else if (is_string($param) && strlen($param) == 3) {

                if(empty($this->_currencyShortName)) {
                    $this->_setCurrencyShortName($param);
                } else {
                    throw new Zend_Currency_Exception("Multiple currencies passed. Only one currency allowed");
                }
            // get the script name
            } else if (is_string($param) && (strlen($param) == 4)) {

                if (empty($this->_numberScript)) {
                    $this->_setNumberScript($param);
                } else {
                    throw new Zend_Currency_Exception("Multiple number script names passed. Only one script name allowed");
                }
            // unknown data passed in this param
            } else if ($param !== null){
                throw new Zend_Currency_Exception("Unknown locale '$param' passed with param #$num, locale must include the region");
            }
        }

        // if no locale is passed, use standard locale
        if (empty($this->_currencyLocale)) {
            $locale = new Zend_Locale();
            $this->_setCurrencyLocale($locale->toString());
        }

        //getting the data related to this currency
        $this->_updateFullName()
             ->_updateShortName()
             ->_updateSymbol()
             ->_updateFormat();
        return $this;
    }


    /**
     * Gets the short name of the currency from Zend_Locale
     *
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateShortName()
    {
        $locale = $this->_currencyLocale;

        //getting the short name of the currency
        $data = Zend_Locale_Data::getContent('', 'currencyforregion', substr($locale, strpos($locale, '_') + 1));
        if (!empty($this->_shortName)) {
            if (!isset($data[$this->_shortName])) {
                $this->_shortName = key($data);
            }
        } else {
            $this->_shortName = key($data);
        }

        return $this;
    }


    /**
     * Gets the full name of the currency from Zend_Locale
     *
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateFullName()
    {
        $locale = $this->_currencyLocale;

        if(empty($this->_symbolLocale)) {
            $this->_symbolLocale = $this->_currencyLocale;
        }

        //getting the full name of the currency
        $names = Zend_Locale_Data::getContent($this->_symbolLocale, 'currencynames', substr($locale, strpos($locale, '_') + 1) );
        $this->_fullName = isset($names[$this->_shortName]) ? $names[$this->_shortName] : $this->_shortName;

        return $this;
    }


    /**
     * Gets the symbol of the currency from Zend_Locale
     *
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateSymbol()
    {
        $formatLocale = $this->_symbolLocale;

        if (empty($formatLocale)) {
            $this->_symbolLocale = $this->_currencyLocale;
            $formatLocale = $this->_symbolLocale;
        }

        //getting the symbol of the currency
        $symbols = Zend_Locale_Data::getContent($formatLocale, 'currencysymbols');
        $this->_symbol = isset($symbols[$this->_shortName])?$symbols[$this->_shortName]:null;

        return $this;
    }


    /**
     * Gets the information required for formating the currency from Zend_Locale
     *
     * @return Zend_Currency
     * @throws Zend_Currency_Exception
     */
    protected function _updateFormat()
    {
        $formatLocale = $this->_formatLocale;

        if (empty($formatLocale)) {
            $this->_formatLocale = $this->_currencyLocale;
            $formatLocale = $this->_formatLocale;
        }

        //getting the format information of the currency
        $format = Zend_Locale_Data::getContent($formatLocale, 'currencyformat');
        $format = $format['default'];

        iconv_set_encoding('internal_encoding', 'UTF-8');
        if (iconv_strpos($format, ';')) {
            $format = iconv_substr($format, 0, iconv_strpos($format, ';'));
        }

        //knowing the sign positioning information
        if (iconv_strpos($format, '¤') == 0) {
            $this->_signPosition = self::LEFT;
        } else if (iconv_strpos($format, '¤') == iconv_strlen($format)-1) {
            $this->_signPosition = self::RIGHT;
        }

        return $this;
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
        //validate the passed number
        if (!isset($value) || !is_numeric($value)) {
            throw new Zend_Currency_Exception("Value '$value' must be an number");
        }

        //format the number
        if (!empty($locale)) {
            $value = Zend_Locale_Format::toNumber($value, array('locale' => $locale));
        } else {
            $value = Zend_Locale_Format::toNumber($value, array('locale' => $this->_formatLocale));
        }

        //localize the number digits
        if (!empty($script)) {
            $value = Zend_Locale_Format::convertNumerals($value, 'Latn', $script);
        } else if (!empty($this->_numberScript)) {
            $value = Zend_Locale_Format::convertNumerals($value, 'Latn', $this->_numberScript);
        }
print "\n1:".(int) $this->_useSymbol.":".$this->_symbol;
print "\n2:".(int) $this->_useName.":".$this->_fullName.":".$this->_shortName;
        //get the sign to be placed next to the number
        $sign = '';
        if ($this->_useSymbol && !empty($this->_symbol)) {
            $sign = $this->_symbol;
        } else if ($this->_useName) {
            $sign = $this->_fullName;
        } else {
            $sign = $this->_shortName;
        }

        //place the sign next to the number
        if ($this->_signPosition == self::RIGHT) {
            $value = $value . ' ' . $sign;
        } else if ($this->_signPosition == self::LEFT) {
            $value = $sign . ' ' . $value;
        }

        return $value;
    }


    /**
     * Sets the formating options of the localized currency string
     * If no parameter is passed, the standard setting of the
     * actual set locale will be used
     *
     * @param  const|string        $rules   OPTIONAL formating rules for currency
     *                  - USE_SYMBOL|NOSYMBOL : display currency symbol
     *                  - USE_NAME|NONAME     : display currency name
     *                  - STANDARD|RIGHT|LEFT : where to display currency symbol/name
     *                  - string: gives the currency string/name/sign to set
     * @param  string              $script  OPTIONAL Number script to use for output
     * @param  string|Zend_Locale  $locale  OPTIONAL Locale for output formatting
     * @return Zend_Currency
     */
    public function setFormat($rules = null, $script = null, $locale = null)
    {
        //process the rules
        if ($rules === self::USE_SYMBOL || $rules === self::NO_SYMBOL)    {
            $this->_useSymbol = ($rules == self::USE_SYMBOL) ? true : false ;
        } else if ($rules === self::USE_NAME || $rules === self::USE_SHORTNAME) {
            $this->_useName   = ($rules == self::USE_NAME)   ? true : false;
        } else if ($rules === self::RIGHT    || $rules === self::LEFT)    {
            $this->_signPosition = $rules;
        } else if ($rules === self::STANDARD) {
            $this->_updateFormat();
        }

        //set the new number script
        if (!empty($script)) {
            $this->_setNumberScript($script);
        }

        //set the locale for the number formating process
        if (!empty($locale)) {
            if ($locale = Zend_Locale::isLocale($locale) and (strlen($locale) > 4)) {
                $this->_formatLocale = $locale;
            } else {
                throw new Zend_Currency_Exception("Locale '$locale' is no valid locale");
            }
        }
        return $this;
    }


    /**
     * Returns the actual or details of other currency symbols,
     * when no symbol is avaiable it returns the currency shortname (f.e. FIM for Finnian Mark)
     *
     * @param  string              $currency   OPTIONAL Currency name
     * @param  string|Zend_Locale  $locale     OPTIONAL Locale to display informations
     * @return string
     */
    public static function getSymbol($currency = null, $locale = null)
    {
        //manage the params
        if (empty($locale) && !empty($currency)) {
            $locale = $currency;
            $currency = null;
        } else if (empty($locale) && empty($currency)) {
            throw new Zend_Currency_Exception('you should pass a locale');
        }

        //validate the locale and get the country short name
        $country = null;
        if ($locale = Zend_Locale::isLocale($locale) and (strlen($locale) > 4)) {
            $country = substr($locale, strpos($locale, '_')+1 );
        } else {
            throw new Zend_Currency_Exception('pass a valid locale');
        }

        //get the available currencies for this country
        $data = Zend_Locale_Data::getContent($locale, 'currencyforregion', $country);
        if (!empty($currency)) {
            if (isset($data[$currency])) {
                $shortName = $currency;
            } else {
                return key($data);
            }
        } else {
            $shortName = key($data);
        }

        //get the symbol
        $symbols = Zend_Locale_Data::getContent($locale, 'currencysymbols');

        return isset($symbols[$shortName]) ? $symbols[$shortName] : $shortName;
    }


    /**
     * Returns the actual or details of other currency shortnames
     *
     * @param  string              $currency   OPTIONAL Currency's short name
     * @param  string|Zend_Locale  $locale     OPTIONAL the locale
     * @return string
     */
    public static function getShortName($currency = null, $locale = null)
    {
        //manage the params
        if (empty($locale) && !empty($currency)) {
            $locale = $currency;
            $currency = null;
        } else if (empty($locale) && empty($currency)) {
            throw new Zend_Currency_Exception('you should pass a locale');
        }

        //validate the locale and get the country short name
        $country = null;
        if ($locale = Zend_Locale::isLocale($locale) and (strlen($locale) > 4)) {
            $country = substr($locale, strpos($locale, '_') + 1 );
        } else {
            throw new Zend_Currency_Exception('pass a valid locale');
        }

        //get the available currencies for this country
        $data = Zend_Locale_Data::getContent($locale,'currencyforregion',$country);
        if (!empty($currency)) {
            if (isset($data[$currency])) {
                $shortName = $currency;
            } else {
                return key($data);
            }
        } else {
            $shortName = key($data);
        }

        //get the name
        $names = Zend_Locale_Data::getContent($locale, 'currencynames', $country);

        return isset($names[$shortName]) ? $names[$shortName] : $shortName;
    }


    /**
     * Returns the actual or details of other currency names
     *
     * @param  string              $currency   OPTIONAL Currency's short name
     * @param  string|Zend_Locale  $locale     OPTIONAL the locale
     * @return string
     */
    public static function getName($currency = null, $locale = null)
    {
        //manage the params
        if (empty($locale) && !empty($currency)) {
            $locale = $currency;
            $currency = null;
        } else if (empty($locale) && empty($currency)) {
            throw new Zend_Currency_Exception('you should pass a locale');
        }

        //validate the locale and get the country short name
        $country = null;
        if ($locale = Zend_Locale::isLocale($locale) and (strlen($locale) > 4)) {
            $country = substr($locale, strpos($locale, '_') + 1 );
        } else {
            throw new Zend_Currency_Exception('pass a valid locale');
        }

        //get the available currencies for this country
        $data = Zend_Locale_Data::getContent($locale,'currencyforregion',$country);
        if (!empty($currency)) {
            if (isset($data[$currency])) {
                $shortName = $currency;
            } else {
                return key($data);
            }
        } else {
            $shortName = key($data);
        }

        //get the name
        $names = Zend_Locale_Data::getContent($locale, 'currencynames', $country);

        return isset($names[$shortName]) ? $names[$shortName] : $shortName;
    }


    /**
     * Returns a list of regions where this currency is or was known
     *
     * @param  string  $currency  Currency's short name
     * @return array              List of regions
     */
    public static function getRegionList($currency)
    {
        $data = Zend_Locale_Data::getContent('', 'currencyforregionlist');
        $regionList = array();

        foreach($data as $region => $currencyShortName) {
            if ($currencyShortName == $currency) {
                $regionList[] = $region;
            }
        }

        return $regionList;
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
        return Zend_Locale_Data::getContent('', 'currencyforregion', $region);
    }


    /**
     * Returns the actual currency name
     *
     * @return string
     */
    public function toString()
    {
        if (!empty($this->_fullName)) {
            return $this->_fullName;
        } else {
            return $this->shortName;
        }
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
        if ($locale = Zend_Locale::isLocale($locale) and (strlen($locale) > 4)) {
            $this->_currencyLocale = $locale ;
        } else {
            throw new Zend_Currency_Exception("Given locale '$locale' is no valid locale");
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
            $this->_currencyShortName = strtoupper($currency);
        } else {
            throw new Zend_Currency_Exception("Given currency short name '$currency' is no valid short name");
        }
    }


    /**
     * sets the script name which used for formatting the outputed numbers
     *
     * @param string $script    script name
     * @return void
     * @throws Zend_Currency_Exception
     */
    private function _setNumberScript($script)
    {
        try {
            Zend_Locale_Format::convertNumerals(0,$script);
            $this->_numberScript = $script;
        } catch (Zend_Locale_Exception $e) {
            throw new Zend_Currency_Exception($e->getMessage());
        }
    }
}
