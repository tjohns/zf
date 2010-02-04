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
 * @package    Zend\Uri
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Uri;

/**
 * Base URI
 *
 * @package    Zend\Uri
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Uri
{
    /**
     * Reserved characters
     *
     * @var string
     */
    protected $_reservedCharacters = ';/?:@&=+$,\\[\\]';

    /**
     * Unreserved characters
     *
     * @var string
     */
    protected $_unreservedCharacters = 'a-zA-Z0-9\-_.!~*\'()';

    /**
     * Allowed components of the URI
     * 
     * @var array
     */
    protected $_components = array();

    /**
     * Save characters of specific components
     * 
     * @var array
     */
    protected $_saveComponentCharacters = array();

    /**
     * Component values
     *
     * @var array
     */
    protected $_componentValues = array();

    /**
     * Create a new URI
     *
     * @param  string  $uri
     * @param  boolean $strict
     * @return Zend\Uri\Uri
     */
    public function __construct($uri, $strict = false)
    {
        $syntax = $this->_getSyntax();

        if (!preg_match('(^' . $syntax . '$)iu', $uri, $matches)) {
            throw new \InvalidArgumentException('URI does not seem to be valid');
        }

        foreach ($matches as $component => $value) {
            if (is_integer($component)) {
                continue;
            }

            if ($strict) {
                // Validate component
            }

            if (!empty($value)) {
                $setter = 'set' . ucfirst($component);

                $this->{$setter}($value);
            }
        }
    }

    /**
     * Overloading for setters and getters
     *
     * @param  string $name
     * @param  array $arguments
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        if (strpos($name, 'get') !== 0 && strpos($name, 'set') !== 0) {
            throw new \BadMethodCallException("Method with name '$name' does not exist");
        }

        $mode      = substr($name, 0, 3);
        $key       = substr($name, 3);
        $component = lcfirst($key);

        if (!in_array($component, $this->_components)) {
            throw new \BadMethodCallException("Component with name '$component' does not exist");
        }

        switch ($mode) {
            case 'get':
                $getter = 'get' . $key;

                if (method_exists($this, $getter)) {
                    $value = $this->{$getter}();
                } elseif (array_key_exists($component, $this->_componentValues)) {
                    $value = $this->_componentValues[$component];
                } else {
                    $value = null;
                }

                return $value;

            case 'set':
                if (count($arguments) !== 1) {
                    throw new \BadMethodCallException("You must supply a component value");
                }

                $escaper = '_escape' . $key;

                if (method_exists($this, $escaper)) {
                    $value = $this->{$escaper}($arguments[0]);
                } else {
                    if (isset($this->_saveComponentCharacters[$component])) {
                        $additionalSaveCharacters = $this->_saveComponentCharacters[$component];
                    } else {
                        $additionalSaveCharacters = null;
                    }

                    $value = $this->_escape($arguments[0], $additionalSaveCharacters);
                }

                $this->_componentValues[$component] = $value;

                return $this;
        }
    }

    /**
     * General escaping method
     *
     * @param  string $value
     * @param  string $additionalSaveCharacters
     * @return string
     */
    protected function _escape($value, $additionalSaveCharacters)
    {
        $value = preg_replace(
            '([^' . $this->_unreservedCharacters . $additionalSaveCharacters . ']+)eu',
            'rawurlencode("\\0")',
            $value
        );

        return $value;
    }

    /**
     * Create a new URI
     *
     * When $strict is set to true, all URI components will be checked to only
     * contain safe characters. Else unsafe characters will automatically be
     * escaped.
     *
     * @param  string  $uri
     * @param  boolean $strict
     * @return Zend\Uri\Uri
     */
    public static function factory($uri, $strict = false)
    {
        if (!preg_match('(^([a-z][a-z+\-.]*):)i', $uri, $matches)) {
            throw new \InvalidArgumentException('URI does not contain a scheme');
        }

        $schemeFilename = ucfirst(preg_replace('([+\-.]([a-z])?)e', 'strtoupper("\\1")', strtolower($matches[1])));
        $schemeClass    = '\\Zend\\Uri\\Scheme\\' . $schemeFilename;

        // @todo Use Zend\Loader here which also checks if the scheme exists
        require_once dirname(__FILE__) . '/Scheme/' . $schemeFilename . '.php';

        return new $schemeClass($uri, $strict);
    }

    /**
     * Magic method to convert the URI to a string
     *
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->getUri();
        } catch (\Exception $e) {
            trigger_error($e->getMessage(), E_USER_ERROR);
        }
    }

    /**
     * Get the syntax for parsing the URI
     *
     * @return string
     */
    abstract protected function _getSyntax();

    /**
     * Transform the URI to a string
     *
     * @return string
     */
    abstract public function getUri();
}
