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
 * @version    $Id:$
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

    const ALLOW_EMPTY       = 'allowEmpty';
    const BREAK_CHAIN       = 'breakChainOnFailure';
    const ESCAPE_FILTER     = 'escapeFilter';
    const FIELDS            = 'fields';
    const FILTER_CHAIN      = 'filterChain';
    const NAMESPACE         = 'namespace';
    const PRESENCE          = 'presence';
    const PRESENCE_OPTIONAL = 'optional';
    const PRESENCE_REQUIRED = 'required';
    const RULE              = 'rule';
    const RULE_WILDCARD     = '*';
    const VALIDATOR         = 'validator';
    const VALIDATOR_CHAIN   = 'validatorChain';

    /**
     * @var array Input data, before processing.
     */
    protected $_data = array();

    /**
     * @var array Association of rules to filters.
     */
    protected $_filterRules = array();

    /**
     * @var array Association of rules to validators.
     */
    protected $_validatorRules = array();

    /**
     * @var array After processing data, this contains mapping of valid fields
     * to field values.
     */
    protected $_validFields = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules that did not pass validation to the array of messages returned
     * by the validator chain.
     */
    protected $_invalidFields = array();

    /**
     * @var array After processing data, this contains mapping of validation
     * rules in which some fields were missing to the array of messages
     * indicating which fields were missing.
     */
    protected $_missingFields = array();

    /**
     * @var array After processing, this contains a copy of $_data elements
     * that were not mentioned in any validation rule.
     */
    protected $_unknownFields = array();

    /**
     * @var array Default namespaces, to search after user-defined namespaces.
     */
    protected $_namespaces = array('Zend_Filter', 'Zend_Validate');

    /**
     * @var array User-defined namespaces, to search before $_namespaces.
     */
    protected $_userNamespaces = array();

    /**
     * @var Zend_Filter_Interface The filter object that is run on values
     * returned by the getEscaped() method.
     */
    protected $_defaultEscapeFilter = null;

    /**
     * @var array Default values to use when processing filters and validators.
     */
    protected $_defaults = array(
        self::ALLOW_EMPTY   => false,
        self::BREAK_CHAIN   => false,
        self::ESCAPE_FILTER => 'HtmlEntities',
        self::PRESENCE      => self::PRESENCE_OPTIONAL
    );

    /**
     * @var boolean Set to False initially, this is set to True after the
     * input data have been processed.  Reset to False in setData() method.
     */
    protected $_processed = false;

    /**
     * @param array $filters
     * @param array $validators
     * @param array $data       OPTIONAL
     * @param array $options    OPTIONAL
     */
    public function __construct($filterRules, $validatorRules, array $data = null, array $options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->_filterRules = (array) $filterRules;
        $this->_validatorRules = (array) $validatorRules;

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
        $this->_namespaces = array_merge(
            $this->_userNamespaces,
            array('Zend_Filter', 'Zend_Validate')
        );
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
            if (is_array($this->_validFields[$fieldName])) {
                return array_map(array($escapeFilter, 'filter'), $this->_validFields[$fieldName]);
            } else {
                return $escapeFilter->filter($this->_validFields[$fieldName]);
            }
        } else {
            return null;
        }
    }

    /**
     * @param string $fieldName
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
     * @param string $fieldName
     * @return string
     */
    public function __get($fieldName)
    {
        return $this->getEscaped($fieldName);
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
     * @param string $fieldName
     * @return boolean
     */
    public function __isset($fieldName)
    {
        $this->_process();
        return isset($this->_validFields[$fieldName]);
    }

    /**
     * @return void
     * @throw Zend_Filter_Exception
     */
    public function process()
    {
        $this->_process();
        if ($this->hasInvalid()) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("Input has invalid fields");
        }
        if ($this->hasMissing()) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("Input has missing fields");
        }
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->_data = $data;

        /**
         * Reset to initial state
         */
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
        if (!$escapeFilter instanceof Zend_Filter_Interface) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Escape filter specified does not implement Zend_Filter_Interface');
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
            switch ($option) {
                case self::ESCAPE_FILTER:
                    $this->setDefaultEscapeFilter($value);
                    break;
                case self::NAMESPACE:
                    $this->addNamespace($value);
                    break;
                case self::ALLOW_EMPTY:
                case self::BREAK_CHAIN:
                case self::PRESENCE:
                    $this->_defaults[$option] = $value;
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
    protected function _filter()
    {
        foreach ($this->_filterRules as $ruleName => &$filterRule) {
            /**
             * Make sure we have an array representing this filter chain.
             * Don't typecast to (array) because it might be a Zend_Filter object
             */
            if (!is_array($filterRule)) {
                $filterRule = array($filterRule);
            }

            /**
             * Filters are indexed by integer, metacommands are indexed by string.
             * Pick out the filters.
             */
            $filterList = array();
            foreach ($filterRule as $key => $value) {
                if (is_int($key)) {
                    $filterList[] = $value;
                }
            }

            /**
             * Use defaults for filter metacommands.
             */
            $filterRule[self::RULE] = $ruleName;
            if (!isset($filterRule[self::FIELDS])) {
                $filterRule[self::FIELDS] = $ruleName;
            }

            /**
             * Load all the filter classes and add them to the chain.
             */
            if (!isset($filterRule[self::FILTER_CHAIN])) {
                $filterRule[self::FILTER_CHAIN] = new Zend_Filter();
                foreach ($filterList as $filter) {
                    if (is_string($filter)) {
                        $filter = $this->_getFilter($filter);
                    }
                    $filterRule[self::FILTER_CHAIN]->addFilter($filter);
                }
            }

            /**
             * If the ruleName is the special wildcard rule,
             * then apply the filter chain to all input data.
             * Else just process the field named by the rule.
             */
            if ($ruleName == self::RULE_WILDCARD) {
                foreach (array_keys($this->_data) as $field)  {
                    $this->_filterRule(array_merge($filterRule, array(self::FIELDS => $field)));
                }
            } else {
                $this->_filterRule($filterRule);
            }
        }
    }

    /**
     * @param array $filterRule
     * @return void
     */
    protected function _filterRule(array $filterRule)
    {
        $field = $filterRule[self::FIELDS];
        if (is_array($this->_data[$field])) {
            foreach ($this->_data[$field] as $key => $value) {
                $this->_data[$field][$key] = $filterRule[self::FILTER_CHAIN]->filter($value);
            }
        } else {
            if (isset($this->_data[$field])) {
                $this->_data[$field] =
                    $filterRule[self::FILTER_CHAIN]->filter($this->_data[$field]);
            }
        }
    }

    /**
     * @return Zend_Filter_Interface
     */
    protected function _getDefaultEscapeFilter()
    {
        if ($this->_defaultEscapeFilter !== null) {
            return $this->_defaultEscapeFilter;
        }
        return $this->setDefaultEscapeFilter($this->_defaults[self::ESCAPE_FILTER]);
    }

    /**
     * @return void
     */
    protected function _process()
    {
        if ($this->_processed === false) {
            $this->_filter();
            $this->_validate();
            $this->_processed = true;
        }
    }

    /**
     * @return void
     */
    protected function _validate()
    {
        /**
         * Special case: if there are no validators, treat all fields as valid.
         */
        if (!$this->_validatorRules) {
            $this->_validFields = $this->_data;
            $this->_data = array();
            return;
        }

        foreach ($this->_validatorRules as $ruleName => &$validatorRule) {
            /**
             * Make sure we have an array representing this validator chain.
             * Don't typecast to (array) because it might be a Zend_Validate object
             */
            if (!is_array($validatorRule)) {
                $validatorRule = array($validatorRule);
            }

            /**
             * Validators are indexed by integer, metacommands are indexed by string.
             * Pick out the validators.
             */
            $validatorList = array();
            foreach ($validatorRule as $key => $value) {
                if (is_int($key)) {
                    $validatorList[] = $value;
                }
            }

            /**
             * Use defaults for validation metacommands.
             */
            $validatorRule[self::RULE] = $ruleName;
            if (!isset($validatorRule[self::FIELDS])) {
                $validatorRule[self::FIELDS] = $ruleName;
            }
            if (!isset($validatorRule[self::BREAK_CHAIN])) {
                $validatorRule[self::BREAK_CHAIN] = $this->_defaults[self::BREAK_CHAIN];
            }
            if (!isset($validatorRule[self::PRESENCE])) {
                $validatorRule[self::PRESENCE] = $this->_defaults[self::PRESENCE];
            }
            if (!isset($validatorRule[self::ALLOW_EMPTY])) {
                $validatorRule[self::ALLOW_EMPTY] = $this->_defaults[self::ALLOW_EMPTY];
            }

            /**
             * Load all the validator classes and add them to the chain.
             */
            if (!isset($validatorRule[self::VALIDATOR_CHAIN])) {
                $validatorRule[self::VALIDATOR_CHAIN] = new Zend_Validate();
                foreach ($validatorList as $validator) {

                    if (is_string($validator)) {
                        $validator = $this->_getValidator($validator);
                    }
                    $validatorRule[self::VALIDATOR_CHAIN]->addValidator($validator, $validatorRule[self::BREAK_CHAIN]);
                }
            }

            /**
             * If the ruleName is the special wildcard rule,
             * then apply the validator chain to all input data.
             * Else just process the field named by the rule.
             */
            if ($ruleName == self::RULE_WILDCARD) {
                foreach (array_keys($this->_data) as $field)  {
                    $this->_validateRule(array_merge($validatorRule, array(self::FIELDS => $field)));
                }
            } else {
                $this->_validateRule($validatorRule);
            }
        }

        /**
         * Unset fields in $_data that have been added to other arrays.
         * We have to wait until all rules have been processed because
         * a given field may be referenced by multiple rules.
         */
        foreach (array_keys($this->_missingFields) + array_keys($this->_invalidFields) as $rule) {
            foreach ((array) $this->_validatorRules[$rule][self::FIELDS] as $field) {
                unset($this->_data[$field]);
            }
        }
        foreach ($this->_validFields as $field => $value) {
            unset($this->_data[$field]);
        }

        /**
         * Anything left over in $_data is an unknown field.
         */
        $this->_unknownFields = $this->_data;
    }

    /**
     * @param array $validatorRule
     * @return void
     */
    protected function _validateRule(array $validatorRule)
    {
        /**
         * Get one or more data values from input, and check for missing fields.
         */
        $data = array();
        foreach ((array) $validatorRule[self::FIELDS] as $field) {
            if (!isset($this->_data[$field])) {
                if ($validatorRule[self::PRESENCE] == self::PRESENCE_REQUIRED) {
                    $this->_missingFields[$validatorRule[self::RULE]][] =
                        "Field '$field' is required by rule '"
                        . $validatorRule[self::RULE]
                        . "', but the field is missing";
                }
                continue;
            }
            $data[$field] = $this->_data[$field];
        }

        /**
         * If any required fields are missing, break the loop.
         */
        if (count($this->_missingFields[$validatorRule[self::RULE]]) > 0) {
            return;
        }

        /**
         * Evaluate the inputs against the validator chain.
         */
        if (count((array) $validatorRule[self::FIELDS]) > 1) {
            if (!$validatorRule[self::VALIDATOR_CHAIN]->isValid($data)) {
                $this->_invalidFields[$validatorRule[self::RULE]] = $validatorRule[self::VALIDATOR_CHAIN]->getMessages();
                return;
            }
        } else {
            $failed = false;
            foreach ($data as $fieldKey => $field) {
                foreach ((array) $field as $value) {
                    if (empty($value)) {
                        if ($validatorRule[self::ALLOW_EMPTY] == true) {
                            continue;
                        }
                    }
                    if (!$validatorRule[self::VALIDATOR_CHAIN]->isValid($value)) {
                        $this->_invalidFields[$validatorRule[self::RULE]] =
                            array_merge(
                                (array) $this->_invalidFields[$validatorRule[self::RULE]],
                                $validatorRule[self::VALIDATOR_CHAIN]->getMessages()
                            );
                        unset($this->_validFields[$fieldKey]);
                        $failed = true;
                        if ($validatorRule[self::BREAK_CHAIN]) {
                            return;
                        }
                    }
                }
            }
            if ($failed) {
                return;
            }
        }

        /**
         * If we got this far, the inputs for this rule pass validation.
         */
        foreach ((array) $validatorRule[self::FIELDS] as $field) {
            $this->_validFields[$field] = $this->_data[$field];
        }
    }

    /**
     * @param string $classBaseName
     * @return Zend_Filter_Interface
     */
    protected function _getFilter($classBaseName)
    {
        return $this->_getFilterOrValidator('Zend_Filter_Interface', $classBaseName);
    }

    /**
     * @param string $classBaseName
     * @return Zend_Validate_Interface
     */
    protected function _getValidator($classBaseName)
    {
        return $this->_getFilterOrValidator('Zend_Validate_Interface', $classBaseName);
    }

    /**
     * @param string $derivedFrom
     * @param string $classBaseName
     * @return mixed
     * @throws Zend_Filter_Exception
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
        require_once 'Zend/Filter/Exception.php';
        throw new Zend_Filter_Exception("Could not find a class based on name '$classBaseName' extending $derivedFrom");
    }

}

