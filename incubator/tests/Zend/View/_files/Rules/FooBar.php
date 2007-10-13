<?php
/** Zend_View_Inflector_Rule_Interface */
require_once 'Zend/View/Inflector/Rule/Interface.php';

/**
 * Test rule
 *
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Rules_FooBar implements Zend_View_Inflector_Rule_Interface
{
    public function inflect($path, array $params = array())
    {
        return strtoupper($path);
    }
}
