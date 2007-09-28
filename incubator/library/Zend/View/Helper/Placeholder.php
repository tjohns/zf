<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE.txt, and
 * is available through the world-wide-web at the following URL:
 * http://framework.zend.com/license/new-bsd. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_View_Helper_Placeholder_Container */
require_once 'Zend/View/Helper/Placeholder/Container.php';

/**
 * Helper for passing data between otherwise segregated Views. It's called
 * Placeholder to make its typical usage obvious, but can be used just as easily
 * for non-Placeholder things. That said, the support for this is only
 * guaranteed to effect subsequently rendered templates, and of course Layouts.
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
class Zend_View_Helper_Placeholder  
{  
    /**
     * @var Zend_View_Interface
     */  
    public $view;  
  
    /**
     * Placeholder items
     * @var array
     */  
    protected $_items = array();  
  
    /**
     * Set view
     * 
     * @param  Zend_View_Interface $view 
     * @return void
     */  
    public function setView(Zend_View_Interface $view)  
    {  
        $this->view = $view;  
    }  
  
    /**
     * Placeholder helper
     * 
     * @param  string $name 
     * @return Zend_View_Helper_Placeholder_Container
     */  
    public function placeholder($name)  
    {  
        $name = (string) $name;  
        if (!isset($this->_items[$name])) {  
            $this->_items[$name] = new Zend_View_Helper_Placeholder_Container(array());  
        }  
  
        return $this->_items[$name];  
    }  
}
