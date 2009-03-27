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
class Zend_Tool_Framework_Manifest_ProviderMetadata extends Zend_Tool_Framework_Manifest_Metadata
{
    /**
     * @var string
     */
    protected $_type = 'Provider';
    
    /**#@+
     * @var string
     */
    protected $_providerName  = null;
    protected $_actionName    = null;
    protected $_specialtyName = null;
    /**#@-*/

    /**
     * setProviderName
     *
     * @param string $providerName
     * @return Zend_Tool_Framework_Manifest_ProviderMetadata
     */
    public function setProviderName($providerName)
    {
        $this->_providerName = $providerName;
        return $this;
    }

    /**
     * getProviderName()
     *
     * @return string
     */
    public function getProviderName()
    {
        return $this->_providerName;
    }

    /**
     * setActionName()
     *
     * @param string $actionName
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return;
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
     * setSpecialtyName()
     *
     * @param string $specialtyName
     * @return Zend_Tool_Framework_Manifest_ProviderMetadata
     */
    public function setSpecialtyName($specialtyName)
    {
        $this->_specialtyName = $specialtyName;
        return $this;
    }

    /**
     * getSpecialtyName()
     *
     * @return string
     */
    public function getSpecialtyName()
    {
        return $this->_specialtyName;
    }

    /**
     * __toString() cast to string
     *
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();
        $string .= ' (ProviderName: ' . $this->_providerName 
             . ', ActionName: '     . $this->_actionName 
             . ', SpecialtyName: '  . $this->_specialtyName 
             . ')';
        
        return $string;
    }
    
    
}