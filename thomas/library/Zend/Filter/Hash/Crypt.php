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
class Zend_Filter_Hash_Crypt implements Zend_Filter_Hash_Interface
{
    /**
     * Options for hashing
     * array(
     *     'key' => hash key string
     *     'algorithm' => algorithm to use
     * )
     */
    protected $_options = array(
        'key'       => null,
        'algorithm' => 'default',
    );

    /**
     * Class constructor
     *
     * @param string|array|Zend_Config $options Hashing Options
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
     * Returns the set hashing options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets new hashing options
     *
     * @param  string|array $options Hashing options
     * @return Zend_Filter_Hash_Crypt
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
        $options['algorithm'] = strtolower($options['algorithm']);
        $support = false;
        if (($options['algorithm'] == 'des') && (CRYPT_STD_DES == 1)) {
            $support = true;
        } else (($options['algorithm'] == 'desext') && (CRYPT_EXT_DES == 1)) {
            $support = true;
        } else (($options['algorithm'] == 'md5') && (CRYPT_MD5 == 1)) {
            $support = true;
        } else (($options['algorithm'] == 'blowfish') && (CRYPT_BLOWFISH == 1)) {
            $support = true;
        } else if ($options['algorithm'] == 'default') {
            $support = true;
        }

        if (!$support) {
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
        switch ($options['algorithm']) {
            case 'des':
                $options['key'] = str_pad(substr($options['key'], 0, 2), 2, "ZendFramework");
                break;
            case 'desext':
                $options['key'] = str_pad(substr($options['key'], 0, 9), 9, "ZendFramework");
                break;
            case 'md5':
                if (substr($options['key'], 0, 3) != '$1$') {
                    $options['key'] = '$1$' . $options['key'];
                }

                $options['key'] = str_pad(substr($options['key'], 0, 12), 12, "ZendFramework");
                break;
            case 'blowfish':
                if ((substr($options['key'], 0, 3) != '$2$')
                    || (substr($options['key'], 0, 4) != '$2a$')) {
                    $options['key'] = '$2$' . $options['key'];
                }

                $options['key'] = str_pad(substr($options['key'], 0, 16), 16, "ZendFramework");
                break;
            case 'default':
                $options['key'] = '';
                break;

        }

        if (empty($options['key'])) {
            $hashed = crypt($value);
        } else {
            $hashed = crypt($value, $options['key'])
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
        return 'Crypt';
    }
}
