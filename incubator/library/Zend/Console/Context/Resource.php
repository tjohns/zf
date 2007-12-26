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
 * @package    Zend_Console_Context
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Console_Context_Action
{
    private $_resource = null;

    public function init(array $argv = array(), $verbosity = 0)
    {
        // Translate the arguments in to Zend_Config here
        
    }
    
    public function getResource()
    {
        require_once 'Zend/Build/Resource.php';
    }
    
    public function getUsage()
    {
        
    }
    
    public function getOptions()
    {
        
    }
}