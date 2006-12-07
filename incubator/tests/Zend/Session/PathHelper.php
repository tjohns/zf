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
 * @package    Zend_Session_PathHelper
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Session.php 2060 2006-12-02 19:41:07Z gavin $
 * @since      Preview Release 0.2
 */

class Zend_Session_PathHelper
{

    protected static $pathCwd;

    protected static $pathIncubator;

    protected static $pathLibrary;
    
    protected static $pathIncubatorLibrary;

    protected static $pathIncubatorTests;

    public static function setIncludePath()
    {

        self::$pathCwd = dirname(__FILE__);

        self::$pathIncubator = dirname(dirname(dirname(self::$pathCwd)));

        self::$pathLibrary = dirname(self::$pathIncubator) . DIRECTORY_SEPARATOR . 'library';
    
        self::$pathIncubatorLibrary = self::$pathIncubator . DIRECTORY_SEPARATOR . 'library';

        self::$pathIncubatorTests = self::$pathIncubator . DIRECTORY_SEPARATOR . 'tests';

        set_include_path( self::$pathCwd . PATH_SEPARATOR
            . self::$pathIncubatorTests . PATH_SEPARATOR
            . self::$pathIncubatorLibrary .  PATH_SEPARATOR
            . self::$pathLibrary .  PATH_SEPARATOR
            . get_include_path()
        );

    }
}

Zend_Session_PathHelper::setIncludePath();
