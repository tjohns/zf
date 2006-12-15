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
 * Zend_Acl_Aco_Interface
 */
require_once 'Zend/Acl/Aco/Interface.php';


/**
 * Zend_Acl_Aro_Registry
 */
require_once 'Zend/Acl/Aro/Registry.php';


/**
 * Zend_Acl_Assert_Interface
 */
require_once 'Zend/Acl/Assert/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Acl
{
    /**
     * Rule type: allow
     */
    const TYPE_ALLOW = 'TYPE_ALLOW';

    /**
     * Rule type: deny
     */
    const TYPE_DENY  = 'TYPE_DENY';

    /**
     * Rule operation: add
     */
    const OP_ADD = 'OP_ADD';

    /**
     * Rule operation: remove
     */
    const OP_REMOVE = 'OP_REMOVE';

    /**
     * ARO registry
     *
     * @var Zend_Acl_Aro_Registry
     */
    protected $_aroRegistry = null;

    /**
     * ACO tree
     *
     * @var array
     */
    protected $_acos = array();

    /**
     * ACL rules; whitelist (deny everything to all) by default
     *
     * @var array
     */
    protected $_rules = array(
        'allAcos' => array(
            'allAros' => array(
                'allPrivileges' => array(
                    'type'   => self::TYPE_DENY,
                    'assert' => null
                    ),
                'byPrivilegeId' => array()
                ),
            'byAroId' => array()
            ),
        'byAcoId' => array()
        );

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
     * @uses   Zend_Acl_Aro_Registry::add()
     * @return Zend_Acl Provides a fluent interface
     */
    public function addAro(Zend_Acl_Aro_Interface $aro, $parents = null)
    {
        $this->_getAroRegistry()->add($aro, $parents);

        return $this;
    }

    /**
     * Returns the identified ARO
     *
     * The $aro parameter can either be an ARO or an ARO identifier.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @uses   Zend_Acl_Aro_Registry::get()
     * @return Zend_Acl_Aro_Interface
     */
    public function getAro($aro)
    {
        return $this->_getAroRegistry()->get($aro);
    }

    /**
     * Returns true if and only if the ARO exists in the registry
     *
     * The $aro parameter can either be an ARO or an ARO identifier.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @uses   Zend_Acl_Aro_Registry::has()
     * @return boolean
     */
    public function hasAro($aro)
    {
        return $this->_getAroRegistry()->has($aro);
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
     * @uses   Zend_Acl_Aro_Registry::inherits()
     * @return boolean
     */
    public function inheritsAro($aro, $inherit, $onlyParents = false)
    {
        return $this->_getAroRegistry()->inherits($aro, $inherit, $onlyParents = false);
    }

    /**
     * Removes the ARO from the registry
     *
     * The $aro parameter can either be an ARO or an ARO identifier.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @uses   Zend_Acl_Aro_Registry::remove()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAro($aro)
    {
        $this->_getAroRegistry()->remove($aro);

        if ($aro instanceof Zend_Acl_Aro_Interface) {
            $aroId = $aro->getAroId();
        } else {
            $aroId = $aro;
        }

        foreach ($this->_rules['allAcos']['byAroId'] as $aroIdCurrent => $rules) {
            if ($aroId === $aroIdCurrent) {
                unset($this->_rules['allAcos']['byAroId'][$aroIdCurrent]);
            }
        }
        foreach ($this->_rules['byAcoId'] as $acoIdCurrent => $visitor) {
            foreach ($visitor['byAroId'] as $aroIdCurrent => $rules) {
                if ($aroId === $aroIdCurrent) {
                    unset($this->_rules['byAcoId'][$acoIdCurrent]['byAroId'][$aroIdCurrent]);
                }
            }
        }

        return $this;
    }

    /**
     * Removes all AROs from the registry
     *
     * @uses   Zend_Acl_Aro_Registry::removeAll()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAroAll()
    {
        $this->_getAroRegistry()->removeAll();

        foreach ($this->_rules['allAcos']['byAroId'] as $aroIdCurrent => $rules) {
            unset($this->_rules['allAcos']['byAroId'][$aroIdCurrent]);
        }
        foreach ($this->_rules['byAcoId'] as $acoIdCurrent => $visitor) {
            foreach ($visitor['byAroId'] as $aroIdCurrent => $rules) {
                unset($this->_rules['byAcoId'][$acoIdCurrent]['byAroId'][$aroIdCurrent]);
            }
        }

        return $this;
    }

    /**
     * Adds an ACO having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing ACO from which the newly added ACO will inherit.
     *
     * @param  Zend_Acl_Aco_Interface        $aco
     * @param  Zend_Acl_Aco_Interface|string $parent
     * @throws Zend_Acl_Exception
     * @return Zend_Acl Provides a fluent interface
     */
    public function add(Zend_Acl_Aco_Interface $aco, $parent = null)
    {
        $acoId = $aco->getAcoId();

        if ($this->has($acoId)) {
            throw Zend::exception('Zend_Acl_Exception',
                                  "ACO id '$acoId' already exists in the ACL");
        }

        $acoParent = null;

        if (null !== $parent) {
            Zend::loadClass('Zend_Acl_Exception');
            try {
                if ($parent instanceof Zend_Acl_Aco_Interface) {
                    $acoParentId = $parent->getAcoId();
                } else {
                    $acoParentId = $parent;
                }
                $acoParent = $this->get($acoParentId);
            } catch (Zend_Acl_Exception $e) {
                throw new Zend_Acl_Exception("Parent ACO id '$acoParentId' does not exist");
            }
            $this->_acos[$acoParentId]['children'][$acoId] = $aco;
        }

        $this->_acos[$acoId] = array(
            'instance' => $aco,
            'parent'   => $acoParent,
            'children' => array()
            );

        return $this;
    }

    /**
     * Returns the identified ACO
     *
     * The $aco parameter can either be an ACO or an ACO identifier.
     *
     * @param  Zend_Acl_Aco_Interface|string $aco
     * @throws Zend_Acl_Exception
     * @return Zend_Acl_Aco_Interface
     */
    public function get($aco)
    {
        if ($aco instanceof Zend_Acl_Aco_Interface) {
            $acoId = $aco->getAcoId();
        } else {
            $acoId = (string) $aco;
        }

        if (!$this->has($aco)) {
            throw Zend::exception('Zend_Acl_Exception',
                                  "ACO '$acoId' not found");
        }

        return $this->_acos[$acoId]['instance'];
    }

    /**
     * Returns true if and only if the ACO exists in the ACL
     *
     * The $aco parameter can either be an ACO or an ACO identifier.
     *
     * @param  Zend_Acl_Aco_Interface|string $aco
     * @return boolean
     */
    public function has($aco)
    {
        if ($aco instanceof Zend_Acl_Aco_Interface) {
            $acoId = $aco->getAcoId();
        } else {
            $acoId = (string) $aco;
        }

        return isset($this->_acos[$acoId]);
    }

    /**
     * Returns true if and only if $aco inherits from $inherit
     *
     * Both parameters may be either an ACO or an ACO identifier. If
     * $onlyParent is true, then $aco must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance tree to determine whether $aco
     * inherits from $inherit through its ancestor ACOs.
     *
     * @param  Zend_Acl_Aco_Interface|string $aco
     * @param  Zend_Acl_Aco_Interface|string $inherit
     * @param  boolean                       $onlyParent
     * @throws Zend_Acl_Aco_Registry_Exception
     * @return boolean
     */
    public function inherits($aco, $inherit, $onlyParent = false)
    {
        Zend::loadClass('Zend_Acl_Exception');

        try {
            $acoId     = $this->get($aco)->getAcoId();
            $inheritId = $this->get($inherit)->getAcoId();
        } catch (Zend_Acl_Exception $e) {
            throw $e;
        }

        if (null !== $this->_acos[$acoId]['parent']) {
            $parentId = $this->_acos[$acoId]['parent']->getAcoId();
            if ($inheritId === $parentId) {
                return true;
            } else if ($onlyParent) {
                return false;
            }
        } else {
            return false;
        }

        while (null !== $this->_acos[$parentId]['parent']) {
            $parentId = $this->_acos[$parentId]['parent']->getAcoId();
            if ($inheritId === $parentId) {
                return true;
            }
        }

        return false;
    }

    /**
     * Removes an ACO and all of its children
     *
     * The $aco parameter can either be an ACO or an ACO identifier.
     *
     * @param  Zend_Acl_Aco_Interface|string $aco
     * @throws Zend_Acl_Exception
     * @return Zend_Acl Provides a fluent interface
     */
    public function remove($aco)
    {
        Zend::loadClass('Zend_Acl_Exception');

        try {
            $acoId = $this->get($aco)->getAcoId();
        } catch (Zend_Acl_Exception $e) {
            throw $e;
        }

        $acosRemoved = array($acoId);
        if (null !== ($acoParent = $this->_acos[$acoId]['parent'])) {
            unset($this->_acos[$acoParent->getAcoId()]['children'][$acoId]);
        }
        foreach ($this->_acos[$acoId]['children'] as $childId => $child) {
            $this->remove($childId);
            $acosRemoved[] = $childId;
        }

        foreach ($acosRemoved as $acoIdRemoved) {
            foreach ($this->_rules['byAcoId'] as $acoIdCurrent => $rules) {
                if ($acoIdRemoved === $acoIdCurrent) {
                    unset($this->_rules['byAcoId'][$acoIdCurrent]);
                }
            }
        }

        unset($this->_acos[$acoId]);

        return $this;
    }

    /**
     * Removes all ACOs
     *
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAll()
    {
        foreach ($this->_acos as $acoId => $aco) {
            foreach ($this->_rules['byAcoId'] as $acoIdCurrent => $rules) {
                if ($acoId === $acoIdCurrent) {
                    unset($this->_rules['byAcoId'][$acoIdCurrent]);
                }
            }
        }

        $this->_acos = array();

        return $this;
    }

    /**
     * Adds an "allow" rule to the ACL
     *
     * @param  Zend_Acl_Aro_Interface|string|array $aros
     * @param  Zend_Acl_Aco_Interface|string|array $acos
     * @param  string|array                        $privileges
     * @param  Zend_Acl_Assert_Interface           $assert
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function allow($aros = null, $acos = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        return $this->setRule(self::OP_ADD, self::TYPE_ALLOW, $aros, $acos, $privileges, $assert);
    }

    /**
     * Adds a "deny" rule to the ACL
     *
     * @param  Zend_Acl_Aro_Interface|string|array $aros
     * @param  Zend_Acl_Aco_Interface|string|array $acos
     * @param  string|array                        $privileges
     * @param  Zend_Acl_Assert_Interface           $assert
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function deny($aros = null, $acos = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        return $this->setRule(self::OP_ADD, self::TYPE_DENY, $aros, $acos, $privileges, $assert);
    }

    /**
     * Removes "allow" permissions from the ACL
     *
     * @param  Zend_Acl_Aro_Interface|string|array $aros
     * @param  Zend_Acl_Aco_Interface|string|array $acos
     * @param  string|array                        $privileges
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeAllow($aros = null, $acos = null, $privileges = null)
    {
        return $this->setRule(self::OP_REMOVE, self::TYPE_ALLOW, $aros, $acos, $privileges);
    }

    /**
     * Removes "deny" restrictions from the ACL
     *
     * @param  Zend_Acl_Aro_Interface|string|array $aros
     * @param  Zend_Acl_Aco_Interface|string|array $acos
     * @param  string|array                        $privileges
     * @uses   Zend_Acl::setRule()
     * @return Zend_Acl Provides a fluent interface
     */
    public function removeDeny($aros = null, $acos = null, $privileges = null)
    {
        return $this->setRule(self::OP_REMOVE, self::TYPE_DENY, $aros, $acos, $privileges);
    }

    /**
     * Performs operations on ACL rules
     *
     * The $operation parameter may be either OP_ADD or OP_REMOVE, depending on whether the
     * user wants to add or remove a rule, respectively:
     *
     * OP_ADD specifics:
     *
     *      A rule is added that would allow one or more AROs access to [certain $privileges
     *      upon] the specified ACO(s).
     *
     * OP_REMOVE specifics:
     *
     *      The rule is removed only in the context of the given AROs, ACOs, and privileges.
     *      Existing rules to which the remove operation does not apply would remain in the
     *      ACL.
     *
     * The $type parameter may be either TYPE_ALLOW or TYPE_DENY, depending on whether the
     * rule is intended to allow or deny permission, respectively.
     *
     * The $aros and $acos parameters may be references to, or the string identifiers for,
     * existing ACOs/AROs, or they may be passed as arrays of these - mixing string identifiers
     * and objects is ok - to indicate the ACOs and AROs to which the rule applies. If either
     * $aros or $acos is null, then the rule applies to all AROs or all ACOs, respectively.
     * Both may be null in order to work with the default rule of the ACL.
     *
     * The $privileges parameter may be used to further specify that the rule applies only
     * to certain privileges upon the ACO(s) in question. This may be specified to be a single
     * privilege with a string, and multiple privileges may be specified as an array of strings.
     *
     * If $assert is provided, then its assert() method must return true in order for
     * the rule to apply. If $assert is provided with $aros, $acos, and $privileges all
     * equal to null, then a rule having a type of:
     *
     *      TYPE_ALLOW will imply a type of TYPE_DENY, and
     *
     *      TYPE_DENY will imply a type of TYPE_ALLOW
     *
     * when the rule's assertion fails. This is because the ACL needs to provide expected
     * behavior when an assertion upon the default ACL rule fails.
     *
     * @param  string                              $operation
     * @param  string                              $type
     * @param  Zend_Acl_Aro_Interface|string|array $aros
     * @param  Zend_Acl_Aco_Interface|string|array $acos
     * @param  string|array                        $privileges
     * @param  Zend_Acl_Assert_Interface           $assert
     * @throws Zend_Acl_Exception
     * @uses   Zend_Acl_Aro_Registry::get()
     * @uses   Zend_Acl::get()
     * @return Zend_Acl Provides a fluent interface
     */
    public function setRule($operation, $type, $aros = null, $acos = null, $privileges = null,
                            Zend_Acl_Assert_Interface $assert = null)
    {
        // ensure that the rule type is valid; normalize input to uppercase
        $type = strtoupper($type);
        if (self::TYPE_ALLOW !== $type && self::TYPE_DENY !== $type) {
            throw Zend::exception('Zend_Acl_Exception', "Unsupported rule type; must be either '"
                                . self::TYPE_ALLOW . "' or '" . self::TYPE_DENY . "'");
        }

        // ensure that all specified AROs exist; normalize input to array of ARO objects or null
        if (!is_array($aros)) {
            $aros = array($aros);
        } else if (0 === count($aros)) {
            $aros = array(null);
        }
        $arosTemp = $aros;
        $aros = array();
        foreach ($arosTemp as $aro) {
            if (null !== $aro) {
                $aros[] = $this->_getAroRegistry()->get($aro);
            } else {
                $aros[] = null;
            }
        }
        unset($arosTemp);

        // ensure that all specified ACOs exist; normalize input to array of ACO objects or null
        if (!is_array($acos)) {
            $acos = array($acos);
        } else if (0 === count($acos)) {
            $acos = array(null);
        }
        $acosTemp = $acos;
        $acos = array();
        foreach ($acosTemp as $aco) {
            if (null !== $aco) {
                $acos[] = $this->get($aco);
            } else {
                $acos[] = null;
            }
        }
        unset($acosTemp);

        // normalize privileges to array
        if (null === $privileges) {
            $privileges = array();
        } else if (!is_array($privileges)) {
            $privileges = array($privileges);
        }

        switch ($operation) {

            // add to the rules
            case self::OP_ADD:
                foreach ($acos as $aco) {
                    foreach ($aros as $aro) {
                        $rules =& $this->_getRules($aco, $aro, true);
                        if (0 === count($privileges)) {
                            $rules['allPrivileges']['type']   = $type;
                            $rules['allPrivileges']['assert'] = $assert;
                            if (!isset($rules['byPrivilegeId'])) {
                                $rules['byPrivilegeId'] = array();
                            }
                        } else {
                            foreach ($privileges as $privilege) {
                                $rules['byPrivilegeId'][$privilege]['type']   = $type;
                                $rules['byPrivilegeId'][$privilege]['assert'] = $assert;
                            }
                        }
                    }
                }
                break;

            // remove from the rules
            case self::OP_REMOVE:
                foreach ($acos as $aco) {
                    foreach ($aros as $aro) {
                        $rules =& $this->_getRules($aco, $aro);
                        if (null === $rules) {
                            continue;
                        }
                        if (0 === count($privileges)) {
                            if (null === $aco && null === $aro) {
                                if ($type === $rules['allPrivileges']['type']) {
                                    $rules = array(
                                        'allPrivileges' => array(
                                            'type'   => self::TYPE_DENY,
                                            'assert' => null
                                            ),
                                        'byPrivilegeId' => array()
                                        );
                                }
                                continue;
                            }
                            if ($type === $rules['allPrivileges']['type']) {
                                unset($rules['allPrivileges']);
                            }
                        } else {
                            foreach ($privileges as $privilege) {
                                if (isset($rules['byPrivilegeId'][$privilege]) &&
                                    $type === $rules['byPrivilegeId'][$privilege]['type']) {
                                    unset($rules['byPrivilegeId'][$privilege]);
                                }
                            }
                        }
                    }
                }
                break;

            default:
                throw Zend::exception('Zend_Acl_Exception', "Unsupported operation; must be either '" . self::OP_ADD
                                    . "' or '" . self::OP_REMOVE . "'");
        }

        return $this;
    }

    /**
     * Returns true if and only if the ARO has access to the ACO
     *
     * The $aro and $aco parameters may be references to, or the string identifiers for,
     * an existing ACO and ARO combination.
     *
     * If either $aros or $acos is null, then the query applies to all AROs or all ACOs,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend_Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * ARO is denied access to at least one privilege upon the ACO. In other words, this
     * method returns true if and only if the ARO is allowed all privileges on the ACO.
     *
     * This method checks ARO inheritance using a depth-first traversal of the ARO registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the ARO are checked.
     *
     * @param  Zend_Acl_Aro_Interface|string $aro
     * @param  Zend_Acl_Aco_Interface|string $aco
     * @param  string                        $privilege
     * @uses   Zend_Acl::get()
     * @uses   Zend_Acl_Aro_Registry::get()
     * @return boolean
     */
    public function isAllowed($aro = null, $aco = null, $privilege = null)
    {
        if (null !== $aro) {
            $aro = $this->_getAroRegistry()->get($aro);
        }

        if (null !== $aco) {
            $aco = $this->get($aco);
        }

        if (null === $privilege) {
            // query on all privileges
            do {
                // depth-first search on $aro if it is not 'allAros' pseudo-parent
                if (null !== $aro && null !== ($result = $this->_aroDFSAllPrivileges($aro, $aco, $privilege))) {
                    return $result;
                }

                // look for rule on 'allAros' psuedo-parent
                if (null !== ($rules = $this->_getRules($aco, null))) {
                    foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
                        if (self::TYPE_DENY === ($ruleTypeOnePrivilege = $this->_getRuleType($aco, null, $privilege))) {
                            return false;
                        }
                    }
                    if (null !== ($ruleTypeAllPrivileges = $this->_getRuleType($aco, null, null))) {
                        return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
                    }
                }

                // try next ACO
                $aco = $this->_acos[$aco->getAcoId()]['parent'];

            } while (true); // loop terminates at 'allAcos' pseudo-parent
        } else {
            // query on one privilege
            do {
                // depth-first search on $aro if it is not 'allAros' pseudo-parent
                if (null !== $aro && null !== ($result = $this->_aroDFSOnePrivilege($aro, $aco, $privilege))) {
                    return $result;
                }

                // look for rule on 'allAros' pseudo-parent
                if (null !== ($ruleType = $this->_getRuleType($aco, null, $privilege))) {
                    return self::TYPE_ALLOW === $ruleType;
                } else if (null !== ($ruleTypeAllPrivileges = $this->_getRuleType($aco, null, null))) {
                    return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
                }

                // try next ACO
                $aco = $this->_acos[$aco->getAcoId()]['parent'];

            } while (true); // loop terminates at 'allAcos' pseudo-parent
        }
    }

    /**
     * Returns the ARO registry for this ACL
     *
     * If no ARO registry has been created yet, a new default ARO registry
     * is created and returned.
     *
     * @return Zend_Acl_Aro_Registry
     */
    protected function _getAroRegistry()
    {
        if (null === $this->_aroRegistry) {
            $this->_aroRegistry = new Zend_Acl_Aro_Registry();
        }
        return $this->_aroRegistry;
    }

    /**
     * Performs a depth-first search of the ARO DAG, starting at $aro, in order to find a rule
     * allowing/denying $aro access to all privileges upon $aco
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  Zend_Acl_Aco_Interface $aco
     * @return boolean|null
     */
    protected function _aroDFSAllPrivileges(Zend_Acl_Aro_Interface $aro, Zend_Acl_Aco_Interface $aco = null)
    {
        $dfs = array(
            'visited' => array(),
            'stack'   => array()
            );

        if (null !== ($result = $this->_aroDFSVisitAllPrivileges($aro, $aco, $dfs))) {
            return $result;
        }

        while (null !== ($aro = array_pop($dfs['stack']))) {
            if (!isset($dfs['visited'][$aro->getAroId()])) {
                if (null !== ($result = $this->_aroDFSVisitAllPrivileges($aro, $aco, $dfs))) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Visits an $aro in order to look for a rule allowing/denying $aro access to all privileges upon $aco
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * This method is used by the internal depth-first search algorithm and may modify the DFS data structure.
     *
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  Zend_Acl_Aco_Interface $aco
     * @param  array                  $dfs
     * @return boolean|null
     */
    protected function _aroDFSVisitAllPrivileges(Zend_Acl_Aro_Interface $aro, Zend_Acl_Aco_Interface $aco = null,
                                                 &$dfs)
    {
        if (null !== ($rules = $this->_getRules($aco, $aro))) {
            foreach ($rules['byPrivilegeId'] as $privilege => $rule) {
                if (self::TYPE_DENY === ($ruleTypeOnePrivilege = $this->_getRuleType($aco, $aro, $privilege))) {
                    return false;
                }
            }
            if (null !== ($ruleTypeAllPrivileges = $this->_getRuleType($aco, $aro, null))) {
                return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
            }
        }

        $dfs['visited'][$aro->getAroId()] = true;
        foreach ($this->_getAroRegistry()->getParents($aro) as $aroParentId => $aroParent) {
            $dfs['stack'][] = $aroParent;
        }

        return null;
    }

    /**
     * Performs a depth-first search of the ARO DAG, starting at $aro, in order to find a rule
     * allowing/denying $aro access to a $privilege upon $aco
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  Zend_Acl_Aco_Interface $aco
     * @param  string                 $privilege
     * @return boolean|null
     */
    protected function _aroDFSOnePrivilege(Zend_Acl_Aro_Interface $aro, Zend_Acl_Aco_Interface $aco = null, $privilege)
    {
        $dfs = array(
            'visited' => array(),
            'stack'   => array()
            );

        if (null !== ($result = $this->_aroDFSVisitOnePrivilege($aro, $aco, $privilege, $dfs))) {
            return $result;
        }

        while (null !== ($aro = array_pop($dfs['stack']))) {
            if (!isset($dfs['visited'][$aro->getAroId()])) {
                if (null !== ($result = $this->_aroDFSVisitOnePrivilege($aro, $aco, $privilege, $dfs))) {
                    return $result;
                }
            }
        }

        return null;
    }

    /**
     * Visits an $aro in order to look for a rule allowing/denying $aro access to a $privilege upon $aco
     *
     * This method returns true if a rule is found and allows access. If a rule exists and denies access,
     * then this method returns false. If no applicable rule is found, then this method returns null.
     *
     * This method is used by the internal depth-first search algorithm and may modify the DFS data structure.
     *
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  Zend_Acl_Aco_Interface $aco
     * @param  string                 $privilege
     * @param  array                  $dfs
     * @return boolean|null
     */
    protected function _aroDFSVisitOnePrivilege(Zend_Acl_Aro_Interface $aro, Zend_Acl_Aco_Interface $aco = null,
                                                $privilege, &$dfs)
    {
        if (null !== ($ruleTypeOnePrivilege = $this->_getRuleType($aco, $aro, $privilege))) {
            return self::TYPE_ALLOW === $ruleTypeOnePrivilege;
        } else if (null !== ($ruleTypeAllPrivileges = $this->_getRuleType($aco, $aro, null))) {
            return self::TYPE_ALLOW === $ruleTypeAllPrivileges;
        }

        $dfs['visited'][$aro->getAroId()] = true;
        foreach ($this->_getAroRegistry()->getParents($aro) as $aroParentId => $aroParent) {
            $dfs['stack'][] = $aroParent;
        }

        return null;
    }

    /**
     * Returns the rule type associated with the specified ACO, ARO, and privilege
     * combination.
     *
     * If a rule does not exist or its attached assertion fails, which means that
     * the rule is not applicable, then this method returns null. Otherwise, the
     * rule type applies and is returned as either TYPE_ALLOW or TYPE_DENY.
     *
     * If $aco or $aro is null, then this means that the rule must apply to
     * all ACOs or AROs, respectively.
     *
     * If $privilege is null, then the rule must apply to all privileges.
     *
     * If all three parameters are null, then the default ACL rule type is returned,
     * based on whether its assertion method passes.
     *
     * @param  Zend_Acl_Aco_Interface $aco
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  string                 $privilege
     * @return string|null
     */
    protected function _getRuleType(Zend_Acl_Aco_Interface $aco = null, Zend_Acl_Aro_Interface $aro = null,
                                    $privilege = null)
    {
        // get the rules for the $aco and $aro
        if (null === ($rules = $this->_getRules($aco, $aro))) {
            return null;
        }

        // follow $privilege
        if (null === $privilege) {
            if (isset($rules['allPrivileges'])) {
                $rule = $rules['allPrivileges'];
            } else {
                return null;
            }
        } else if (!isset($rules['byPrivilegeId'][$privilege])) {
            return null;
        } else {
            $rule = $rules['byPrivilegeId'][$privilege];
        }

        // check assertion if necessary
        if (null === $rule['assert'] || $rule['assert']->assert($this, $aro, $aco, $privilege)) {
            return $rule['type'];
        } else if (null !== $aco || null !== $aro || null !== $privilege) {
            return null;
        } else if (self::TYPE_ALLOW === $rule['type']) {
            return self::TYPE_DENY;
        } else {
            return self::TYPE_ALLOW;
        }
    }

    /**
     * Returns the rules associated with an ACO and an ARO, or null if no such rules exist
     *
     * If either $aco or $aro is null, this means that the rules returned are for all ACOs or all AROs,
     * respectively. Both can be null to return the default rule set for all ACOs and all AROs.
     *
     * If the $create parameter is true, then a rule set is first created and then returned to the caller.
     *
     * @param  Zend_Acl_Aco_Interface $aco
     * @param  Zend_Acl_Aro_Interface $aro
     * @param  boolean                $create
     * @return array|null
     */
    protected function &_getRules(Zend_Acl_Aco_Interface $aco = null, Zend_Acl_Aro_Interface $aro = null,
                                  $create = false)
    {
        // create a reference to null
        $null = null;
        $nullRef =& $null;

        // follow $aco
        do {
            if (null === $aco) {
                $visitor =& $this->_rules['allAcos'];
                break;
            }
            $acoId = $aco->getAcoId();
            if (!isset($this->_rules['byAcoId'][$acoId])) {
                if (!$create) {
                    return $nullRef;
                }
                $this->_rules['byAcoId'][$acoId] = array();
            }
            $visitor =& $this->_rules['byAcoId'][$acoId];
        } while (false);


        // follow $aro
        if (null === $aro) {
            if (!isset($visitor['allAros'])) {
                if (!$create) {
                    return $nullRef;
                }
                $visitor['allAros']['byPrivilegeId'] = array();
            }
            return $visitor['allAros'];
        }
        $aroId = $aro->getAroId();
        if (!isset($visitor['byAroId'][$aroId])) {
            if (!$create) {
                return $nullRef;
            }
            $visitor['byAroId'][$aroId]['byPrivilegeId'] = array();
        }
        return $visitor['byAroId'][$aroId];
    }

}
