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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Filter_Hash_Interface
 */
require_once 'Zend/Filter/Hash/Interface.php';

/**
 * Hashing adapter for hash
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Hash_Hash implements Zend_Filter_Hash_Interface
{
    /**
     * Definitions for hashing
     * array(
     *     'key' => salt key
     *     'algorithm' => algorithm to use
     * )
     */
    protected $_options = array(
        'key'       => null,
        'algorithm' => 'md5',
    );

    /**
     * Class constructor
     *
     * @param string|array|Zend_Config $options Hash Options
     */
    public function __construct($options)
    {
        if (!extension_loaded('hash')) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('This filter needs the hash extension');
        }

        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (is_string($options)) {
            $options = array('algorithm' => $options);
        } elseif (!is_array($options)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Invalid options argument provided to filter');
        }

        $this->setOptions($options);
    }

    /**
     * Returns the set hash options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets new hash options
     *
     * @param  string|array $options Hashing options
     * @return Zend_Filter_Hash_Hash
     */
    public function setOptions($options)
    {
        if (is_string($options)) {
            $options = array('algorithm' => $options);
        }

        if (!is_array($options)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Invalid options argument provided to filter');
        }

        $options = $options + $this->getOptions();
        $algorithms = hash_algos();
        if (!in_array($options['algorithm'], $algorithms)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("The algorithm '{$options['algorithm']}' is not supported");
        }

        $this->_options = $options;
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Hashes $value with the defined settings
     *
     * @param  string $value The content to hash
     * @return string The hashed content
     */
    public function hash($value)
    {
        $options = $this->getOptions();
        if (empty($options['key'])) {
            $hashed = hash($options['algorithm'], $value);
        } else {
            $hashed = hash_hmac($options['algorithm'], $options['key'], $value);
        }

        return $hashed;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Hash';
    }
}
