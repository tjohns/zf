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
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/** Zend_Loader */
require_once 'Zend/Loader.php';

/**
 * View script inflection
 *
 * Zend_View_Inflector is used to resolve path names for view, partial, and 
 * layout scripts.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Inflector
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_View_Inflector
{
    /**
     * Default inflection rule to use
     * @var string
     */
    protected $_defaultRule = 'controllerAction';

    /**
     * Default rule path
     * @var array
     */
    protected $_defaultRulePath = array(
        'prefix' => 'Zend_View_Inflector_Rule', 
        'path'   => 'Zend/View/Inflector/Rule'
    );

    /**
     * Array of potential paths to search for inflection rules
     * @var array
     */
    protected $_rulePaths = array();

    /**
     * Array of loaded rules
     * @var array
     */
    protected $_loadedRules = array();

    /**
     * Constructor
     * 
     * @return void
     */
    public function __construct()
    {
        $this->initRulePath();
    }

    /**
     * Retrieve default rule
     *
     * @return string
     */
    public function getDefaultRule()
    {
        return $this->_defaultRule;
    }

    /**
     * Set defaultRule
     *
     * @param  string $value
     * @return Zend_View_Inflector
     */
    public function setDefaultRule($value)
    {
        $this->_defaultRule = (string) $value;
        return $this;
    }

    /**
     * Retrieve rule paths
     *
     * @return array
     */
    public function getRulePath()
    {
        return $this->_rulePaths;
    }

    /**
     * Initialize rule path
     * 
     * @return Zend_View_Inflecotr
     */
    public function initRulePath()
    {
        $this->_rulePaths = array($this->_defaultRulePath);
        return $this;
    }

    /**
     * Set rule path (overwrites)
     *
     * @param  string|array $paths
     * @param  string $prefix Class prefix to use for this path
     * @return Zend_View_Inflector
     */
    public function setRulePath($spec, $prefix = 'Zend_View_Inflector_Rule')
    {
        $spec   = (array) $spec;
        $this->initRulePath();

        $paths  = $this->_rulePaths;
        $prefix = $this->normalizePrefix($prefix);
        foreach ($spec as $key => $path) {
            $curPrefix = (is_numeric($key)) ? $prefix : $key;
            $this->addRulePath($path, $curPrefix);
        }

        return $this;
    }

    /**
     * Add another path to the rules path
     * 
     * @param  string $path 
     * @param  string $prefix Class prefix to use for this path
     * @return Zend_View_Inflector
     */
    public function addRulePath($path, $prefix = 'Zend_View_Inflector_Rule')
    {
        $this->_rulePaths[] = array(
            'prefix' => $this->normalizePrefix($prefix), 
            'path'   => $this->normalizePath($path)
        );
        return $this;
    }

    /**
     * Normalize class prefix
     * 
     * @param  string $prefix 
     * @return string
     */
    public function normalizePrefix($prefix) 
    {
        return rtrim($prefix, '_');
    }

    /**
     * Normalize path
     *
     * Trim off trailing directory separator
     * 
     * @param  string $path 
     * @return string
     */
    public function normalizePath($path)
    {
        $path = rtrim((string) $path, '/');
        $path = rtrim($path, '\\');
        return $path;
    }

    /**
     * Inflect a path
     * 
     * @param  string $path 
     * @param  string $ruleName 
     * @return string
     */
    public function inflect($path, $ruleName = null)
    {
        if (null == $ruleName) {
            $ruleName = $this->getDefaultRule();
        }

        $ruleClass = $this->loadRuleClass($ruleName);
        $rule      = new $ruleClass;
        return $rule->inflect($path);
    }

    /**
     * Load rule class
     *
     * Attempts to load a rule class. If previously loaded, simply returns the 
     * class name as previously discovered. Otherwise, searches through the 
     * paths to find the rule class.
     * 
     * @param  string $rule Rule name
     * @return string Class name
     * @throws Zend_View_Inflector_Exception when rule class not found
     */
    public function loadRuleClass($rule)
    {
        $rule  = ucfirst($rule);
        if (isset($this->_loadedRules[$rule])) {
            return $this->_loadedRules[$rule];
        }

        $class = null;
        $found = false;

        foreach (array_reverse($this->_rulePaths) as $spec) {
            $prefix   = $spec['prefix'];
            $path     = $spec['path'];
            $class    = $this->getRuleClass($rule, $prefix);

            if (class_exists($class)) {
                $found = true;
                break;
            }

            $filePath = $this->getRulePathSpec($rule, $path);
            $pos      = strrpos($filePath, DIRECTORY_SEPARATOR) + 1;
            $pathDir  = substr($filePath, 0, $pos);
            $pathFile = substr($filePath, $pos);
            if (Zend_Loader::isReadable($filePath)) {
                Zend_Loader::loadFile($pathFile, $pathDir);
                if (class_exists($class)) {
                    $found = true;
                    break;
                }
            }
        }

        if (!$found) {
            require_once 'Zend/View/Inflector/Exception.php';
            throw new Zend_View_Inflector_Exception(sprintf('View inflector rule "%s" not found', $rule));
        }

        $this->_loadedRules[$rule] = $class;
        return $class;
    }

    /**
     * Get the rule class based on the rule name and specified prefix
     * 
     * @param  string $rule 
     * @param  string $prefix 
     * @return string
     */
    public function getRuleClass($rule, $prefix)
    {
        return $prefix . '_' . $rule;
    }

    /**
     * Get the rule class path specification
     * 
     * @param  string $rule 
     * @param  string $path 
     * @return string
     */
    public function getRulePathSpec($rule, $path)
    {
        return $path . DIRECTORY_SEPARATOR . $rule . '.php';
    }
}
