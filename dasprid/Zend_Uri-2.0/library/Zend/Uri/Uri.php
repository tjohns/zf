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
    protected $_allowedComponents = array();

    /**
     * Component handlers for shared functionality
     *
     * @var array
     */
    protected $_componentHandlers = array();

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
        $this->_init();

        if (!preg_match('(^' . $this->_getSyntax() . '$)iu', $uri, $matches)) {
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

        if (!in_array($component, $this->_allowedComponents)) {
            throw new \BadMethodCallException("Component with name '$component' does not exist");
        }

        if (isset($this->_componentHandlers[$component])) {
            $handler = $this->_componentHandlers[$component];
        } else {
            $handler = null;
        }

        switch ($mode) {
            case 'get':
                if (array_key_exists($component, $this->_componentValues)) {
                    $value = $this->_componentValues[$component];
                } else {
                    $value = null;
                }

                if ($handler instanceof \Zend\Uri\Component\GetterInterface) {
                    $value = $handler->get($value);
                }

                return $value;

            case 'set':
                if (count($arguments) !== 1) {
                    throw new \BadMethodCallException("You must supply a component value");
                }

                $escaperMethod = '_escape' . $key;

                if ($handler instanceof \Zend\Uri\Component\EscaperInterface) {
                    $value = $handler->escape($arguments[0], $this->_reservedCharacters, $this->_unreservedCharacters);
                } elseif (method_exists($this, $escaperMethod)) {
                    $value = $this->{$escaperMethod}($arguments[0]);
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
     * Init hook called in the beginning of the constructor
     *
     * @return void
     */
    protected function _init()
    {
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
     * Check if a given URI is valid
     *
     * If you want to validate an URI in non-strict mode and use it afterwards,
     * it is better to use the factory() method directly in a try/catch block
     * and use the resulting URI object, as it will result in an escaped URI.
     *
     * @param  string $uri
     * @param  boolean $strict
     * @return boolean
     */
    public static function check($uri, $strict = false)
    {
        try {
            self::factory($uri, $strict);
        } catch (\Exception $e) {
            return false;
        }

        return true;
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
