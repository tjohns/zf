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

class ManualXML
{

    private static $_file;

    public static function main()
    {
        if (file_exists('./temp/manual.xml') && is_writable('./temp/manual.xml')) {
            self::$_file = file_get_contents('./temp/manual.xml');
            self::_insertZfVersion();
            self::_insertPublisher();
            self::_insertSvnRevision();
            self::_removeIndexLink();
            file_put_contents('./temp/manual.xml', self::$_file);
        }
    }

    private static function _insertPublisher()
    {
        if (file_exists('./temp/svn_rev') && is_readable('./temp/svn_rev')) {
            $svn_rev = trim(file_get_contents('./temp/svn_rev'));
            $link = "by <ulink url=\"http://www.mikaelkael.fr/zf-chm-pdf\">mikaelkael</ulink>";
            self::$_file = str_replace('</pubdate>', " $link</pubdate>", self::$_file);
        } else {
            echo "  Impossible to read svn revision\n";
        }
    }

    private static function _insertSvnRevision()
    {
        if (file_exists('./temp/svn_rev') && is_readable('./temp/svn_rev')) {
            $svn_rev = trim(file_get_contents('./temp/svn_rev'));
            $link = "<ulink url=\"http://framework.zend.com/code/browse/Zend_Framework/standard/trunk/\">SVN $svn_rev</ulink>";
            self::$_file = str_replace('</pubdate>', " ($link)</pubdate>", self::$_file);
            echo "  SVN: $svn_rev\n";
        } else {
            echo "  Impossible to read svn revision\n";
        }
    }

    private static function _insertZfVersion()
    {
        if (file_exists('../../library/Zend/Version.php') && is_readable('../../library/Zend/Version.php')) {
            require_once '../../library/Zend/Version.php';
            $version = implode('.', array_slice(explode('.', Zend_Version::VERSION), 0, 2)) . '.x';
            self::$_file = str_replace('</subtitle>', " $version</subtitle>", self::$_file);
            echo "  ZF version: $version\n";
        } else {
            echo "  Impossible to read zf version\n";
        }
    }
    
    private static function _removeIndexLink()
    {
        self::$_file = str_replace('<index id="the.index"/>', '', self::$_file);
    }
}

ManualXML::main();