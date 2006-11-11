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
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * Zend_View_Exception
 */
require_once 'Zend/View/Exception.php';

/**
 * Zend_View_Interface
 */
require_once 'Zend/View/Interface.php';


/**
 * Abstract class for Zend_View to help enforce private constructs.
 *
 * @category   Zend
 * @package    Zend_View
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_View_Abstract implements Zend_View_Interface
{

    /**
     * Assigned variables.
     *
     * @var array
     */
    private $_vars = array();

    /**
     * Path stack for script, helper, and filter directories.
     *
     * @var array
     */
    private $_path = array(
        'script' => array(),
        'helper' => array(),
        'filter' => array(),
    );

    /**
     * Script file name to execute
     *
     * @var string
     */
    private $_file = null;

    /**
     * Instances of helper objects.
     *
     * @var array
     */
    private $_helper = array();

    /**
     * Stack of Zend_View_Filter names to apply as filters.
     *
     * @var array
     */
    private $_filter = array();

    /**
     * Callback for escaping.
     *
     * @var string
     */
    private $_escape = 'htmlspecialchars';

    /**
     * Constructor.
     *
     * @param array $config Configuration key-value pairs.
     */
    public function __construct($config = array())
    {
        // set inital paths
        $this->setScriptPath(null);
        $this->setHelperPath(null);
        $this->setFilterPath(null);

        // user-defined escaping callback
        if (array_key_exists('escape', $config)) {
            $this->setEscape($config['escape']);
        }

        // user-defined view script path
        if (array_key_exists('scriptPath', $config)) {
            $this->addScriptPath($config['scriptPath']);
        }

        // user-defined helper path
        if (array_key_exists('helperPath', $config)) {
            $this->addHelperPath($config['helperPath']);
        }

        // user-defined filter path
        if (array_key_exists('filterPath', $config)) {
            $this->addFilterPath($config['filterPath']);
        }

        // user-defined filters
        if (array_key_exists('filter', $config)) {
            $this->addFilter($config['filter']);
        }
    }

    /**
     * Return the template engine object
     *
     * Returns the object instance, as it is its own template engine
     * 
     * @return self
     */
    public function getEngine()
    {
        return $this;
    }

    /**
     * Directly assigns a variable to the view script.
     *
     * Note that variable names may not be prefixed with '_'.
     *
     * @param string $key The variable name.
     * @param mixed $val The variable value.
     * @return void
     */
    public function __set($key, $val)
    {
        /**
         * @todo exception?
         */
        if ($key[0] != '_') {
            $this->_vars[$key] = $val;
        }
    }

    /**
     * Retrieves an assigned variable.
     *
     * Note that variable names may not be prefixed with '_'.
     *
     * @param string $key The variable name.
     * @return mixed The variable value.
     */
    public function __get($key)
    {
        /**
         * @todo exception?
         */
        if ($this->__isset($key)) {
            return $this->_vars[$key];
        }

        return null;
    }


    /**
     * Allows testing with empty() and isset() to work inside
     * templates -- only available on PHP 5.1
     *
     * @param  string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->_vars) && ($key[0] != '_');
    }

    /**
     * Allows unset() on object properties to work
     *
     * @param string $key
     * @return void
     */
    public function __unset($key)
    {
        if ($this->__isset($key)) {
            unset($this->_vars[$key]);
        }
    }

    /**
     * Accesses a helper object from within a script.
     *
     * @param string $name The helper name.
     * @param array $args The parameters for the helper.
     * @return string The result of the helper output.
     */
    public function __call($name, $args)
    {
        // is the helper already loaded?
        if (empty($this->_helper[$name])) {
            // load class and create instance
            $class = $this->_loadClass('helper', $name);
            $this->_helper[$name] = new $class();
        }

        // call the helper method
        return call_user_func_array(
            array($this->_helper[$name], $name),
            $args
        );
    }

    /**
     * Adds to the stack of view script paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @return void
     */
    public function addScriptPath($path)
    {
        $this->_addPath('script', $path);
    }

    /**
     * Resets the stack of view script paths.
     *
     * To clear all paths, use Zend_View::setScriptPath(null).
     *
     * @param string|array The directory (-ies) to set as the path.
     * @return void
     */
    public function setScriptPath($path)
    {
        $this->_path['script'] = array();
        $this->_addPath('script', $path);
    }

    /**
     * Returns an array of all currently set script paths
     * 
     * @return array
     */
    public function getScriptPaths()
    {
        return $this->_getPaths('script');
    }

    /**
     * Adds to the stack of helper paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @return void
     */
    public function addHelperPath($path)
    {
        $this->_addPath('helper', $path);
    }

    /**
     * Resets the stack of helper paths.
     *
     * To clear all paths, use Zend_View::setHelperPath(null).
     *
     * @param string|array The directory (-ies) to set as the path.
     * @return void
     */
    public function setHelperPath($path)
    {
        $this->_setPath('helper', $path);
    }

    /**
     * Returns an array of all currently set helper paths
     * 
     * @return array
     */
    public function getHelperPaths()
    {
        return $this->_getPaths('helper');
    }

    /**
     * Adds to the stack of filter paths in LIFO order.
     *
     * @param string|array The directory (-ies) to add.
     * @return void
     */
    public function addFilterPath($path)
    {
        $this->_addPath('filter', $path);
    }

    /**
     * Resets the stack of filter paths.
     *
     * To clear all paths, use Zend_View::setFilterPath(null).
     *
     * @param string|array The directory (-ies) to set as the path.
     * @return void
     */
    public function setFilterPath($path)
    {
        $this->_setPath('filter', $path);
    }

    /**
     * Returns an array of all currently set filter paths
     * 
     * @return array
     */
    public function getFilterPaths()
    {
        return $this->_getPaths('filter');
    }

    /**
     * Add one or more filters to the stack in FIFO order.
     *
     * @param string|array One or more filters to add.
     * @return void
     */
    public function addFilter($name)
    {
        foreach ((array) $name as $val) {
            $this->_filter[] = $val;
        }
    }

    /**
     * Resets the filter stack.
     *
     * To clear all filters, use Zend_View::setFilter(null).
     *
     * @param string|array One or more filters to set.
     * @return void
     */
    public function setFilter($name)
    {
        $this->_filter = array();
        $this->addFilter($name);
    }

    /**
     * Sets the _escape() callback.
     *
     * @param mixed $spec The callback for _escape() to use.
     * @return void
     */
    public function setEscape($spec)
    {
        $this->_escape = $spec;
    }

    /**
     * Assigns variables to the view script via differing strategies.
     *
     * Zend_View::assign('name', $value) assigns a variable called 'name'
     * with the corresponding $value.
     *
     * Zend_View::assign($array) assigns the array keys as variable
     * names (with the corresponding array values).
     *
     * @param string|array The assignment strategy to use.
     * @param mixed (Optional) If assigning a named variable, use this
     * as the value.
     * @return void
     * @see __set()
     */
    public function assign($spec, $value = null)
    {
        // which strategy to use?
        if (is_string($spec)) {
            // assign by name and value
            $this->_vars[$spec] = $value;
        } elseif (is_array($spec)) {
            // assign from associative array
            foreach ($spec as $key => $val) {
                $this->_vars[$key] = $val;
            }
        } else {
            throw new Zend_View_Exception('assign() expects a string or array, received ' . gettype($spec));
        }
    }

    /**
     * Clear all assigned variables
     *
     * Clears all variables assigned to Zend_View either via {@link assign()} or 
     * property overloading ({@link __get()}/{@link __set()}).
     * 
     * @return void
     */
    public function clearVars()
    {
        $this->_vars = array();
    }

    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        // find the script file name using the parent private method
        $this->_file = $this->_script($name);
        unset($name); // remove $name from local scope

        ob_start();
        $this->_run($this->_file); 

        return $this->_filter(ob_get_clean()); // filter output
    }

    /**
     * Escapes a value for output in a view script.
     *
     * If escaping mechanism is one of htmlspecialchars or htmlentities, UTF-8 
     * encoding is assumed for escaping purposes.
     *
     * @param mixed $var The output to escape.
     * @return mixed The escaped value.
     */
    public function escape($var)
    {
        if (in_array($this->_escape, array('htmlspecialchars', 'htmlentities'))) {
            return call_user_func($this->_escape, $var, ENT_COMPAT, 'UTF-8');
        }

        return call_user_func($this->_escape, $var);
    }

    /**
     * Finds a view script from the available directories.
     *
     * @param $name string The base name of the script.
     * @return void
     */
    protected function _script($name)
    {
        if (0 == count($this->_path['script'])) {
            throw new Zend_View_Exception('no view script directory set; unable to determine location for view script');
        }

        foreach ($this->_path['script'] as $dir) {
            if (is_readable($dir . $name)) {
                return $dir . $name;
            }
        }

        throw new Zend_View_Exception("script '$name' not found in path");
    }

    /**
     * Applies the filter callback to a buffer.
     *
     * @param string $buffer The buffer contents.
     * @return string The filtered buffer.
     */
    private function _filter($buffer)
    {
        // loop through each filter class
        foreach ($this->_filter as $name) {
            // load and apply the filter class
            $class = $this->_loadClass('filter', $name);
            $buffer = call_user_func(array($class, 'filter'), $buffer);
        }

        // done!
        return $buffer;
    }

    /**
     * Adds paths to the path stack in LIFO order.
     *
     * Zend_View::_addPath($type, 'dirname') adds one directory
     * to the path stack.
     *
     * Zend_View::_addPath($type, $array) adds one directory for
     * each array element value.
     *
     * @param string $type The path type ('script', 'helper', or 'filter').
     * @param string|array $path The path specification.
     * @return void
     */
    private function _addPath($type, $path)
    {
        // add the path to the stack
        foreach ((array) $path as $dir) {
        	// attempt to strip any possible separator and
        	// append the system directory separator
            $dir = rtrim($dir, '\\/' . DIRECTORY_SEPARATOR) 
                 . DIRECTORY_SEPARATOR;
            
            // add to the top of the stack.
            array_unshift($this->_path[$type], $dir);
        }
    }

    /**
     * Resets the path stack for helpers and filters.
     *
     * @param string $type The path type ('helper' or 'filter').
     * @param string|array $path The directory (-ies) to set as the path.
     */
    private function _setPath($type, $path)
    {
        $dir = DIRECTORY_SEPARATOR . ucfirst($type) . DIRECTORY_SEPARATOR;
        $this->_path[$type] = array(dirname(__FILE__) . $dir);
        $this->_addPath($type, $path);
    }

    /**
     * Return all paths for a given path type
     * 
     * @param string $type The path type  ('helper', 'filter', 'script')
     * @return array
     */
    private function _getPaths($type)
    {
        return $this->_path[$type];
    }

    /**
     * Loads a helper or filter class.
     *
     * @param string $type The class type ('helper' or 'filter').
     * @param string $name The base name.
     * @param string The full class name.
     */
    private function _loadClass($type, $name)
    {
        // from $type & $name to Zend_View_$Type_$Name
        // (note the case changes)
        $class = 'Zend_View_' . ucfirst($type) . '_' . ucfirst($name);

        // if the class does not exist, attempt to load it from the path stack
        if (class_exists($class, false)) {
        	return $class;
        }
        
        // only look for "$Name.php"
        $file = ucfirst($name) . '.php';
        foreach ($this->_path[$type] as $dir) {
            if (is_readable($dir. $file)) {
                include $dir . $file;
                
                if (! class_exists($class, false)) {
                	$msg = "$type '$name' loaded but class '$class' not found within";
                	throw new Zend_View_Exception($msg);
                }
                
                return $class;
            }
        }
        
        throw new Zend_View_Exception("$type '$name' not found in path.");
    }

    /**
     * Use to include the view script in a scope that only allows public 
     * members.
     * 
     * @return mixed
     */
    abstract protected function _run();
}
