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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @see Zend_Filter_Encrypt_Interface
 */
require_once 'Zend/Filter/Encrypt/Interface.php';

/**
 * Encryption adapter for mcrypt
 *
 * @category   Zend
 * @package    Zend_Filter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Filter_Encrypt_Mcrypt implements Zend_Filter_Encrypt_Interface
{
    /**
     * Definitions for encryption
     * array(
     *     'key' => encryption key string
     *     'algorithm' => algorithm to use
     *     'algorithm_directory' => directory where to find the algorithm
     *     'mode' => encryption mode to use
     *     'modedirectory' => directory where to find the mode
     *  ))
     */
    protected $_encryption = array(
        'key'                 => 'ZendFramework',
        'algorithm'           => 'blowfish',
        'algorithm_directory' => '',
        'mode'                => 'cbc',
        'mode_directory'      => '',
        'vector'              => null
    );

    /**
     * Class constructor
     *
     * @param string|array $oldfile   File which should be renamed/moved
     * @param string|array $newfile   New filename, when not set $oldfile will be used as new filename
     *                                for $value when filtering
     * @param boolean      $overwrite If set to true, it will overwrite existing files
     */
    public function __construct($options)
    {
        if (!extension_loaded('mcrypt')) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('This filter needs the mcrypt extension');
        }

        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (is_string($options)) {
            $options = array('key' => $options);
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
            $options = array('key' => $options);
        }

        if (!is_array($options)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Invalid options argument provided to filter');
        }

        $options = $options + $this->getEncryption();
        $algorithms = mcrypt_list_algorithms($options['algorithm_directory']);
        if (!in_array($options['algorithm'], $algorithms)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("The algorithm '{$options['algorithm']}' is not supported");
        }

        $modes = mcrypt_list_modes($options['mode_directory']);
        if (!in_array($options['mode'], $modes)) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception("The mode '{$options['mode']}' is not supported");
        }

        if (!mcrypt_module_self_test($options['algorithm'], $options['algorithm_directory'])) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('The given algorithm can not be used due an internal mcrypt problem');
        }

        $cipher = mcrypt_module_open(
            $options['algorithm'],
            $options['algorithm_directory'],
            $options['mode'],
            $options['mode_directory']);

        if ($cipher === false) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Mcrypt can not be opened with your settings');
        }

        if (empty($options['vector'])) {
            srand();
            $options['vector'] = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_RAND);
        } else {
            $options['vector'] = str_pad($options['vector'], mcrypt_enc_get_iv_size($cipher));
            $options['vector'] = substr($options['vector'], 0, mcrypt_enc_get_iv_size($cipher));
        }

        mcrypt_module_close($cipher);
        $this->_encryption = $options;

        return $this;
    }

    /**
     * Returns the set vector
     *
     * @return string
     */
    public function getVector()
    {
        return $this->_encryption['vector'];
    }

    /**
     * Sets the initialization vector
     *
     * @param string $vector (Optional) Vector to set
     * @return Zend_Filter_Encrypt_Mcrypt
     */
    public function setVector($vector = null)
    {
        if (empty($vector)) {
            $cipher = mcrypt_module_open(
                $this->_encryption['algorithm'],
                $this->_encryption['algorithm_directory'],
                $this->_encryption['mode'],
                $this->_encryption['mode_directory']);

            if ($cipher === false) {
                require_once 'Zend/Filter/Exception.php';
                throw new Zend_Filter_Exception('Mcrypt can not be opened with your settings');
            }

            srand();
            $this->_encryption['vector'] = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_RAND);
            mcrypt_module_close($cipher);
        } else {
            $this->_encryption['vector'] = $vector;
        }

        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Encrypts the file $value with the defined settings
     *
     * @param  string $value Full path of file to change
     * @return string The filename which has been set, or false when there were errors
     */
    public function encrypt($value)
    {
        $cipher = mcrypt_module_open(
            $this->_encryption['algorithm'],
            $this->_encryption['algorithm_directory'],
            $this->_encryption['mode'],
            $this->_encryption['mode_directory']);

        if ($cipher === false) {
            require_once 'Zend/Filter/Exception.php';
            throw new Zend_Filter_Exception('Mcrypt can not be opened with your settings');
        }

        srand();
        $keysize = mcrypt_enc_get_key_size($cipher);
        $key     = substr(md5($this->_encryption['key']), 0, $keysize);

        mcrypt_generic_init($cipher, $key, $this->_encryption['vector']);
        $encrypted = mcrypt_generic($cipher, $value);
        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);

        return $encrypted;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Decrypts the file $value with the defined settings
     *
     * @param  string $value Full path of file to change
     * @return string The filename which has been set, or false when there were errors
     */
    public function decrypt($value)
    {
        $cipher = mcrypt_module_open(
            $this->_encryption['algorithm'],
            $this->_encryption['algorithm_directory'],
            $this->_encryption['mode'],
            $this->_encryption['mode_directory']);

        srand();
        $keysize = mcrypt_enc_get_key_size($cipher);
        $key     = substr(md5($this->_encryption['key']), 0, $keysize);

        mcrypt_generic_init($cipher, $key, $this->_encryption['vector']);
        $decrypted = mdecrypt_generic($cipher, $value);
        mcrypt_generic_deinit($cipher);
        mcrypt_module_close($cipher);

        return $decrypted;
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    public function toString()
    {
        return 'Mcrypt';
    }
}
