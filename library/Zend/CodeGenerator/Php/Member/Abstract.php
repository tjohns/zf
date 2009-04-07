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
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_CodeGenerator_Php_Abstract
 */
require_once 'Zend/CodeGenerator/Php/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_CodeGenerator_Php_Member_Abstract extends Zend_CodeGenerator_Php_Abstract
{
    /**#@+
     * @param const string
     */
    const VISIBILITY_PUBLIC    = 'public';
    const VISIBILITY_PROTECTED = 'protected';
    const VISIBILITY_PRIVATE   = 'private';
    /**#@-*/
    
    /**
     * @var bool
     */
    protected $_isAbstract = false;
    
    /**
     * @var bool
     */
    protected $_isStatic   = false;
    
    /**
     * @var const
     */
    protected $_visibility = self::VISIBILITY_PUBLIC;
    
    /**
     * @var string
     */
    protected $_name = null;

    /**
     * setAbstract()
     *
     * @param bool $isAbstract
     * @return Zend_CodeGenerator_Php_Member_Abstract
     */
    public function setAbstract($isAbstract)
    {
        $this->_isAbstract = ($isAbstract) ? true : false;
        return $this;
    }
    
    /**
     * isAbstract()
     *
     * @return bool
     */
    public function isAbstract()
    {
        return $this->_isAbstract;
    }
    
    /**
     * setStatic()
     *
     * @param bool $isStatic
     * @return Zend_CodeGenerator_Php_Member_Abstract
     */
    public function setStatic($isStatic)
    {
        $this->_isStatic = ($isStatic) ? true : false;
        return $this;
    }
    
    /**
     * isStatic()
     *
     * @return bool
     */
    public function isStatic()
    {
        return $this->_isStatic;
    }    
    
    /**
     * setVisitibility()
     *
     * @param const $visibility
     * @return Zend_CodeGenerator_Php_Member_Abstract
     */
    public function setVisibility($visibility)
    {
        $this->_visibility = $visibility;
        return $this;
    }
    
    /**
     * getVisibility()
     *
     * @return const
     */
    public function getVisibility()
    {
        return $this->_visibility;
    }
    
    /**
     * setName()
     *
     * @param string $name
     * @return Zend_CodeGenerator_Php_Member_Abstract
     */
    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }
    
    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return $this->_name;
    }
}
