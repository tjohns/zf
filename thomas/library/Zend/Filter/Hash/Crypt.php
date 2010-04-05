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
 * @see Zend_Filter_Encrypt_Interface
 */
require_once 'Zend/Filter/Encrypt/Interface.php';

/**
 * Encryption adapter for hash
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Encrypt_Hash implements Zend_Filter_Encrypt_Interface
{
    /**
     * Definitions for encryption
     * array(
     *     'key' => encryption key string
     *     'algorithm' => algorithm to use
     * )
     */
    protected $_encryption = array(
        'key'       => null,
        'algorithm' => 'md5',
    );

    /**
     * Class constructor
     *
     * @param string|array|Zend_Config $options Cryption Options
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

        $this->setEncryption($options);
    }

    /**
     * Returns the set encryption options
     *
     * @return array
     */
    public function getEncryption()
    {
        return $this->_encryption;
    }

    /**
     * Sets new encryption options
     *
     * @param  string|array $options Encryption options
     * @return Zend_Filter_File_Encryption
     */
    public function setEncryption($options)
    {
        if (is_string($options)) {
            $options = array('algorithm' => $options);
        }

        if (!is_array($options)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Invalid options argument provided to filter');
        }

        $options = $options + $this->getEncryption();
        $algorithms = hash_algos();
        if (!in_array($options['algorithm'], $algorithms)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("The algorithm '{$options['algorithm']}' is not supported");
        }

        $this->_encryption = $options;
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Encrypts $value with the defined settings
     *
     * @param  string $value The content to encrypt
     * @return string The encrypted content
     */
    public function encrypt($value)
    {
        $options = $this->getEncryption();
        if (empty($options['key'])) {
            $encrypted = hash($options['algorithm'], $value);
        } else {
            $encrypted = hash_hmac($options['algorithm'], $options['key'], $value);
        }

        return $encrypted;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Decrypts $value with the defined settings
     *
     * @param  string $value Content to decrypt
     * @throws Zend_Filter_Exception Decryption not possible for hashes
     */
    public function decrypt($value)
    {
        require_once 'Zend/Filter/Exception.php';
        throw new Zend_Filter_Exception('Hashes can not be decrypted');
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
