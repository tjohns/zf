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
 * Zend_Acl_Aro_Registry
 */
require_once 'Zend/Acl/Aro/Registry.php';


/**
 * Zend_Acl_Permission
 */
require_once 'Zend/Acl/Permission.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Acl
{
    /**
     * Default group id
     */
    const ARO_DEFAULT = '_default';

    /**
     * Default path
     */
    const PATH_DEFAULT = '_default';

    /**
     * Default delimiter for paths
     */
    const PATH_DELIMITER = '/';

    /**
     * Magic catch-all keyword
     */
    const ACO_CATCHALL = '__ALL__';

    /**
     * Default permission in case of final neutral result
     */
    const PERM_DEFAULT = false;

    /**
     * Modes for adding permissions to a permission container
     */
    const MODE_SET = 1;
    const MODE_ADD = 2;
    const MODE_REMOVE = 3;
    const MODE_UNSET = 4;

    /**
     * Path name for ACO
     * @var string
     */
    protected $_path;

    /**
     * Permissions for this ACO.
     * @var Zend_Acl
     */
    protected $_perm;

    /**
     * ARO registry for this ARO.
     * @var Zend_Acl
     */
    protected $_registry;

    /**
     * Parent ACO.
     * @var Zend_Acl
     */
    protected $_parent;

    /**
     * All children of this ACO
     * @var array
     */
    protected $_data = array();

    /**
     * Class constructor
     *
     * A reference to the parent object is created if supplied.
     *
     * @param  Zend_Acl $parent
     * @param  string   $path
     * @throws Zend_Acl_Exception
     */
    public function __construct(Zend_Acl $parent = null, $path = self::PATH_DEFAULT)
    {
        $this->_parent = $parent;
        $this->_path = $path;
    }

    /**
     * Retrieve reference to ACO via 'path' property
     *
     * If the path exists then return a reference to it, otherwise return a
     * reference to self. This means that permissions can be implied for a path
     * rather than explicitly set (they infer an inherited permission).
     *
     * @param  string $path
     * @return Zend_Acl
     */
    public function __get($path)
    {
        if (isset($this->_data[$path])) {
            return $this->_data[$path];
        } else {
            return $this->_addPath($path);
        }
    }

    /**
     * Create path
     *
     * Add a new path to this ACL.
     *
     * @param  string   $path
     * @param  Zend_Acl $value
     * @return void
     */
    public function __set($path, Zend_Acl $value)
    {
        $this->_data[$path] = $value;
    }

    /**
     * Retrieve the global ARO registry
     *
     * @return Zend_Acl_Aro_Registry
     */
    public function aroRegistry()
    {
        if (!$this->_isRoot()) {
            return $this->getParent()->aroRegistry();
        }
        if (!($this->_registry instanceof Zend_Acl_Aro_Registry)) {
            $this->_registry = new Zend_Acl_Aro_Registry;
        }
        return $this->_registry;
    }

    /**
     * Test permissions for a path
     *
     * Retrieve permissions for an ACO based on the ARO, current context
     * and an optional path. The optional path parameter allows for a top-down
     * search from a root - if not supplied, then this instance is used as a
     * target. A 'null' context will return true if no explicit permissions
     * are set to 'deny' for the specified group on the target ACO.
     *
     * @param string $id
     * @param string $context
     * @param string $path
     * @throws Zend_Acl_Exception
     * @return boolean
     */
    public function valid($aro = Zend_Acl::ARO_DEFAULT, $context = null, $path = null)
    {
        $root = $this->_findPath($this, $path);

        if (is_array($aro)) {
            throw new Zend_Acl_Exception('cannot determine permissions for multiple AROs');
        } else {
            $aro = current($this->_parseAro($aro));
        }

        return $this->_valid($root, $aro, $context);
    }

    /**
     * Returns the ACL's parent object
     *
     * @return Zend_Acl|null
     */
    public function getParent()
    {
        return $this->_parent;
    }

    /**
     * Returns the ACL's child nodes
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->_data;
    }

    /**
     * Returns the ACL's path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_path;
    }

    /**
     * Returns an array of AROs that can access the ACL
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
    public function getValidAro($context = null, $aro = null)
    {
        $valid = array();

        if (null === $aro) {
            $aro = $this->aroRegistry()->toArray();
        }

        foreach ($this->_parseAro($aro) as $member) {
            if ($this->valid($member, $context)) {
                $valid[$member->getId()] = $member;
            }
        }

        return $valid;
    }

    /**
     * Sets allow permissions to the ACL
     *
     * Each parameter can be a string or a numeric array of values to allow
     * assignment to many parameters at once. The $path can use a forward slash
     * delimeter to indicate a nested relative path
     *
     * @param mixed $aro
     * @param mixed $value
     * @param mixed $path
     * @return Zend_Acl Provides a fluent interface
     */
    public function allow($aro = Zend_Acl::ARO_DEFAULT, $value = null, $path = null)
    {
        return $this->_setPermission('allow', $value, $aro, $path, Zend_Acl::MODE_ADD);
    }

    /**
     * Sets deny permissions to the ACL
     *
     * @param mixed $aro
     * @param mixed $value
     * @param mixed $path
     * @return Zend_Acl Provides a fluent interface
     */
    public function deny($aro = Zend_Acl::ARO_DEFAULT, $value = null, $path = null)
    {
        return $this->_setPermission('deny', $value, $aro, $path, Zend_Acl::MODE_ADD);
    }

    /**
     * Removes allow permissions from the ACL
     *
     * Removes explicit allow permissions from the ACL whilst retaining existing
     * values. If no $value is passed, all explicit permissions are removed.
     *
     * @param mixed $aro
     * @param mixed $value
     * @param mixed $path
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAllow($aro = Zend_Acl::ARO_DEFAULT,
                                $value = Zend_Acl::ACO_CATCHALL,
                                $path = Zend_Acl::PATH_DEFAULT)
    {
        return $this->_setPermission('allow', $value, $aro, $path, Zend_Acl::MODE_REMOVE);
    }

    /**
     * Removes deny permissions from the ACL
     *
     * @param mixed $aro
     * @param mixed $value
     * @param mixed $path
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeDeny($aro = Zend_Acl::ARO_DEFAULT,
                               $value = Zend_Acl::ACO_CATCHALL,
                               $path = Zend_Acl::PATH_DEFAULT)
    {
        return $this->_setPermission('deny', $value, $aro, $path, Zend_Acl::MODE_REMOVE);
    }

    /**
     * Returns allow permissions for the current ACL
     *
     * @return Zend_Acl_Permission Provides a fluent interface
     */
    public function getAllow()
    {
        return $this->_getPermission()->getPermissions('allow');
    }

    /**
     * Returns deny permissions for the current ACL
     *
     * @param mixed $path
     * @return Zend_Acl_Permission Provides a fluent interface
     */
    public function getDeny()
    {
        return $this->_getPermission()->getPermissions('deny');
    }

    /**
     * Removes an ACL node
     *
     * If a path is provided and exists, it will be destroyed. If no path is
     * provided then the current ACL will instead be removed from its parent
     * (if the current ACL is not root)
     *
     * @param string $path
     * @return boolean
     * @throws Zend_Acl_Exception
     */
    public function remove($path = null)
    {
        if (null === $path) {
            if (null !== ($parent = $this->getParent())) {
                return $parent->remove($this->getPath());
            } else {
                throw new Zend_Acl_Exception('cannot remove root node');
            }
        } else {
            if (isset($this->_data[$path])) {
                unset($this->_data[$path]);
                return true;
            }
        }
        return false;
    }

    /**
     * Removes an ARO from current node and all children
     *
     * @param mixed $aro
     * @param mixed $context
     * @param mixed $path
     * @return boolean
     */
    public function removeAro($aro, $context = Zend_Acl::ACO_CATCHALL, $path = null)
    {
        $root = $this->_findPath($this, $path);

        $aro = $this->_parseAro($aro);
        foreach ($this->getChildren() as $aco) {
            $aco->removeAro($aro, $context);
        }

        $root->_getPermission()->setValues('allow', $context, $aro, Zend_Acl::MODE_UNSET);
        $root->_getPermission()->setValues('deny', $context, $aro, Zend_Acl::MODE_UNSET);
    }

    /**
     * Removes an ARO from current node and all children
     *
     * @param Zend_Acl $root
     * @param mixed $aro
     * @param mixed $context
     * @return boolean
     */
    protected function _valid($root, $aro, $context)
    {
        $score = 0;
        $score += $root->_getPermission()->score('allow', $aro, $context);
        $score -= $root->_getPermission()->score('deny', $aro, $context);

        if ($score < 0) {
            return false;
        } elseif ($score > 0) {
            return true;
        }

        // Keep working back to root if ACL has a parent
        if (!$root->_isRoot()) {
            return $this->_valid($root->getParent(), $aro, $context);
        }

        // Return a 'catchall' permission as a last resort
        return self::PERM_DEFAULT;
    }

    /**
     * Adds permissions to one or more permission containers
     *
     * This method is responsible for passing contexts and groups to each
     * individual permissions container for processing. The $mode determines
     * if those contexts are to be set, added or removed
     *
     * @param mixed $acl
     * @param mixed $value
     * @param mixed $aro
     * @param mixed $path
     * @param mixed $mode
     * @return Zend_Acl Provides a fluent interface
     */
    protected function _setPermission($acl, $value, $aro, $path,
                                      $mode = Zend_Acl::MODE_SET)
    {
        $aro = $this->_parseAro($aro);

        if (!$this->_isRoot()) {
            $this->_getPermission()->setValues($acl, $value, $aro, $mode);
            $acl = $this;
            while (!$acl->_isRoot()) {
                $path = $acl->getPath();
                $parent = $acl->getParent();
                $parent->$path = $acl;
                $acl = $parent;
            }
        } else {
            if (!is_array($path)) {
                $path = array($path);
            }
            foreach ($path as $container) {
                $this->_createPath($container)->_getPermission()->setValues($acl, $value, $aro, $mode);
            }
        }

        return $this;
    }

    /**
     * Retrieves permissions for a container
     *
     * @return Zend_Acl_Permission
     */
    protected function _getPermission()
    {
        if (null === $this->_perm) {
            $this->_perm = new Zend_Acl_Permission;
        }
        return $this->_perm;
    }

    /**
     * Retrieves an array of ARO objects for the selected id
     *
     * @return array
     */
    protected function _parseAro($id)
    {
        $aro = array();
        if (!is_array($id)) {
            $id = array($id);
        }
        foreach ($id as $member) {
            if (!($member instanceof Zend_Acl_Aro)) {
                $member = $this->aroRegistry()->find($member);
            }
            array_push($aro, $member);
        }
        return $aro;
    }

    /**
     * Creates child paths from current ACO to destination ACO
     *
     * $path uses a forward slash to denote a nested path (@see setAllow()).
     * If the target path does not exist, a new empty ACL is created.
     *
     * @param string $path
     * @return Zend_Acl_Permission
     */
    protected function _createPath($path)
    {
        $root = $this;
        if (null !== $path && $path !== '_default') {
            $path = explode(Zend_Acl::PATH_DELIMITER, $path);
            foreach ($path as $key) {
                $root->{$key} = $this->$key;
                $root = $root->{$key};
            }
        }
        return $root;
    }

    /**
     * Creates new 'virtual' ACLs to target (nonexistent) path
     *
     * @param mixed $path
     * @return Zend_Acl
     */
    protected function _addPath($path)
    {
        return new self($this, $path);
    }

    /**
     * Expand a path to retrieve target node
     *
     * @param Zend_Acl $root
     * @param mixed $path
     * @return Zend_Acl
     */
    protected function _findPath(Zend_Acl $root, $path)
    {
        if (null !== $path) {
            // cascade down to retrieve final path
            if (!is_array($path)) {
                $delimiter = self::PATH_DELIMITER;
                $path = explode($delimiter, trim("{$path}", $delimiter));
            }
            foreach ($path as $container) {
                $root = $root->{$container};
            }
        }
        return $root;
    }

    /**
     * Determines if the current ACL is root
     *
     * @return boolean
     */
    protected function _isRoot()
    {
        return null === $this->_parent;
    }

}
