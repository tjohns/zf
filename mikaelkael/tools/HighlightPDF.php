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
 * @category   Documentation
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

class HighlightPDF
{

    private static $_filename;

    private static $_dom;

    private static $_lang;

    public static function main($lang)
    {
        if (preg_match('/[a-z]{2}/i', $lang)) {
            self::$_lang = substr(strtolower($lang), -2);
            if (!file_exists("../manual/" . self::$_lang)) {
                echo ("Language '" . self::$_lang . "' doesn't exist\n");
                exit(1);
            }
        } else {
            echo ("Language '" . self::$_lang . "' doesn't valid\n");
            exit(1);
        }
        self::$_filename = "./temp/Zend_Framework_" . self::$_lang . ".fo";
        self::highlightFile();
    }

    public static function highlightFile()
    {
        self::$_dom = new DOMDocument();
        self::$_dom->load(self::$_filename);
        $xpath = new DOMXPath(self::$_dom);
        $elements = $xpath->query("//fo:block[@codehl]");
        foreach ($elements as $block) {
            $code = self::_highlight($block->nodeValue);
            $code_block = self::_createBlockCode($code);
            foreach ($block->childNodes as $node) {
                $block->removeChild($node);
            }
            $block->appendChild($code_block);
            $block->removeAttribute('codehl');
        }
        self::$_dom->save(self::$_filename);

    }

    private static function _highlight($code)
    {
        $code = trim(
                str_replace(array('&amp;' , '&gt;' , '&lt;' , '&quot;'),
                        array('&' , '' , '' , '>' , '<' , '"'),
                        $code));
        if (substr($code, 0, 5) != '<?php') {
            $code = '<?php ' . $code;
            $codehl = highlight_string($code, true);
            $codehl = preg_replace("/\&lt\;\?php\&nbsp\;/", '', $codehl, 1);
        } else {
            $codehl = highlight_string($code, true);
        }
        $codehl = str_replace(array('<code>' , '</code>' , '&nbsp;' , '<br />' , "\r"),
                array('' , '' , ' ' , "\n" , "\n"),
                $codehl);
        $codehl = str_replace(array('&gt;' , '&lt;' , '&'), array('$$$$$' , '£££££' , '&amp;'), $codehl);
        $codehl = str_replace(array('$$$$$' , '£££££'), array('&gt;' , '&lt;'), $codehl);
        $codehl = preg_replace("!\n\n\n+!", "\n\n", $codehl);
        $codehl = trim($codehl);
        return $codehl;
    }

    private static function _createBlockCode($code)
    {
        $dom = new DomDocument();
        $dom->loadXML($code);
        $xpath = new DomXPath($dom);
        $parentSpan = $xpath->query('/span')->item(0);
        $block_code = self::$_dom->createElement('fo:inline');
        $block_code->setAttribute('color', substr($parentSpan->getAttributeNode('style')->value, 7, 7));
        $nodes = $xpath->query('/span/node()');
        foreach ($nodes as $node) {
            if ($node->nodeType == XML_ELEMENT_NODE) {
                $child = self::$_dom->createElement('fo:inline', $node->nodeValue);
                $child->setAttribute('color',
                        substr(
                                $node->getAttributeNode(
                                        'style')->value,
                                7,
                                7));
            } else {
                $child = self::$_dom->importNode($node, true);
            }
            $block_code->appendChild($child);
        }
        if (preg_match("/^\s+$/", $block_code->firstChild->textContent)) {
            $block_code->removeChild($block_code->firstChild);
        }
        return $block_code;
    }
}

HighlightPDF::main($argv[1]);