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
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Include needed Locale classes
 */
require_once('Zend/Locale/Data.php');


/**
 * @category   Zend
 * @package    Zend_Locale
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Locale {

    // Class wide Locale Constants
    
    // scottish iso-8859-1, windows-1252
    // hebrew   iso-8859-8
    // inuit    iso-8859-10
    // lapp     iso-8859-10
    private $_LocaleData = array(
        'root'  => '',
        'aa_DJ' => '',
        'aa_ER' => '',
        'aa_ET' => '',
        'aa'    => '',
        'af_ZA' => 'iso-8859-1,windows-1252',
        'af'    => 'iso-8859-1,windows-1252',
        'am_ET' => '',
        'am'    => '',
        'ar_AE' => 'iso-8859-6',
        'ar_BH' => 'iso-8859-6',
        'ar_DZ' => 'iso-8859-6',
        'ar_EG' => 'iso-8859-6',
        'ar_IQ' => 'iso-8859-6',
        'ar_JO' => 'iso-8859-6',
        'ar_KW' => 'iso-8859-6',
        'ar_LB' => 'iso-8859-6',
        'ar_LY' => 'iso-8859-6',
        'ar_MA' => 'iso-8859-6',
        'ar_OM' => 'iso-8859-6',
        'ar_QA' => 'iso-8859-6',
        'ar_SA' => 'iso-8859-6',
        'ar_SD' => 'iso-8859-6',
        'ar_SY' => 'iso-8859-6',
        'ar_TN' => 'iso-8859-6',
        'ar_YE' => 'iso-8859-6',
        'ar'    => 'iso-8859-6',
        'as_IN' => '',
        'as'    => '',
        'az_AZ' => '',
        'az'    => '',
        'be_BY' => 'iso-8859-5',
        'be'    => 'iso-8859-5',
        'bg_BG' => 'iso-8859-5',
        'bg'    => 'iso-8859-5',
        'bn_IN' => '',
        'bn'    => '',
        'bs_BA' => '',
        'bs'    => '',
        'byn_ER'=> '',
        'byn'   => '',
        'ca_ES' => 'iso-8859-1,windows-1252',
        'ca'    => 'iso-8859-1,windows-1252',
        'cs_CZ' => 'iso-8859-2',
        'cs'    => 'iso-8859-2',
        'cy_GB' => '',
        'cy'    => '',
        'da_DK' => 'iso-8859-1,windows-1252',
        'da'    => 'iso-8859-1,windows-1252',
        'de_AT' => 'iso-8859-1,windows-1252',
        'de_BE' => 'iso-8859-1,windows-1252',
        'de_CH' => 'iso-8859-1,windows-1252',
        'de_DE' => 'iso-8859-1,windows-1252',
        'de_LI' => 'iso-8859-1,windows-1252',
        'de_LU' => 'iso-8859-1,windows-1252',
        'de'    => 'iso-8859-1,windows-1252',
        'dv_MV' => '',
        'dv'    => '',
        'dz_BT' => '',
        'dz'    => '',
        'el_CY' => 'iso-8859-7',
        'el_GR' => 'iso-8859-7',
        'el'    => 'iso-8859-7',
        'en_AS' => 'iso-8859-1,windows-1252',
        'en_AU' => 'iso-8859-1,windows-1252',
        'en_BE' => 'iso-8859-1,windows-1252',
        'en_BW' => 'iso-8859-1,windows-1252',
        'en_BZ' => 'iso-8859-1,windows-1252',
        'en_CA' => 'iso-8859-1,windows-1252',
        'en_GB' => 'iso-8859-1,windows-1252',
        'en_GU' => 'iso-8859-1,windows-1252',
        'en_HK' => 'iso-8859-1,windows-1252',
        'en_IE' => 'iso-8859-1,windows-1252',
        'en_IN' => 'iso-8859-1,windows-1252',
        'en_JM' => 'iso-8859-1,windows-1252',
        'en_MH' => 'iso-8859-1,windows-1252',
        'en_MP' => 'iso-8859-1,windows-1252',
        'en_MT' => 'iso-8859-1,windows-1252',
        'en_NZ' => 'iso-8859-1,windows-1252',
        'en_PH' => 'iso-8859-1,windows-1252',
        'en_PK' => 'iso-8859-1,windows-1252',
        'en_SG' => 'iso-8859-1,windows-1252',
        'en_TT' => 'iso-8859-1,windows-1252',
        'en_UM' => 'iso-8859-1,windows-1252',
        'en_US' => 'iso-8859-1,windows-1252',
        'en_VI' => 'iso-8859-1,windows-1252',
        'en_ZA' => 'iso-8859-1,windows-1252',
        'en_ZW' => 'iso-8859-1,windows-1252',
        'en'    => 'iso-8859-1,windows-1252',
        'eo'    => 'iso-8859-3',
        'es_AR' => 'iso-8859-1,windows-1252',
        'es_BO' => 'iso-8859-1,windows-1252',
        'es_CL' => 'iso-8859-1,windows-1252',
        'es_CO' => 'iso-8859-1,windows-1252',
        'es_CR' => 'iso-8859-1,windows-1252',
        'es_DO' => 'iso-8859-1,windows-1252',
        'es_EC' => 'iso-8859-1,windows-1252',
        'es_ES' => 'iso-8859-1,windows-1252',
        'es_GT' => 'iso-8859-1,windows-1252',
        'es_HN' => 'iso-8859-1,windows-1252',
        'es_MX' => 'iso-8859-1,windows-1252',
        'es_NI' => 'iso-8859-1,windows-1252',
        'es_PA' => 'iso-8859-1,windows-1252',
        'es_PE' => 'iso-8859-1,windows-1252',
        'es_PR' => 'iso-8859-1,windows-1252',
        'es_PY' => 'iso-8859-1,windows-1252',
        'es_SV' => 'iso-8859-1,windows-1252',
        'es_US' => 'iso-8859-1,windows-1252',
        'es_UY' => 'iso-8859-1,windows-1252',
        'es_VE' => 'iso-8859-1,windows-1252',
        'es'    => 'iso-8859-1,windows-1252',
        'et_EE' => 'iso-8859-15',
        'et'    => 'iso-8859-15',
        'eu_ES' => 'iso-8859-1,windows-1252',
        'eu'    => 'iso-8859-1,windows-1252',
        'fa_AF' => '',
        'fa_IR' => '',
        'fa'    => '',
        'fi_FI' => 'iso-8859-1,windows-1252',
        'fi'    => 'iso-8859-1,windows-1252',
        'fo_FO' => 'iso-8859-1,windows-1252',
        'fo'    => 'iso-8859-1,windows-1252',
        'fr_BE' => 'iso-8859-1,windows-1252',
        'fr_CA' => 'iso-8859-1,windows-1252',
        'fr_CH' => 'iso-8859-1,windows-1252',
        'fr_FR' => 'iso-8859-1,windows-1252',
        'fr_LU' => 'iso-8859-1,windows-1252',
        'fr_MC' => 'iso-8859-1,windows-1252',
        'fr'    => 'iso-8859-1,windows-1252',
        'ga_IE' => 'iso-8859-1,windows-1252',
        'ga'    => 'iso-8859-1,windows-1252',
        'gez_ER'=> '',
        'gez_ET'=> '',
        'gez'   => '',
        'gl_ES' => 'iso-8859-1,windows-1252',
        'gl'    => 'iso-8859-1,windows-1252',
        'gu_IN' => '',
        'gu'    => '',
        'gv_GB' => '',
        'gv'    => '',
        'haw_US'=> '',
        'haw'   => '',
        'he_IL' => '',
        'he'    => '',
        'hi_IN' => '',
        'hi'    => '',
        'hr_HR' => 'iso-8859-2,windows-1250',
        'hr'    => 'iso-8859-2,windows-1250',
        'hu_HU' => 'iso-8859-2',
        'hu'    => 'iso-8859-2',
        'hy_AM' => '',
        'hy'    => '',
        'id_ID' => '',
        'id'    => '',
        'is_IS' => 'iso-8859-1,windows-1252',
        'is'    => 'iso-8859-1,windows-1252',
        'it_CH' => 'iso-8859-1,windows-1252',
        'it_IT' => 'iso-8859-1,windows-1252',
        'it'    => 'iso-8859-1,windows-1252',
        'iu'    => '',
        'ja_JP' => 'shift_jis,iso-2022-jp,euc-jp',
        'ja'    => 'shift_jis,iso-2022-jp,euc-jp',
        'ka_GE' => '',
        'ka'    => '',
        'kk_KZ' => '',
        'kk'    => '',
        'kl_GL' => '',
        'kl'    => '',
        'km_KH' => '',
        'km'    => '',
        'kn_IN' => '',
        'kn'    => '',
        'ko_KR' => 'euc-kr',
        'ko'    => 'euc-kr',
        'kok_IN'=> '',
        'kok'   => '',
        'kw_GB' => '',
        'kw'    => '',
        'ky_KG' => '',
        'ky'    => '',
        'lo_LA' => '',
        'lo'    => '',
        'lt_LT' => 'iso-8859-13,windows-1257',
        'lt'    => 'iso-8859-13,windows-1257',
        'lv_LV' => 'iso-8859-13,windows-1257',
        'lv'    => 'iso-8859-13,windows-1257',
        'mk_MK' => 'iso-8859-5,windows-1251',
        'mk'    => 'iso-8859-5,windows-1251',
        'ml_IN' => '',
        'ml'    => '',
        'mn_MN' => '',
        'mn'    => '',
        'mr_IN' => '',
        'mr'    => '',
        'ms_BN' => '',
        'ms_MY' => '',
        'ms'    => '',
        'mt_MT' => 'iso-8859-3',
        'mt'    => 'iso-8859-3',
        'nb_NO' => '',
        'nb'    => '',
        'nl_BE' => 'iso-8859-1,windows-1252',
        'nl_NL' => 'iso-8859-1,windows-1252',
        'nl'    => 'iso-8859-1,windows-1252', 
        'no_NO' => 'iso-8859-1,windows-1252',
        'no'    => 'iso-8859-1,windows-1252',
        'om_ET' => '',
        'om_KE' => '',
        'om'    => '',
        'or_IN' => '',
        'or'    => '',
        'pa_IN' => '',
        'pa'    => '',
        'pl_PL' => 'iso-8859-2',
        'pl'    => 'iso-8859-2',
        'ps_AF' => '',
        'ps'    => '',
        'pt_BR' => 'iso-8859-1,windows-1252',
        'pt_PT' => 'iso-8859-1,windows-1252',
        'pt'    => 'iso-8859-1,windows-1252',
        'ro_RO' => 'iso-8859-2',
        'ro'    => 'iso-8859-2',
        'ru_RU' => 'koi8-r,iso-8859-5',
        'ru_UA' => 'koi8-r,iso-8859-5',
        'ru'    => 'koi8-r,iso-8859-5',
        'sa_IN' => '',
        'sa'    => '',
        'sh_BA' => '',
        'sh_CS' => '',
        'sh_YU' => '',
        'sh'    => '',
        'sid_ET'=> '',
        'sid'   => '',
        'sk_SK' => 'iso-8859-2',
        'sk'    => 'iso-8859-2',
        'sl_SI' => 'iso-8859-2,windows-1250',
        'sl'    => 'iso-8859-2,windows-1250',
        'so_DJ' => '',
        'so_ET' => '',
        'so_KE' => '',
        'so_SO' => '',
        'so'    => '',
        'sq_AL' => 'iso-8859-1,windows-1252',
        'sq'    => 'iso-8859-1,windows-1252',
        'sr_BA' => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sr_CS' => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sr_YU' => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sr'    => 'windows-1251,iso-8859-5,iso-8859-2,windows-1250',
        'sv_FI' => 'iso-8859-1,windows-1252',
        'sv_SE' => 'iso-8859-1,windows-1252',
        'sv'    => 'iso-8859-1,windows-1252',
        'sw_KE' => '',
        'sw_TZ' => '',
        'sw'    => '',
        'syr_SY'=> '',
        'syr'   => '',
        'ta_IN' => '',
        'ta'    => '',
        'te_IN' => '',
        'te'    => '',
        'th_TH' => '',
        'th'    => '',
        'ti_ER' => '',
        'ti_ET' => '',
        'ti'    => '',
        'tig_ER'=> '',
        'tig'   => '',
        'tr_TR' => 'iso-8859-9,windows-1254',
        'tr'    => 'iso-8859-9,windows-1254',
        'tt_RU' => '',
        'tt'    => '',
        'uk_UA' => 'iso-8859-5',
        'uk'    => 'iso-8859-5',
        'ur_PK' => '',
        'ur'    => '',
        'uz_AF' => '',
        'uz_UZ' => '',
        'uz'    => '',
        'vi_VN' => '',
        'vi'    => '',
        'wal_ET'=> '',
        'wal'   => '',
        'zh_CN' => '',
        'zh_HK' => '',
        'zh_MO' => '',
        'zh_SG' => '',
        'zh_TW' => '',
        'zh'    => ''
    );


    /**
     * Actual set locale 
     */
    private $_Locale;


    /**
     * Generates a locale object
     *
     * @param $locale string   - OPTIONAL locale for parsing input
     * @return object
     */
    public function __construct($locale)
    {
        if (empty($locale))
            $locale = $this->SearchLocale();
        if (!isset($this->_LocaleData[$locale]))
        {
            $region = substr($locale, 0, 3);
            if (($region[2] == '_') or ($region[2] == '-'))
                $region = substr($region, 0, 2);
            if (isset($this->_LocaleData[$region]))
                $this->_Locale = $region;
            else
                $this->_Locale = 'root';
        }
        $this->_Locale = $locale;
    }


    /**
     * Serialization Interface
     */
    public function serialize()
    {
        return serialize($this);
    }


    /**
     * Returns a string representation of the object
     * 
     * @return string
     */
    public function toString()
    {
    }


    /**
     * Returns a string representation of the object
     * Alias for toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }


    /**
     * Search the locale automatically
     * Searchorder is
     * - httpRequestLanguage
     * - Environment
     * - Zend Config
     */
    public function SearchLocale()
    {
        
    }
}