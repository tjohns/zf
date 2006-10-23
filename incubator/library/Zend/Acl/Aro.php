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
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Acl_Aro
{
    /**
     * Unique id of ARO
     * @var string
     */
    protected $_id;

    /**
     * Reference to parent ARO registry
     * @var Zend_Acl_Aro_Registry
     */
    protected $_registry;

    /**
     * Collection of parents
     * @var array
     */
    protected $_parent = array();

    /**
     * Class constructor
     *
     * If $inherit contains a string or array of values, then iterate through
     * the parents, retrieving their ids and store in LIFO order. The first
     * element of the $_parent array will always refer to the current object
     *
     * @param string $id
     * @param mixed $inherit
     * @return void
     */
    public function __construct(Zend_Acl_Aro_Registry $registry, $id, $inherit = null)
    {
        $this->_id = $id;
        $this->_registry = $registry;
        $this->_addParent($this);

        if (null !== $inherit) {
            if (!is_array($inherit)) {
                $inherit = array($inherit);
            }

            foreach ($inherit as $parent) {

                if (!($parent instanceof self)) {
                    $parent = $this->_registry->find($parent);
                }

                foreach ($parent->getParent() as $aro) {
                    $this->_addParent($aro);
                }
                $this->_addParent($parent);
            }
        }
        array_push($this->_parent, Zend_Acl::ARO_DEFAULT);
    }

    /**
     * Retrieves ARO id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Retrieves ARO registry
     *
     * @return Zend_Acl_Aro_Registry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Retrieves ARO parent ids
     *
     * @return array
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Performs a validation on an ACO using the current ARO
     *
     * @return boolean
     */
    public function canAccess(Zend_Acl $aco, $context = null, $path = null)
    {
        return $aco->valid($this, $context, $path);
    }

    /**
     * Returns an ACO tree that the current ARO has access to
     *
     * This function will determine which AROs - from either a list of AROs or
     * the entire ARO registry - have access to the current ARO.
     *
     * AROs can be supplied either an an ARO object, an array of ARO objects,
     * a string id or an array of string ids. If the ARO parameter is left empty
     * then the ARO registry is used to return all members.
     *
     * To allow fine-grain control, a specific context can also be used to
     * validate the AROs
     *
     * An array of ARO objects is returned upon success or empty
     *
     * @param mixed $aro
     * @param string $context
     * @return array
     */
    public function getValidAco(Zend_Acl $aco, $context = null)
    {
        return $this->_getValidAco(clone $aco, $context);
    }

    protected function _getValidAco(Zend_Acl $aco, $context)
    {
        foreach ($aco->getChildren() as $node) {
            if (!$this->canAccess($node, $context)) {
                $aco->remove($node->getPath());
            }
        }
        return $aco;
    }

    /**
     * Add parent to current ARO
     *
     * @return Zend_Acl_Aro
     */
    protected function _addParent($parent)
    {
        if (!($parent instanceof self)) {
            $parent = $this->_registry->find($parent);
        }

        if (!in_array($parent->getId(), $this->_parent)) {
            array_push($this->_parent, $parent->getId());
        }

        return $parent;
    }
}
