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
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: StripTags.php 4135 2007-03-20 12:46:11Z darby $
 */

require_once 'Zend/Loader.php';
require_once 'Zend/Filter.php';
require_once 'Zend/Validate.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Zend_Filter_Input
{

    const OPT_ESCAPE_FILTER = 'escape_filter';
    const OPT_NAMESPACE     = 'namespace';

    const RULE              = 'rule';
    const FIELD             = 'field';
    const PRESENCE          = 'presence';
    const VALIDATOR         = 'validator';
    const VALIDATOR_CHAIN   = 'validatorChain';
    const BREAK_CHAIN       = 'breakChainOnFailure';

    const PRESENCE_REQUIRED = 'required';
    const PRESENCE_OPTIONAL = 'optional';

    /**
     * @var array
     */
    protected $_originalData = array();

    /**
     * @var array
     */
    protected $_data = array();

    /**
     * @var array
     */
    protected $_filterRules = array();

    /**
     * @var array
     */
    protected $_validatorRules = array();

    /**
     * @var array
     */
    protected $_validFields = array();

    /**
     * @var array
     */
    protected $_invalidFields = array();

    /**
     * @var array
     */
    protected $_missingFields = array();

    /**
     * @var array
     */
    protected $_unknownFields = array();

    /**
     * @var array
     */
    protected $_namespaces = array('Zend_Filter', 'Zend_Validate');

    /**
     * @var array
     */
    protected $_userNamespaces = array();

    /**
     * @var Zend_Filter_Interface
     */
    protected $_defaultEscapeFilter = null;

    /**
     * @var boolean
     */
    protected $_processed = false;

    /**
     * @param array $filters
     * @param array $validators
     * @param array $data       OPTIONAL
     * @param array $options    OPTIONAL
     */
    public function __construct(array $filterRules, array $validatorRules, array $data = null, array $options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->_filterRules = $filterRules;
        $this->_validatorRules = $validatorRules;

        if ($data) {
            $this->setData($data);
        }
    }

    /**
     * @param mixed $namespaces
     * @return void
     */
    public function addNamespace($namespaces)
    {
        foreach((array) $namespaces as $namespace) {
            $this->_userNamespaces[] = $namespace;
        }
        $this->_namespaces = array_merge($this->_userNamespaces, array('Zend_Filter', 'Zend_Validate'));
    }

    /**
     * @return boolean
     */
    public function hasInvalid()
    {
        $this->_process();
        return !(empty($this->_invalidFields));
    }

    /**
     * @return boolean
     */
    public function hasMissing()
    {
        $this->_process();
        return !(empty($this->_missingFields));
    }

    /**
     * @return boolean
     */
    public function hasUnknown()
    {
        $this->_process();
        return !(empty($this->_unknownFields));
    }

    /**
     * @return array
     */
    public function getInvalid()
    {
        $this->_process();
        return $this->_invalidFields;
    }

    /**
     * @return array
     */
    public function getMissing()
    {
        $this->_process();
        return $this->_missingFields;
    }

    /**
     * @return array
     */
    public function getUnknown()
    {
        $this->_process();
        return $this->_unknownFields;
    }

    /**
     * @return string
     */
    public function getEscaped($fieldName)
    {
        $this->_process();
        $escapeFilter = $this->_getDefaultEscapeFilter();

        if (isset($this->_validFields[$fieldName])) {
            return $escapeFilter->filter($this->_validFields[$fieldName]);
        } else {
            return null;
        }
    }

    protected function _getDefaultEscapeFilter()
    {
        if ($this->_defaultEscapeFilter !== null) {
            return $this->_defaultEscapeFilter;
        }
        return $this->setDefaultEscapeFilter('htmlEntities');
    }

    /**
     * @return string
     */
    public function getUnescaped($fieldName)
    {
        $this->_process();
        if (isset($this->_validFields[$fieldName])) {
            return $this->_validFields[$fieldName];
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function __get($fieldName)
    {
        return $this->getEscaped($fieldName);
    }

    /**
     * @param string $fieldName
     * @return boolean
     */
    public function __isset($fieldName)
    {
        $this->_process();
        return isset($this->_validFields[$fieldName]);
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->_originalData = $data;
        $this->_data = $data;

        // Reset to initial state
        $this->_validFields = array();
        $this->_invalidFields = array();
        $this->_missingFields = array();
        $this->_unknownFields = array();

        $this->_processed = false;
    }

    /**
     * @param mixed $escapeFilter
     * @return void
     */
    public function setDefaultEscapeFilter($escapeFilter)
    {
        if (is_string($escapeFilter)) {
            $escapeFilter = $this->_getFilter($escapeFilter);
        }
        if (!$escapeFilter) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Cannot find escape filter');
        }
        $this->_defaultEscapeFilter = $escapeFilter;
        return $escapeFilter;
    }

    /**
     * @param array $options
     * @return void
     * @throws Zend_Filter_Exception if an unknown option is given
     */
    public function setOptions(array $options)
    {
        foreach ($options as $option => $value) {
            switch (strtolower($option)) {
                case OPT_ESCAPE_FILTER:
                    $this->setDefaultEscapeFilter($value);
                    break;
                case OPT_NAMESPACE:
                    $this->addNamespace($value);
                    break;
                default:
                    require_once 'Zend/Filter/Exception.php';
                    throw new Zend_Filter_Exception("Unknown option '$option'");
                    break;
            }
        }
    }

    /*
     * Protected methods
     */

    /**
     * @return void
     */
    protected function _process()
    {
        if ($this->_processed === false) {
            $this->_processFilterRules();
            $this->_processValidatorRules();
            $this->_processed = true;
        }
    }

    /**
     * @return void
     * @throws Zend_Filter_Exception
     */
    protected function _processFilterRules()
    {
        foreach ($this->_filterRules as $ruleName => $filterRule) {
            if (!is_array($filterRule)) {
                $filterRule = array($filterRule);
            }

            $filterList = array();
            foreach ($filterRule as $key => $value) {
                if (is_int($key)) {
                    $filterList[] = $value;
                }
            }

            if (!isset($filterRule[self::FIELD])) {
                $filterRule[self::FIELD] = $ruleName;
            }

            $filterChain = new Zend_Filter();
            foreach ($filterList as $filter) {
                if (is_string($filter)) {
                    $filter = $this->_getFilter($filter);
                }
                if (!($filter && $filter instanceof Zend_Filter_Interface)) {
                    require_once 'Zend/Filter/Exception.php';
                    throw new Zend_Filter_Exception('Expected object implementing Zend_Filter_Interface, got '.get_class($filter));
                }
                $filterChain->addFilter($filter);
            }

            $field = $filterRule[self::FIELD];

            // @todo: support multi-valued data inputs
            $this->_data[$field] = $filterChain->filter($this->_data[$field]);
        }
    }

    /**
     * @return void
     * @throws Zend_Validate_Exception
     */
    protected function _processValidatorRules()
    {
        foreach ($this->_validatorRules as $ruleName => $validatorRule) {
            if (!is_array($validatorRule)) {
                $validatorRule = array($validatorRule);
            }

            $validatorList = array();
            foreach ($validatorRule as $key => $value) {
                if (is_int($key)) {
                    $validatorList[] = $value;
                }
            }

            // set defaults
            if (!isset($validatorRule[self::BREAK_CHAIN])) {
                $validatorRule[self::BREAK_CHAIN] = false;
            }
            if (!isset($validatorRule[self::FIELD])) {
                $validatorRule[self::FIELD] = $ruleName;
            }
            if (!isset($validatorRule[self::PRESENCE])) {
                $validatorRule[self::PRESENCE] = self::PRESENCE_OPTIONAL;
            }

            $validatorChain = new Zend_Validate();
            foreach ($validatorList as $validator) {
                if (is_string($validator)) {
                    $validator = $this->_getValidator($validator);
                }
                if (!($validator && $validator instanceof Zend_Validate_Interface)) {
                    require_once 'Zend/Validate/Exception.php';
                    throw new Zend_Validate_Exception('Expected object implementing Zend_Validate_Interface, got '.get_class($validator));
                }
                $validatorChain->addValidator($validator, $validatorRule[self::BREAK_CHAIN]);
            }

            $field = $validatorRule[self::FIELD];

            if (!isset($this->_data[$field]) && $validatorRule[self::PRESENCE] == self::PRESENCE_REQUIRED) {
                $this->_missingFields[$field][] = "Field '$field' is required by rule $ruleName, but field is missing.";
                continue;
            }

            // @todo: support multi-valued data inputs
            if (!$validatorChain->isValid($this->_data[$field])) {
                $this->_invalidFields[$field] = array_merge($this->_invalidFields, $validatorChain->getMessages());
                continue;
            }

            $this->_validFields[$field] = $this->_data[$field];
        }

        /**
         * Unset fields in $_data that have been added to other arrays.
         * We have to wait until all rules have been processed because
         * a given field may be referenced by multiple rules.
         */
        foreach (array_merge(
            array_keys($this->_validFields),
            array_keys($this->_invalidFields),
            array_keys($this->_missingFields)) as $key) {
            unset($this->_data[$key]);
        }

        /**
         * Anything left over in $_data is an unknown field.
         */
        $this->_unknownFields = $this->_data;
    }

    /**
     * @param string $classBaseName
     * @return Zend_Filter_Interface or null
     */
    protected function _getFilter($classBaseName)
    {
        return $this->_getFilterOrValidator('Zend_Filter_Interface', $classBaseName);
    }

    /**
     * @param string $classBaseName
     * @return Zend_Validate_Interface or null
     */
    protected function _getValidator($classBaseName)
    {
        return $this->_getFilterOrValidator('Zend_Validate_Interface', $classBaseName);
    }

    /**
     * @param string $derivedFrom
     * @param string $classBaseName
     * @return mixed
     */
    protected function _getFilterOrValidator($derivedFrom, $classBaseName)
    {
        foreach ($this->_namespaces as $namespace) {
            $className = $namespace . '_' . ucfirst($classBaseName);
            try {
                Zend_Loader::loadClass($className);
                $object = new $className();
                if ($object instanceof $derivedFrom) {
                    return $object;
                }
            } catch (Zend_Exception $e) {
                // fallthrough and continue
            }
        }
        return null;
    }

}

