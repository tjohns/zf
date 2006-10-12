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
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_Acl_Aro
 */
require_once 'Zend/Acl/Aro.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Acl_Aro_Registry
{
    /**
     * ARO registry
     * @var array
     */
    protected $_aro = array();

    /**
     * Parent Aco
     * @var Zend_Acl
     */
    protected $_aco;

    /**
     * Registry instance
     * @var Zend_Acl_Aro_Registry
     */
    static protected $_instance = null;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->add('_default');
    }

    /**
     * Public access to ARO registry
     *
     * @return void
     */
    public function __get($aro)
    {
        return $this->find($aro);
    }

    /**
     * Add unique ARO to registry
     *
     * @param Zend_Acl_Aro $aro
     * @return Zend_Acl_Aro_Registry for fluent interface
     */
    public function add($aro, $inherit = null)
    {
        if (!($aro instanceof Zend_Acl_Aro)) {
            $aro = new Zend_Acl_Aro($this, $aro, $inherit);
        }

        if (array_key_exists($aro->getId(), $this->_aro)) {
            throw new Zend_Acl_Exception('aro ' . $aro->getId() . ' already registered');
        }

        $this->_aro[$aro->getId()] = $aro;
        return $this;
    }

    /**
     * Remove ARO from registry
     *
     * @param Zend_Acl_Aro $aro
     * @return boolean
     */
    public function remove($aro, $aco)
    {
        $id = $this->find($aro)->getId();

        if ($id == Zend_Acl::ARO_DEFAULT) {
            return false;
        }

        $children = array($id => $id);

        foreach($this->_aro as $aro) {
            if (in_array($id, $aro->getParent())) {
                $children[$aro->getId()] =  $aro->getId();
                $aco->removeAro($aro, $aco);
            }
        }

        foreach($children as $aro) {
            unset($this->_aro[$aro]);
        }

        return true;
    }

    /**
     * FInd group in registry
     *
     * If the named group does not exist, the default ARO is returned
     *
     * @param string $aro
     * @return Zend_Acl_Aro
     */
    public function find($aro)
    {
        if ($aro instanceof Zend_Acl_Aro) {
            $aro = $aro->getId();
           }

        if (isset($this->_aro[$aro])) {
            return $this->_aro[$aro];
        } else {
            return $this->_aro[Zend_Acl::ARO_DEFAULT];
        }
    }

    /**
     * Return registry as an array of ARO objects
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_aro;
    }
}
