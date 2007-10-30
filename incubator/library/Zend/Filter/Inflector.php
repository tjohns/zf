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
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

/** Zend_Loader_PluginLoader */
require_once 'Zend/Loader/PluginLoader.php';

/**
 * Filter chain for string inflection
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Inflector implements Zend_Filter_Interface 
{
    /**
     * @var Zend_Loader_PluginLoader_Interface
     */
    protected $_pluginLoader = null;
    
    /**
     * @var string
     */
    protected $_target = null;
    
    /**
     * @var array
     */
    protected $_rules = array();

    /**
     * Constructor
     *
     * @param string $target
     * @param array $rules
     */
    public function __construct($target = null, Array $rules = array())
    {
        if ($rules != null) {
            $this->addRules($rules);
        }
        
        if ($target != null && is_string($target)) {
            $this->setTarget($target);
        }
    }
    
    /**
     * Retreive PluginLoader
     *
     * @return Zend_Loader_PluginLoader_Interface
     */
    public function getPluginLoader()
    {
        if (!$this->_pluginLoader instanceof Zend_Loader_PluginLoader_Interface) {
            $this->_pluginLoader = new Zend_Loader_PluginLoader(array('Zend_Filter_' => 'Zend/Filter/'), __CLASS__);
        }
        
        return $this->_pluginLoader;
    }
    
    /**
     * Set PluginLoader
     *
     * @param Zend_Loader_PluginLoader_Interface $pluginLoader
     * @return Zend_Filter_Inflector
     */
    public function setPluginLoader(Zend_Loader_PluginLoader_Interface $pluginLoader)
    {
        $this->_pluginLoader = $pluginLoader;
        return $this;
    }
    
    /**
     * Convienence method to add prefix and path to PluginLoader
     *
     * @param string $prefix
     * @param string $path
     * @return Zend_Filter_Inflector
     */
    public function addFilterPrefixPath($prefix, $path)
    {
        $this->getPluginLoader()->addPrefixPath($prefix, $path);
        return $this;
    }

    /**
     * ex: 'scripts/:controller/:action.:suffix'
     * 
     * @param string
     * @return Zend_Filter_Inflector
     */
    public function setTarget($target)
    {
    	$this->_target = (string) $target;
    	return $this;
    }

    /**
     * ex:
     * array(
     *     ':controller' => array('CamelCaseToUnderscore','StringToLower'),
     *     ':action'     => array('CamelCaseToUnderscore','StringToLower'),
     *     'suffix'      => 'phtml'
     *     );
     * 
     * @param array
     * @return Zend_Filter_Inflector
     */
    public function addRules(Array $rules)
    {
        foreach ($rules as $spec => $rule) {
            if ($spec[0] == ':') {
                $this->addFilterRule($spec, $rule);
            } elseif ($spec[0] == '&') {
                $this->setStaticRuleReference($spec, $rule);
            } else {
                $this->setStaticRule($spec, $rule);
            }
        }
        
        return $this;
    }

    /**
     * Set a filtering rule for a spec.  $ruleSet can be a string, Filter object
     * or an array of strings or filter objects.
     *
     * @param string $spec
     * @param array|string|Zend_Filter_Interface $ruleSet
     * @return Zend_Filter_Inflector
     */
    public function setFilterRule($spec, $ruleSet)
    {
        $spec = ltrim($spec, ':');
        foreach ( (array) $ruleSet as $rule) {
            $this->_rules[$spec][] = $this->_getRule($rule);
        }
        return $this;
    }
    
    /**
     * Set a static rule for a spec.  This is a single string value
     *
     * @param string $name
     * @param string $value
     * @return Zend_Filter_Inflector
     */
    public function setStaticRule($name, $value)
    {
        $name = ltrim($name, ':');
        $this->_rules[$name] = (string) $value;
        return $this;
    }
    
    /**
     * Set Static Rule Reference. This allows a consuming class to pass a property or variable
     * in to be referenced when its time to build the output string from the target.
     *
     * @param string $name
     * @param mixed $reference
     * @return Zend_Filter_Inflector
     */
    public function setStaticRuleReference($name, &$reference)
    {
        $name = ltrim($name, ':&');
        $this->_rules[$name] =& $reference;
        return $this;
    }
    
    /**
     * inflect
     *
     * @param mixed $source
     * @return string
     */
    public function filter($source)
    {
        // clean source
        foreach ( (array) $source as $sourceName => $sourceValue) {
            $source[ltrim($sourceName, ':')] = $sourceValue;
        }

    	foreach ($this->_rules as $ruleName => $ruleValue) {
    	    if (isset($source[$ruleName])) {
    	        if (is_string($ruleValue)) {
    	            // overriding the set rule
    	            $processedParts['#:'.$ruleName.'#'] = $source[$ruleName];
    	        } elseif (is_array($ruleValue)) {
    	            $processedPart = $source[$ruleName];
    	            foreach ($ruleValue as $ruleFilter) {
    	                $processedPart = $ruleFilter->filter($processedPart);
    	            }
    	            $processedParts['#:'.$ruleName.'#'] = $processedPart;
    	        }
    	    } else {
                if (is_string($ruleValue)) {
                    $processedParts['#:'.$ruleName.'#'] = $ruleValue;
                } else {
                    require_once 'Zend/Filter/Exception.php';
                    throw new Zend_Filter_Exception('Rule ' . $ruleName . ' was set but not provided as part of the source of this filter.');
                }
    	    }
    	}
    	
    	return preg_replace(array_keys($processedParts), array_values($processedParts), $this->_target);
    }
    
    /**
     * protected function that will resolve string named filter names and convert them to 
     * filter objects.
     *
     * @param string $rule
     * @return Zend_Filter_Interface
     */
    protected function _getRule($rule)
    {
        if ($rule instanceof Zend_Filter_Interface) {
            return $rule;
        }
        
        $rule = (string) $rule;
        
        $className = $this->getPluginLoader()->load($rule);
        $ruleObject = new $className();
        if ($ruleObject instanceof Zend_Filter_Interface) {
            return $ruleObject;
        }
        
        require_once 'Zend/Filter/Exception.php';
        throw new Zend_Filter_Exception('No class named ' . $rule . ' implementing Zend_Filter_Interface could be found.');
    }
}
