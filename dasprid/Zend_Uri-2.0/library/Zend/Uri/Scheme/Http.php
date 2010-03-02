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
 * @package    Zend\Uri\Scheme
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @version    $Id$
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Uri\Scheme;

/**
 * HTTP URI
 *
 * @package    Zend\Uri\Scheme
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @see        http://manuals.rubyonrails.com/read/chapter/65
 */
class Http extends \Zend\Uri\Uri
{
    /**
     * Scheme of the URI, can be changed by extending classes
     * 
     * @var string
     */
    protected $_scheme = 'http';

    /**
     * @see Zend\Uri\Uri\::$_allowedComponents
     * @var array
     */
    protected $_allowedComponents = array(
        'username',
        'password',
        'host',
        'port',
        'path',
        'query',
        'fragment'
    );

    /**
     * @see Zend\Uri\Uri\::$_saveComponentCharacters
     * @var array
     */
    protected $_saveComponentCharacters = array(
        'path' => '/;'
    );

    /**
     * @see    Zend\Uri\Uri::_init()
     * @return void
     */
    protected function _init()
    {
        $this->_componentHandlers['path'] = new \Zend\Uri\Component\Path();
        $this->_componentHandlers['query'] = new \Zend\Uri\Component\Query();
    }

    /**
     * @see    Zend\Uri\Uri::_getSyntax()
     * @return string
     */
    protected function _getSyntax()
    {
        return $this->_scheme . '://'
               . '(?:'
               . '(?<username>[^:@]*)'
               . '(?::(?<password>[^@]*))?'
               . '@)?'
               . '(?<host>[^:/?#]+)'
               . '(?::(?<port>[^:/?#]+))?'
               . '(?<path>/[^?#]*)?'
               . '(?:\\?(?<query>[^#]*))?'
               . '(?:#(?<fragment>.*))?';
    }

    /**
     * @see    Zend\Uri\Uri::getUri()
     * @return string
     */
    public function getUri()
    {
        $uri = $this->_scheme . '://';

        if (($username = $this->getUsername()) !== null) {
            $uri .= $username;

            if (($password = $this->getPassword()) !== null) {
                $uri .= ':' . $password;
            }

            $uri .= '@';
        }

        $uri .= $this->getHost();

        if (($port = $this->getPort()) !== null) {
            $uri .= ':' . $port;
        }

        $uri .= $this->getPath();

        if (($query = $this->getQuery()) !== null) {
            $uri .= '?' . $query;
        }

        if (($fragment = $this->getFragment()) !== null) {
            $uri .= '#' . $fragment;
        }

        return $uri;
    }
}