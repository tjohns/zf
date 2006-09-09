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
    private $_LocaleData = array(
        'root'  => '',
        'aa_DJ' => '',
        'aa_ER' => '',
        'aa_ET' => '',
        'aa'    => '',
        'af_ZA' => 'ISO 8859-1',
        'af'    => 'ISO 8859-1',
        'am_ET' => '',
        'am'    => '',
        'ar_AE' => '',
        'ar_BH' => '',
        'ar_DZ' => '',
        'ar_EG' => '',
        'ar_IQ' => '',
        'ar_JO' => '',
        'ar_KW' => '',
        'ar_LB' => '',
        'ar_LY' => '',
        'ar_MA' => '',
        'ar_OM' => '',
        'ar_QA' => '',
        'ar_SA' => '',
        'ar_SD' => '',
        'ar_SY' => '',
        'ar_TN' => '',
        'ar_YE' => '',
        'ar'    => '',
        'as_IN' => '',
        'as'    => '',
        'az_AZ' => '',
        'az'    => '',
        'be_BY' => '',
        'be'    => '',
        'bg_BG' => '',
        'bg'    => '',
        'bn_IN' => '',
        'bn'    => '',
        'bs_BA' => 'ISO 8859-2',
        'bs'    => 'ISO 8859-2',
        'byn_ER'=> '',
        'byn'   => '',
        'ca_ES' => 'ISO 8859-1',
        'ca'    => 'ISO 8859-1',
        'cs_CZ' => 'ISO 8859-2',
        'cs'    => 'ISO 8859-2',
        'cy_GB' => '',
        'cy'    => '',
        'da_DK' => 'ISO 8859-1',
        'da'    => 'ISO 8859-1',
        'de_AT' => 'ISO 8859-1',
        'de_BE' => 'ISO 8859-1',
        'de_CH' => 'ISO 8859-1',
        'de_DE' => 'ISO 8859-1',
        'de_LI' => 'ISO 8859-1',
        'de_LU' => 'ISO 8859-1',
        'de'    => 'ISO 8859-1',
        'dv_MV' => '',
        'dv'    => '',
        'dz_BT' => '',
        'dz'    => '',
        'el_CY' => '',
        'el_GR' => '',
        'el'    => '',
        'en_AS' => 'ISO 8859-1',
        'en_AU' => 'ISO 8859-1',
        'en_BE' => 'ISO 8859-1',
        'en_BW' => 'ISO 8859-1',
        'en_BZ' => 'ISO 8859-1',
        'en_CA' => 'ISO 8859-1',
        'en_GB' => 'ISO 8859-1',
        'en_GU' => 'ISO 8859-1',
        'en_HK' => 'ISO 8859-1',
        'en_IE' => 'ISO 8859-1',
        'en_IN' => 'ISO 8859-1',
        'en_JM' => 'ISO 8859-1',
        'en_MH' => 'ISO 8859-1',
        'en_MP' => 'ISO 8859-1',
        'en_MT' => 'ISO 8859-1',
        'en_NZ' => 'ISO 8859-1',
        'en_PH' => 'ISO 8859-1',
        'en_PK' => 'ISO 8859-1',
        'en_SG' => 'ISO 8859-1',
        'en_TT' => 'ISO 8859-1',
        'en_UM' => 'ISO 8859-1',
        'en_US' => 'ISO 8859-1',
        'en_VI' => 'ISO 8859-1',
        'en_ZA' => 'ISO 8859-1',
        'en_ZW' => 'ISO 8859-1',
        'en'    => 'ISO 8859-1',
        'eo'    => 'ISO 8859-3',
        'es_AR' => 'ISO 8859-1',
        'es_BO' => 'ISO 8859-1',
        'es_CL' => 'ISO 8859-1',
        'es_CO' => 'ISO 8859-1',
        'es_CR' => 'ISO 8859-1',
        'es_DO' => 'ISO 8859-1',
        'es_EC' => 'ISO 8859-1',
        'es_ES' => 'ISO 8859-1',
        'es_GT' => 'ISO 8859-1',
        'es_HN' => 'ISO 8859-1',
        'es_MX' => 'ISO 8859-1',
        'es_NI' => 'ISO 8859-1',
        'es_PA' => 'ISO 8859-1',
        'es_PE' => 'ISO 8859-1',
        'es_PR' => 'ISO 8859-1',
        'es_PY' => 'ISO 8859-1',
        'es_SV' => 'ISO 8859-1',
        'es_US' => 'ISO 8859-1',
        'es_UY' => 'ISO 8859-1',
        'es_VE' => 'ISO 8859-1',
        'es'    => 'ISO 8859-1',
        'et_EE' => 'ISO 8859-15,ISO 8859-1',
        'et'    => 'ISO 8859-15,ISO 8859-1',
        'eu_ES' => 'ISO 8859-1',
        'eu'    => 'ISO 8859-1',
        'fa_AF' => '',
        'fa_IR' => '',
        'fa'    => '',
        'fi_FI' => 'ISO 8859-15,ISO 8859-1',
        'fi'    => 'ISO 8859-15,ISO 8859-1',
        'fo_FO' => 'ISO 8859-1',
        'fo'    => 'ISO 8859-1',
        'fr_BE' => 'ISO 8859-15,ISO 8859-1',
        'fr_CA' => 'ISO 8859-15,ISO 8859-1',
        'fr_CH' => 'ISO 8859-15,ISO 8859-1',
        'fr_FR' => 'ISO 8859-15,ISO 8859-1',
        'fr_LU' => 'ISO 8859-15,ISO 8859-1',
        'fr_MC' => 'ISO 8859-15,ISO 8859-1',
        'fr'    => 'ISO 8859-15,ISO 8859-1',
        'ga_IE' => 'ISO 8859-1',
        'ga'    => 'ISO 8859-1',
        'gez_ER'=> '',
        'gez_ET'=> '',
        'gez'   => '',
        'gl_ES' => 'ISO 8859-1',
        'gl'    => 'ISO 8859-1',
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
        'hr_HR' => 'ISO 8859-2',
        'hr'    => 'ISO 8859-2',
        'hu_HU' => 'ISO 8859-2',
        'hu'    => 'ISO 8859-2',
        'hy_AM' => '',
        'hy'    => '',
        'id_ID' => '',
        'id'    => '',
        'is_IS' => 'ISO 8859-1',
        'is'    => 'ISO 8859-1',
        'it_CH' => 'ISO 8859-1',
        'it_IT' => 'ISO 8859-1',
        'it'    => 'ISO 8859-1',
        'iu'    => '',
        'ja_JP' => '',
        'ja'    => '',
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
        'ko_KR' => '',
        'ko'    => '',
        'kok_IN'=> '',
        'kok'   => '',
        'kw_GB' => '',
        'kw'    => '',
        'ky_KG' => '',
        'ky'    => '',
        'lo_LA' => '',
        'lo'    => '',
        'lt_LT' => '',
        'lt'    => '',
        'lv_LV' => '',
        'lv'    => '',
        'mk_MK' => '',
        'mk'    => '',
        'ml_IN' => '',
        'ml'    => '',
        'mn_MN' => '',
        'mn'    => '',
        'mr_IN' => '',
        'mr'    => '',
        'ms_BN' => '',
        'ms_MY' => '',
        'ms'    => '',
        'mt_MT' => 'ISO 8859-3',
        'mt'    => 'ISO 8859-3',
        'nb_NO' => 'ISO 8859-1',
        'nb'    => 'ISO 8859-1',
        'nl_BE' => 'ISO 8859-1',
        'nl_NL' => 'ISO 8859-1',
        'nl'    => 'ISO 8859-1', 
        'nn_NO' => 'ISO 8859-1',
        'nn'    => 'ISO 8859-1',
        'om_ET' => '',
        'om_KE' => '',
        'om'    => '',
        'or_IN' => '',
        'or'    => '',
        'pa_IN' => '',
        'pa'    => '',
        'pl_PL' => 'ISO 8859-2',
        'pl'    => 'ISO 8859-2',
        'ps_AF' => '',
        'ps'    => '',
        'pt_BR' => 'ISO 8859-1',
        'pt_PT' => 'ISO 8859-1',
        'pt'    => 'ISO 8859-1',
        'ro_RO' => 'ISO 8859-2',
        'ro'    => 'ISO 8859-2',
        'ru_RU' => '',
        'ru_UA' => '',
        'ru'    => '',
        'sa_IN' => '',
        'sa'    => '',
        'sh_BA' => 'ISO 8859-2',
        'sh_CS' => 'ISO 8859-2',
        'sh_YU' => 'ISO 8859-2',
        'sh'    => 'ISO 8859-2',
        'sid_ET'=> '',
        'sid'   => '',
        'sk_SK' => 'ISO 8859-2',
        'sk'    => 'ISO 8859-2',
        'sl_SI' => 'ISO 8859-2',
        'sl'    => 'ISO 8859-2',
        'so_DJ' => '',
        'so_ET' => '',
        'so_KE' => '',
        'so_SO' => '',
        'so'    => '',
        'sq_AL' => 'ISO 8859-1',
        'sq'    => 'ISO 8859-1',
        'sr_BA' => 'ISO 8859-2',
        'sr_CS' => 'ISO 8859-2',
        'sr_YU' => 'ISO 8859-2',
        'sr'    => 'ISO 8859-2',
        'sv_FI' => 'ISO 8859-1',
        'sv_SE' => 'ISO 8859-1',
        'sv'    => 'ISO 8859-1',
        'sw_KE' => 'ISO 8859-1',
        'sw_TZ' => 'ISO 8859-1',
        'sw'    => 'ISO 8859-1',
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
        'tr_TR' => 'ISO 8859-3',
        'tr'    => 'ISO 8859-3',
        'tt_RU' => '',
        'tt'    => '',
        'uk_UA' => '',
        'uk'    => '',
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