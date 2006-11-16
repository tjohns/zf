<?php
/**
 * This example shows how to get the language for all
 * languages written in native letters
 * 
 * So en = english de = deutsch da = dánsk and so on
 */
require_once 'Zend.php';
Zend::loadClass('Zend_Locale');

$locale = new Zend_Locale();
$list = $locale->getLanguageList();
unset($list['no']);

foreach($list as $language => $content) {
    $lang = new Zend_Locale($language);
    print "\n<br>[".$language."] ".$lang->getLanguageDisplay($language);
}