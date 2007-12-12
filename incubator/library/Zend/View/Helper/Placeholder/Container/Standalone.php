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

/** Zend_View_Helper_Placeholder_Container_Abstract */
require_once 'Zend/View/Helper/Placeholder/Container/Abstract.php';

/**
 * Base class for targetted placeholder helpers
 *
 * @package    Zend_View
 * @subpackage Helpers
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */ 
abstract class Zend_View_Helper_Placeholder_Container_Standalone extends Zend_View_Helper_Placeholder_Container_Abstract
{  
    /**
     * @var Zend_View_Helper_Placeholder_Registry
     */
    protected $_registry;

    /**
     * Registry key under which container registers itself
     * @var string
     */
    protected $_regKey;

    /**
     * @var Zend_View_Interface
     */
    public $view;

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setRegistry(Zend_View_Helper_Placeholder_Registry::getRegistry());
        $this->getRegistry()->setContainer($this->_regKey, $this);
    }

    /**
     * Retrieve registry
     * 
     * @return Zend_View_Helper_Placeholder_Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Set registry object 
     * 
     * @param  Zend_View_Helper_Placeholder_Registry $registry 
     * @return Zend_View_Helper_Placeholder_Container_Standalone
     */
    public function setRegistry(Zend_View_Helper_Placeholder_Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Set viewobject
     * 
     * @param  Zend_View_Interface $view 
     * @return Zend_View_Helper_Placeholder_Container_Standalone
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
        return $this;
    }
}
