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
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_CodeGenerator_Php_TestClassWithManyProperties
{
    
    const FOO = 'foo';
    
    public static $fooStaticProperty = null;
    
    public $fooProperty = true;
    
    protected static $_barStaticProperty = 1;
    
    protected $_barProperty = 1.1115;
    
    private static $_bazStaticProperty = self::FOO;
    
    private $_bazProperty = array(true, false, true);
    
    protected $_complexType = array(
        5,
        'one' => 1,
        'two' => '2',
        array(
            'bar',
            'baz',
            //PHP_EOL
            )
        );
    
}
