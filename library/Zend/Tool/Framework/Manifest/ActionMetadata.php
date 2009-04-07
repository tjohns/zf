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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Tool_Framework_Manifest_Metadata
 */
require_once 'Zend/Tool/Framework/Manifest/Metadata.php';

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Tool_Framework_Manifest_ActionMetadata extends Zend_Tool_Framework_Manifest_Metadata
{
    
    /**#@+
     * @param string
     */
    protected $_type = 'Action';
    protected $_actionName = null;
    /**#@-*/

    /**
     * setActionName()
     *
     * @param string $actionName
     * @return Zend_Tool_Framework_Manifest_ActionMetadata
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return $this;
    }

    /**
     * getActionName()
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }
    
    /**
     * __toString() cast to string
     *
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();
        $string .= ' (ActionName: ' . $this->_actionName . ')';
        return $string; 
    }
    
}