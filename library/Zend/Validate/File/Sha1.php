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
 * @see Zend_Validate_File_Hash
 */
require_once 'Zend/Validate/File/Hash.php';

/**
 * Validator for the sha1 hash of given files
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_File_Sha1 extends Zend_Validate_File_Hash
{
    /**
     * @const string Error constants
     */
    const DOES_NOT_MATCH = 'fileSha1DoesNotMatch';
    const NOT_DETECTED   = 'fileSha1NotDetected';
    const NOT_FOUND      = 'fileSha1NotFound';

    /**
     * @var array Error message templates
     */
    protected $_messageTemplates = array(
        self::DOES_NOT_MATCH => "The file '%value%' does not match the given sha1 hashes",
        self::NOT_DETECTED   => "There was no sha1 hash detected for the given file",
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
     * @return void
     */
    public function __construct($hash)
    {
        $this->setHash($hash, 'sha1');
    }

    /**
     * Returns all set sha1 hashes
     *
     * @return array
     */
    public function getSha1()
    {
        return $this->getHash();
    }

    /**
     * Sets the sha1 hash for one or multiple files
     *
     * @param  string|array $hash      Hash to check for
     * @param  string       $algorithm (Depreciated) Algorithm to use, fixed to sha1
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function setHash($hash, $algorithm = 'sha1')
    {
        parent::setHash($hash, 'sha1');
        return $this;
    }

    /**
     * Sets the sha1 hash for one or multiple files
     *
     * @param  string|array $hash      Hash to check for
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function setSha1($hash)
    {
        $this->setHash($hash, 'sha1');
        return $this;
    }

    /**
     * Adds the sha1 hash for one or multiple files
     *
     * @param  string|array $hash      Hash to check for
     * @param  string       $algorithm (Depreciated) Algorithm to use, fixed to sha1
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function addHash($hash, $algorithm = 'sha1')
    {
        parent::addHash($hash, 'sha1');
        return $this;
    }

    /**
     * Adds the sha1 hash for one or multiple files
     *
     * @param  string|array $hash      Hash to check for
     * @return Zend_Validate_File_Hash Provides a fluent interface
     */
    public function addSha1($hash)
    {
        $this->addHash($hash, 'sha1');
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

        $hashes = array_unique(array_keys($this->_hash));
        $filehash = hash_file('sha1', $value);
        if ($filehash === false) {
            $this->_throw($file, self::NOT_DETECTED);
            return false;
        }

        foreach($hashes as $hash) {
            if ($filehash === $hash) {
                return true;
            }
        }

        $this->_throw($file, self::DOES_NOT_MATCH);
        return false;
    }
}