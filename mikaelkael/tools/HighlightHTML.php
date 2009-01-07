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

class HTML_Filter extends FilterIterator
{

    public function accept()
    {
        return (substr($this->current(), -5) == '.html');
    }
}

class HighlightHTML
{

    private static $_lang = 'en';

    public static function main($lang, $listLang = array())
    {
        if (preg_match('/(html|htmlhelp|website)\/[a-z]{2}/i', $lang)) {
            self::$_lang = strtolower($lang);
            if (!file_exists('../manual/' . substr(self::$_lang, -2))) {
                die("Language '$lang' doesn't exist");
            }
        } else {
            die("Language '$lang' doesn't valid");
        }
        self::highlightAllFile();
        self::insertImageLink();
    }

    public static function highlightAllFile()
    {
        $dir = new DirectoryIterator('./output/' . self::$_lang);
        $filter = new HTML_Filter($dir);
        foreach ($filter as $file) {
            $text = file_get_contents($file->getPathName());
            $text = preg_replace_callback('/(<pre class="programlisting">)(.*?)(<\/pre>)/s',
                    'HighlightHTML::_highlight',
                    $text);
            file_put_contents($file->getPathName(), $text);
        }
    }

    public static function insertImageLink()
    {
        $file_name = './output/' . self::$_lang . '/index.html';
        $index = file_get_contents($file_name);
        $images = '<img src="images/bkg_top.png" height="1" />' . PHP_EOL;
        $images .= '<img src="images/logo_small.png" height="1" width="1" />' . PHP_EOL;
        $images .= '<img src="images/draft.png" height="1" width="1" />' . PHP_EOL;
        $index = str_replace('</body>', $images . '</body>', $index);
        file_put_contents($file_name, $index);
    }

    private static function _highlight($text)
    {
        $code = $text[2];
        // Replace element create by html compilation
        $code = str_replace(array('&gt;' , '&lt;'), array('>' , '<'), $code);
        // Highlight with tag < ?php since they are not present
        $code = highlight_string('<?php ' . $code, true);
        // After highlight remove equivalent to < ?php but just the first one
        $code = preg_replace(
                "`\<span style\=\"color\: \#0000BB\"\>\&lt\;\?php\&nbsp\;<br />`",
                '<span style="color: #0000BB">',
                $code,
                1);
        // Since we are in a tag <code> all \n will create a new line,
        // but we already have some <br/>:
        $code = str_replace("\n", '', $code);
        // In English manual, there is spaces between ]]> and </programlisting>,
        // we remode it:
        $code = preg_replace(
                '/(<br \/>)(\&nbsp\;)*(<\/span><\/code>)/',
                '$3',
                $code);
        $code = preg_replace('/(<br \/>)(\&nbsp\;)*(<\/span><\/span><\/code>)/', '$3', $code);
        return $text[1] . $code . $text[3];
    }
}

HighlightHTML::main($argv[1]);