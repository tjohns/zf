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
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id: $
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/** Zend_Locale */
require_once 'Zend/Locale.php';

/**
 * @category   Zend
 * @package    Zend_Translate
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Translate_Plural {

    /**
     * Returns the plural version to use for each language
     *
     * @param $plural  Plural to check the version for
     * @param $locale  Locale to search the rule for
     * @return string  Evaluation method for the pluralform
     */
    public static function getPlural($plural, $locale) {
        if (substr($locale, 0, 5) == "pt_BR") {
            // temporary set a locale for brasilian
            $locale = "xbr";
        }
        if (strlen($locale) > 3) {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
        }

        switch($locale) {
            case 'bo':
            case 'dz':
            case 'id':
            case 'ja':
            case 'jv':
            case 'ka':
            case 'km':
            case 'kn':
            case 'ko':
            case 'ms':
            case 'th':
            case 'tr':
            case 'vi':
                return 0;
                break;

            case 'af':
            case 'az':
            case 'bn':
            case 'bg':
            case 'ca':
            case 'da':
            case 'de':
            case 'el':
            case 'en':
            case 'eo':
            case 'es':
            case 'et':
            case 'eu':
            case 'fa':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
            case 'gl':
            case 'gu':
            case 'ha':
            case 'he':
            case 'hu':
            case 'is':
            case 'it':
            case 'ku':
            case 'lb':
            case 'ml':
            case 'mn':
            case 'mr':
            case 'nah':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'om':
            case 'or':
            case 'pa':
            case 'pap':
            case 'ps':
            case 'pt':
            case 'so':
            case 'sq':
            case 'sv':
            case 'sw':
            case 'ta':
            case 'te':
            case 'tk':
            case 'ur':
            case 'zh':
            case 'zu':
                return ($plural == 1) ? 1 : 0;

            case 'am':
            case 'bh':
            case 'fil':
            case 'fr':
            case 'gun':
            case 'hi':
            case 'ln':
            case 'mg':
            case 'nso':
            case 'xbr':
            case 'ti':
            case 'wa':
                return (($plural == 0) || ($plural == 1)) ? 1 : 0;

            case 'be':
            case 'bs':
            case 'hr':
            case 'ru':
            case 'sr':
            case 'uk':
                return (($plural % 10 == 1) && ($plural % 100 != 11)) ? 1 : (($plural % 10 >= 2) && ($plural % 10 <= 4) && (($plural % 100 < 10) || ($plural % 100 >= 20))) ? 2 : 0;

            case 'cs':
            case 'sk':
                return ($plural == 1) ? 1 : (($plural >= 2) && ($plural <= 4)) ? 2 : 0;

            case 'ga':
                return ($plural == 1) ? 1 : ($plural == 2) ? 2 : 0;

            case 'lt':
                return (($plural % 10 == 1) && ($plural % 100 != 11)) ? 1 : (($plural % 10 >= 2) && (($plural % 100 < 10) || ($plural % 100 >= 20))) ? 2 : 0;

            case 'sl':
                return ($plural % 100 == 1) ? 1 : ($plural % 100 == 2) ? 2 : (($plural % 100 == 3) || ($plural % 100 == 4)) ? 3 : 0;

            case 'mk':
                return ($plural % 10 == 1) ? 1 : 0;

            case 'mt':
                return ($plural == 1) ? 1 : (($plural == 0) || (($plural % 100 > 1) && ($plural % 100 < 11))) ? 2 : (($plural % 100 > 10) && ($plural % 100 < 20)) ? 3 : 0;

            case 'lv':
                return ($plural == 0) ? 1 : (($plural % 10 == 1) && ($plural % 100 != 11)) ? 2 : 0;

            case 'pl':
                return ($plural == 1) ? 1 : (($plural % 10 >= 2) && ($plural % 10 <= 4) && (($plural % 100 < 10) || ($plural % 100 > 29))) ? 2 : 0;

            case 'cy':
                return ($plural == 1) ? 1 : ($plural == 2) ? 2 : (($plural == 8) || ($plural == 11)) ? 3 : 0;

            case 'ro':
                return ($plural == 1) ? 1 : (($plural == 0) || (($plural % 100 > 0) && ($plural % 100 < 20))) ? 2 : 0;

            case 'ar':
                return ($plural == 0) ? 1 : ($plural == 1) ? 2 : ($plural == 2) ? 3 : (($plural >= 3) && ($plural <= 10)) ? 4 : (($plural >= 11) && ($plural <= 99)) ? 5 : 0;

            default:
                return null;
        }
    }
}
