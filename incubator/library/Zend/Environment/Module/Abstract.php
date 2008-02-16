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
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Exception.php 2794 2007-01-16 01:29:51Z bkarwin $
 */


/**
 * Zend_Environment_Field
 */
require_once 'Zend/Environment/Field.php';


/**
 * Zend_Environment_Module_Interface
 */
require_once 'Zend/Environment/Module/Interface.php';


/**
 * Zend_Environment_Container_Abstract
 */
require_once 'Zend/Environment/Container/Abstract.php';


/**
 * @category   Zend
 * @package    Zend_Environment
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Environment_Module_Abstract extends Zend_Environment_Container_Abstract implements Zend_Environment_Module_Interface
{
    /**
     * Constants to Zend Framework / PHP paths
     */
    const PATH_PHP_MANUAL = 'http://www.php.net/manual/en/';
    
    protected $_id;
    protected $_info = 'Zend_Environment_Info';
    protected $_type;
    
    /**
     * Constructor requires a unique id
     *
     * @param  string $id
     * @return void
     */
    public function __construct($id)
    {
        $this->_id = $id;
        $this->_init();
    }
    
    /**
     * The _init() method is where the module is propagated. Since the methods
     * for populating the fields can vary this is left to each concrete module
     * to implement.
     *
     * @return void
     */
    abstract protected function _init();
    
    /**
     * Convert keys to valid property ids.
     *
     * @param  string $key
     * @return string
     */
    protected function _underscore($key)
    {
        $search = array('/([^\w_]+)/', '/([a-z])([A-Z])/');
        $replace = array("_", "$1_$2");
        return strtolower(preg_replace($search, $replace, $key));
    }

    /**
     * Convert phpinfo output to an array.
     *
     * As phpinfo() output can vary from section to section, the methods for
     * extracting text vary for each. Where multiple values exists for a
     * directive within a section, these are converted to arrays.
     *
     * Directives are also converted to an underscore-based name to allow
     * legal property names.
     *
     * @param  string $section
     * @return array
     */
    protected function _parsePhpInfo($section)
    {
        $info = array();
        ob_start();
        phpinfo($section);

        $search = array('![\w\W]+?<body>([\w\W]+?)</body>[\w\W]+!mi',
                        '!<h(\d)[^>]*>(.*?)</h\\1>\s*!mi',
                        '!\s*(</tr>\s*)?<tr[^>]*>\s*!mi',
                        '!</td>\s*<td[^>]*>!mi',
                        '!<th[^>]*>(.*?)</th>!mi');
        $replace= array("\\1",
                        "___marker___\\2__section__\n",
                        "\n___row___\n",
                        "___delim___",
                        "\\1___delim___");

        $output = strip_tags(preg_replace($search, $replace, ob_get_clean()));
        $output = preg_split('!___marker___!m', $output, -1, PREG_SPLIT_NO_EMPTY);

        foreach($output as $section) {
            // Ensure section is populated
            $section = trim($section);
            if (!$section) {
                continue;
            }
            
            // Split into names and directives
            list($name, $directives) = explode('__section__', $section);
            $directives = preg_split('/___row___\s+/m', trim($directives), -1, PREG_SPLIT_NO_EMPTY);
            
            // Discard redundant header rows if found in directives
            if (count($directives) > 1) {
                array_pop($directives);
            }
            
            foreach($directives as $directive) {
                $props = preg_split('/\s*___delim___\s*/', $directive, -1, PREG_SPLIT_NO_EMPTY);
                $element = $props[0];

                if (count($props) < 2) {
                    $local  = $element;
                } else {
                    $local= $props[1];
                }

                if (count($props) < 3) {
                    $master = $local;
                } else {
                    $master = $props[2];
                }
                
                if (preg_match('!^[a-z0-9_ \.\-\(\)\[\]"]+$!i', $element)) {
                    if (!is_null($master)) {
                        $value = array($local, $master);
                    } else {
                        $value = trim($local);
                    }
                    $name = trim($this->_underscore($name));
                    $element = trim($this->_underscore($element));
                    if (!is_array($value)) {
                        $value = trim($value);
                    }
                    $info[$name][$element] = $value;
                }
            }
        }
        return $info;
    }

    /**
     * Retrieve Module id.
     *
     * @return string
     */
	public function getId()
	{
	    return $this->_id;
	}

    /**
     * Retrieve Module type.
     *
     * @return string
     */
	public function getType()
	{
	    return $this->_type;
	}
}
