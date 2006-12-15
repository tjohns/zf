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
 * @version    $Id$
 */


/**
 * Zend
 */
require_once 'Zend.php';


/**
 * Zend_Acl_Aro_Interface
 */
require_once 'Zend/Acl/Aro/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Acl_Aro_Registry
{
    /**
     * Internal ARO registry data storage
     *
     * @var array
     */
    protected $_aros = array();

    /**
     * Adds an ARO having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * an ARO existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the AROs
     * from which the newly added ARO will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Zend_Acl_Aro_Interface              $aro
     * @param  Zend_Acl_Aro_Interface|string|array $parents
     * @throws Zend_Acl_Aro_Registry_Exception
     * @return Zend_Acl_Aro_Registry Provides a fluent interface
     */
    public function add(Zend_Acl_Aro_Interface $aro, $parents = null)
    {
        $aroId = $aro->getAroId();

        if ($this->has($aroId)) {
            throw Zend::exception('Zend_Acl_Aro_Registry_Exception',
                                  "ARO id '$aroId' already exists in the registry");
        }

        $aroParents = array();

        if (null !== $parents) {
            if (!is_array($parents)) {
                $parents = array($parents);
            }
            if (count($parents) > 0) {
                Zend::loadClass('Zend_Acl_Aro_Registry_Exception');
            }
            foreach ($parents as $parent) {
                try {
                    if ($parent instanceof Zend_Acl_Aro_Interface) {
                        $aroParentId = $parent->getAroId();
                    } else {
                        $aroParentId = $parent;
                    }
                    $aroParent = $this->get($aroParentId);
                } catch (Zend_Acl_Aro_Registry_Exception $e) {
                    throw new Zend_Acl_Aro_Registry_Exception("Parent ARO id '$aroParentId' does not exist");
                }
                $aroParents[$aroParentId] = $aroParent;
                $this->_aros[$aroParentId]['children'][$aroId] = $aro;
            }
        }

        $this->_aros[$aroId] = array(
            'instance' => $aro,
            'parents'  => $aroParents,
            'children' => array()
            );

        return $this;
    }

    /**
     * Returns the identified ARO
     *
     * The $aro parameter can either be an ARO or an ARO identifier.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @throws Zend_Acl_Aro_Registry_Exception
     * @return Zend_Acl_Aro_Interface
     */
    public function get($aro)
    {
        if ($aro instanceof Zend_Acl_Aro_Interface) {
            $aroId = $aro->getAroId();
        } else {
            $aroId = (string) $aro;
        }

        if (!$this->has($aro)) {
            throw Zend::exception('Zend_Acl_Aro_Registry_Exception',
                                  "ARO '$aroId' not found");
        }

        return $this->_aros[$aroId]['instance'];
    }

    /**
     * Returns true if and only if the ARO exists in the registry
     *
     * The $aro parameter can either be an ARO or an ARO identifier.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @return boolean
     */
    public function has($aro)
    {
        if ($aro instanceof Zend_Acl_Aro_Interface) {
            $aroId = $aro->getAroId();
        } else {
            $aroId = (string) $aro;
        }

        return isset($this->_aros[$aroId]);
    }

    /**
     * Returns an array of an existing ARO's parents
     *
     * The array keys are the identifiers of the parent AROs, and the values are
     * the parent ARO instances. The parent AROs are ordered in this array by
     * ascending priority. The highest priority parent ARO, last in the array,
     * corresponds with the parent ARO most recently added.
     *
     * If the ARO does not have any parents, then an empty array is returned.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @uses   Zend_Acl_Aro_Registry::get()
     * @return array
     */
    public function getParents($aro)
    {
        $aroId = $this->get($aro)->getAroId();

        return $this->_aros[$aroId]['parents'];
    }

    /**
     * Returns true if and only if $aro inherits from $inherit
     *
     * Both parameters may be either an ARO or an ARO identifier. If
     * $onlyParents is true, then $aro must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $aro
     * inherits from $inherit through its ancestor AROs.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @param  Zend_Acl_Aro_Interface|string $inherit
     * @param  boolean                       $onlyParents
     * @throws Zend_Acl_Aro_Registry_Exception
     * @return boolean
     */
    public function inherits($aro, $inherit, $onlyParents = false)
    {
        Zend::loadClass('Zend_Acl_Aro_Registry_Exception');

        try {
            $aroId     = $this->get($aro)->getAroId();
            $inheritId = $this->get($inherit)->getAroId();
        } catch (Zend_Acl_Aro_Registry_Exception $e) {
            throw $e;
        }

        $inherits = isset($this->_aros[$aroId]['parents'][$inheritId]);

        if ($inherits || $onlyParents) {
            return $inherits;
        }

        foreach ($this->_aros[$aroId]['parents'] as $parentId => $parent) {
            if ($this->inherits($parentId, $inheritId)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes the ARO from the registry
     *
     * The $aro parameter can either be an ARO or an ARO identifier.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @throws Zend_Acl_Aro_Registry_Exception
     * @return Zend_Acl_Aro_Registry Provides a fluent interface
     */
    public function remove($aro)
    {
        Zend::loadClass('Zend_Acl_Aro_Registry_Exception');

        try {
            $aroId = $this->get($aro)->getAroId();
        } catch (Zend_Acl_Aro_Registry_Exception $e) {
            throw $e;
        }

        foreach ($this->_aros[$aroId]['children'] as $childId => $child) {
            unset($this->_aros[$childId]['parents'][$aroId]);
        }
        foreach ($this->_aros[$aroId]['parents'] as $parentId => $parent) {
            unset($this->_aros[$parentId]['children'][$aroId]);
        }

        unset($this->_aros[$aroId]);

        return $this;
    }

    /**
     * Removes all AROs from the registry
     *
     * @return Zend_Acl_Aro_Registry Provides a fluent interface
     */
    public function removeAll()
    {
        $this->_aros = array();

        return $this;
    }

}
