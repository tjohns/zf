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
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * This example shows how to get the language for all
 * languages written in native letters
 * 
 * So en = english de = deutsch da = dánsk and so on
 */
require_once 'Zend/Locale.php';

$locale = new Zend_Locale();
$list = $locale->getLanguageList();
unset($list['no']);

foreach($list as $language => $content) {
    $lang = new Zend_Locale($language);
    print "\n<br>[".$language."] ".$lang->getLanguageDisplay($language);
}
