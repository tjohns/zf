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
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: $
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';

/**
 * Validator for the hash of given files
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_Hash extends Zend_Validate_Abstract
{
    /**
     * @const string Error constants
     */
    const DOES_NOT_MATCH = 'fileHashDoesNotMatch';
    const NOT_DETECTED   = 'fileHashHashNotDetected';
    const NOT_FOUND      = 'fileHashNotFound';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::DOES_NOT_MATCH => "The file '%value%' does not match the given hashes",
        self::NOT_DETECTED   => "There was no hash detected for the given file",
        self::NOT_FOUND      => "The file '%value%' could not be found"
    );

    /**
     * Hash of the file
     *
     * @var string
     */
    protected $_hash;

    /**
     * Sets validator options
     *
     * $hash is the hash we accept for the file $file
     *
     * @param  string|array $hash      Hash to check for
     * @param  string       $algorithm (Optional) Algorithm to use, defaults to 'crc32'
     * @return void
     */
    public function __construct($hash, $algorithm = 'crc32')
    {
        $this->setHash($hash, $algorithm);
    }

    /**
     * Returns the set hash values as array, the hash as key and the algorithm the value
     *
     * @return array
     */
    public function getHash()
    {
        return $this->_hash;
    }

    /**
     * Sets the hash for one or multiple files
     *
     * @param  string|array $hash      Hash to check for
     * @param  string       $algorithm (Optional) Algorithm to use, defaults to 'crc32'
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function setHash($hash, $algorithm = 'crc32')
    {
        $this->_hash  = null;
        $this->addHash($hash, $algorithm);

        return $this;
    }

    /**
     * Adds the hash for one or multiple files
     *
     * @param  string|array $hash      Hash to check for
     * @param  string       $algorithm (Optional) Algorithm to use, defaults to 'crc32'
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function addHash($hash, $algorithm = 'crc32')
    {
        $known = hash_algos();
        if (!in_array($algorithm, $known)) {
            require_once 'Zend/Validate/Exception.php';
            throw new Zend_Validate_Exception("Unknown algorithm '{$algorithm}'");
        }

        if (is_string($hash)) {
            $hash = array($hash);
        }

        foreach ($hash as $value) {
            $this->_hash[$value] = $algorithm;
        }

        return $this;
    }

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if the given file confirms the set hash
     *
     * @param  string $value Filename to check for hash
     * @param  array  $file  File data from Zend_File_Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        // Is file readable ?
        if (!@is_readable($value)) {
            $this->_throw($file, self::NOT_FOUND);
            return false;
        }

        $algos  = array_unique(array_values($this->_hash));
        $hashes = array_unique(array_keys($this->_hash));
        foreach ($algos as $algorithm) {
            $filehash = hash_file($algorithm, $value);
            if ($filehash === false) {
                $this->_throw($file, self::NOT_DETECTED);
                return false;
            }

            foreach($hashes as $hash) {
                if ($filehash === $hash) {
                    return true;
                }
            }
        }

        $this->_throw($file, self::DOES_NOT_MATCH);
        return false;
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            $this->_value = $file['name'];
        }

        $this->_error($errorType);
        return false;
    }
}