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
class Zend_Acl_Permission
{
    /**
     * Contains allow and deny context values
     * @var array
     */
    protected $_context = array('allow' => array(),
                                'deny' => array());

    /**
     * Returns a score for the selected permission
     *
     * The score is factored according to an exact match for an ARO (3), a
     * match for an inherited ARO (2) or a match for an any/all ARO (1).
     *
     * @param string $type
     * @param array $aro
     * @param string $context
     * @return integer
     */
    public function score($type, Zend_Acl_Aro $aro, $context = null)
    {
        $score = 0;
        if (!empty($this->_context[$type])) {
            $acl = $this->_context[$type];

            // determine only existing aros from type
            $defined = array_keys($acl);
            // do any parents match the existing aros?
            $parent = array_intersect($aro->getParent(), $defined);

            foreach ($parent as $id) {
                // first array member will have the highest inheritance
                $factor = $this->_getFactor($aro, $id);

                if (in_array($context, $acl[$id])) {
                    // is there an explicit match for the context?
                    $score = 4 * $factor;
                } elseif (in_array(Zend_Acl::ACO_CATCHALL, $acl[$id])) {
                    // is there an any/all for the context?
                    $score = 1 * $factor;
                }
            }
        }
        return $score;
    }

    /**
     * Returns the contents of the selected permission
     *
     * @param string $type
     * @return array
     */
    public function getPermissions($type)
    {
        if (!array_key_exists($type, $this->_context)) {
            throw new Zend_Acl_Exception('invalid permission type');
        }
        return $this->_context[$type];
    }

    /**
     * Sets contexts for a permission
     *
     * $type represents either an 'allow' or 'deny'
     * $values represents the contexts allowed for the permission type and can
     * be supplied as a string or an array of values
     * $aro can be either a string id or an array of values to represent
     * multiple aros (and their inherited permissions)
     * $mode is provided as either set, add or remove
     *
     * If $values contains the magic value Zend_Acl::ACO_CATCHALL then all
     * nominated aros will provide an explicit match for the permission type.
     * Otherwise, as each context is set, the opposite is checked for to ensure
     * no deadlocks for permissions
     *
     * E.g. If 'admin' is provided for 'allow', then 'admin' will be removed
     * from 'deny' if it exists for the selected aro(s)
     *
     * @param string $type
     * @param mixed $value
     * @param array $aro
     * @param integer $mode
     * @return integer
     */
    public function setValues($type, $value, $aro, $mode = Zend_Acl::MODE_ADD)
    {
        $value = $this->_getContext($value);
        $rtype = $this->_getReverse($type);

        if ($value == array(Zend_Acl::ACO_CATCHALL)) {
            if ($mode == Zend_Acl::MODE_ADD) {
                $mode = Zend_Acl::MODE_SET;
            } elseif ($mode == Zend_Acl::MODE_REMOVE) {
                $mode = Zend_Acl::MODE_SET;
                $value = array();
            }
        }

        foreach ($aro as $member) {

            $id = $member->getId();

            switch($mode) {
                case Zend_Acl::MODE_UNSET:
                    if (isset($this->_context[$type][$id])) {
                        unset($this->_context[$type][$id]);
                    }
                    break;

                case Zend_Acl::MODE_SET:
                    $this->_context[$type][$id] = $value;
                    break;

                case Zend_Acl::MODE_ADD:
                    if (isset($this->_context[$type][$id])) {
                        $merge = array_merge($this->_context[$type][$id], $value);
                    } else {
                        $merge = $value;
                    }
                    if (in_array(Zend_Acl::ACO_CATCHALL, $merge)) {
                        $merge = array(Zend_Acl::ACO_CATCHALL);
                    } else {
                        $merge = array_unique($merge);
                    }
                    $this->_context[$type][$id] = $merge;
                    break;

                case Zend_Acl::MODE_REMOVE:
                    if (is_array($this->_context[$type][$id])) {
                        $merge = array_diff($this->_context[$type][$id], $value);
                        $this->_context[$type][$id] = $merge;
                    }
                    break;
            }

            if (in_array(Zend_Acl::ACO_CATCHALL, $value) && !empty($this->_context[$rtype][$id])) {
                $this->setValues($rtype, Zend_Acl::ACO_CATCHALL, $aro, Zend_Acl::MODE_REMOVE);
            }
        }
    }

    /**
     * Parses context value
     *
     * Ensures that the magic Zend_Acl::ACO_CATCHALL value is returned as a
     * single array (as it overrides all other explicit contexts) if exists.
     * Otherwise cast the value(s) as an array for storage.
     *
     * @return string
     */
    protected function _getContext($value)
    {
        if (!is_array($value)) {
            if (null === $value) {
                $value = Zend_Acl::ACO_CATCHALL;
            }
            $value = array($value);
        }
        if (in_array(Zend_Acl::ACO_CATCHALL, $value)) {
            $value = array(Zend_Acl::ACO_CATCHALL);
        }
        return $value;
    }

    /**
     * Returns a score factor for the selected Aro
     *
     * Ensures that a specific permission context is assigned a higher score
     * than an inherited permission
     *
     * @return integer
     */
    protected function _getFactor(Zend_Acl_Aro $aro, $id)
    {
        if ($aro->getId() == $id) {
            return 3;
        } elseif ($id != Zend_Acl::ARO_DEFAULT) {
            return 2;
        }
        return 1;
    }

    /**
     * Returns the inverse to the permission type
     *
     * @return string
     */
    protected function _getReverse($type)
    {
        if ($type == 'allow') {
            return 'deny';
        } else {
            return 'allow';
        }
    }
}

?>
