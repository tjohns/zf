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
 * @package    Zend_Auth
 * @subpackage Zend_Auth_Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Digest.php 3412 2007-02-14 22:22:35Z darby $
 */

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage Zend_Auth_Adapter
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_DbTable implements Zend_Auth_Adapter_Interface
{
        
    /**
     * Database Connection
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_zendDb = null;
    
    /**
     * $_tableName - the table name to check
     *
     * @var string
     */
    protected $_tableName = null;
    
    /**
     * $_identityColumn - the column to use as the identity
     *
     * @var string
     */
    protected $_identityColumn = null;
    
    /**
     * $_credentialColumns - columns to be used as the credentials
     *
     * @var string
     */
    protected $_credentialColumn = null;
    
    /**
     * $_identity - Identity value
     *
     * @var string
     */
    protected $_identity = null;
    
    /**
     * $_credential - Credential values
     *
     * @var string
     */
    protected $_credential = null;

    /**
     * $_credentialTreatment - 
     *
     * @var string
     */
    protected $_credentialTreatment = null;
    
    /**
     * $_credentialTreatmentUseRow
     *
     * @var bool
     */
    protected $_credentialTreatmentUseRow = false;
    
    /**
     * $_strictCheck - Wether to retreive the identity and credential together, OR identity then check password.
     *
     * @var bool
     */
    protected $_strictCheck = false;
    
    /**
     * $_resultRow
     *
     * @var array
     */
    protected $_resultRow = null;
    
    /**
     * __construct() - Constructor
     *
     * @param Zend_Db_Adapter_Abstract $databaseConnection
     * @param string $tableName
     * @param string $identityColumn
     * @param string $credentialColumn
     * @param bool $strictCheck
     */
    public function __construct(Zend_Db_Adapter_Abstract $zendDb, $tableName = null, $identityColumn = null, $credentialColumn = null, $credentialTreatment = null, $strictCheck = true)
    {
        // setup configuration
        $this->_zendDb = $zendDb;
        
        if ($tableName) {
            $this->setTableName($tableName);
        }
        
        if ($identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }
        
        if ($credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }

        if ($credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
        
        $this->strictCheck($strictCheck);
    }
    
    /**
     * setTableName() - set the table name to be used in the select query
     *
     * @param string $tableName
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setTableName($tableName)
    {
        $this->_tableName = $tableName;
        return $this;
    }
    
    /**
     * setIdentityColumn() - set the column name to be used as the identity column
     *
     * @param string $identityColumn
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setIdentityColumn($identityColumn)
    {
        $this->_identityColumn = $identityColumn;
        return $this;
    }
    
    /**
     * setCredentialColumn() - set the column name to be used as the credential column
     *
     * @param string $credentialColumn
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setCredentialColumn($credentialColumn)
    {
        $this->_credentialColumn = $credentialColumn;
        return $this;
    }

    /**
     * setCredentialTreatment() - in a large number of cases, passwords and other sensitive type data is
     * encryted or obscured through some function or algorithm.  This method allows the developer to 
     * pass a parameritized string that will be used to determine if the user supplied data matches the
     * stored version and format of the credential. examples: 'PASSWORD(?)' OR 'MD5(?)'
     *
     * NOTE: useRowInforation - this allows for the EXTREME EDGE CASE that multiple columns of a credential
     * could be used for building a hashed value.  Without this, the adapter will issue a treated value
     * (if used in strict mode) like so:   
     *      SELECT MD5('supplied password') as $credential_name;
     * 
     * If you need to retrieve other columns to build the hashing algorithm, set useRowInformation to true,
     * and the following will be possible: 
     * 
     *      $adapter->setCredentialTreatment('SHA1(CONCAT(?, salt_column))', true)
     * resulting in:
     *      SELECT SHA1(CONCAT('my supplied password', salt_column)) as $credential_column WHERE $identity_column = $identity;
     * 
     * @param string $treatment
     * @param bool $useRow
     */
    public function setCredentialTreatment($treatment, $useRow = false)
    {
        $this->_credentialTreatment = $treatment;
        $this->_credentialTreatmentUseRow = ($useRow) ? true : false;
        return $this;
    }
    
    /**
     * strictCheck()
     *     when TRUE - will check for identity first in query, then compare credential column to provided value
     *     when FALSE - will check for both identity and password in one query
     * 
     * The benefits of checking with strictness = true are that your messages will be more fine grained.  You will
     * be able to determine the difference between an identity not existing and a credential being bad.  There is
     * a slight impact on performance as TREATED credentials will require 1 extra query to be issued to resolve the
     * credential value.
     * 
     * By checking with strictness = false, an identity not existing and a password being incorrect will yeild the same
     * failed athentication messages.
     * 
     * @param bool $flag
     */
    public function strictCheck($flag)
    {
        $this->_strictCheck = ($flag) ? true : false;
    }
        
    /**
     * setIdentity() - set the value to be used as the identity
     *
     * @param string $value
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setIdentity($value)
    {
        $this->_identity = $value;
        return $this;
    }
    
    /**
     * setCredential() - set the credential value to be used, optionally can specify a treatment
     * to be used, should be supplied in parameritized form, ie 'MD5?()' or 'PASSWORD(?)'
     *
     * @param string $credential
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    
    /**
     * getResultRow()
     *
     * @return array
     */
    public function getResultRow()
    {
        return $this->_resultRow;
    }
    
    /**
     * authenticate() - complete the Auth Adapter Interface.
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        
        if ($this->_tableName == '') {
            throw new Zend_Auth_Exception('A table must be supplied authentication adapter.');
        }
        
        if ($this->_identityColumn == '') {
            throw new Zend_Auth_Exception('A table column must be supplied for the identity.');
        }
        
        // make sure an identity satisfied
        if ($this->_identity == '') {
            throw new Zend_Auth_Exception('A value for the identity must be provided to authenticate.');
        }
        
        // at least one credential
        if ($this->_credentialColumn == '') {
            throw new Zend_Auth_Exception('At least one credential column must be supplied to autheticate against.');
        }
        
        // all credential values must be provided for
        if ($this->_credential === null) {
            throw new Zend_Auth_Exception('A credential value must be provided to authenticate.');
        }
        
        // create result array
        $result = array(
            'is_valid'  => false,
            'identity' => $this->_identity,
            'messages' => array()
            );
        
        
        // get select
        $select = $this->_zendDb->select();
        $select->from($this->_tableName);
        $select->where("{$this->_identityColumn} = ?", $this->_identity);
        
        // if strict check enabled, identity is processed before credentials
        if ($this->_strictCheck) {
            
            // query for the identity
            try {
                $result_identities = $this->_zendDb->fetchAll($select->__toString());
            } catch (Exception $e) {
                print_r($e);
                die();
                throw new Zend_Auth_Exception($e->getMessage());
            }
            
            if (count($result_identities) < 1) {
                $result['messages'][] = "Identity not found.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
            } elseif (count($result_identities) > 1) {
                $result['messages'][] = "More than one record matches supplied identity.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);                
            }
            
            $identity = $result_identities[0];
            
            // query for the answer to the credential
            if ($this->_credentialTreatment && (strpos($this->_credentialTreatment, "?") !== false) ) {
                if ($this->_credentialTreatmentUseRow) {
                    $select_supplied_credentials = $this->_zendDb->select();
                    $credential_expr = new Zend_Db_Expr($this->_zendDb->quoteInto($this->_credentialTreatment, $this->_credential) . ' AS ' . $this->_credentialColumn);
                    $select_supplied_credentials
                        ->from($this->_tableName, $credential_expr)
                        ->where($this->_identityColumn . ' = ?', $this->_identity);
                    $sql_supplied_credentials = $select_supplied_credentials->__toString();
                } else {
                    $sql_supplied_credentials = 'SELECT '
                        . $this->_zendDb->quoteInto($this->_credentialTreatment, $this->_credential) 
                        . ' AS ' . $this->_credentialColumn;
                }
                
                try {
                    $result_treated_credentials = $this->_zendDb->fetchAll($sql_supplied_credentials);
                } catch (Exception $e) {
                    throw Zend_Auth_Exception($e->getMessage());
                }
                $supplied_credential_value = $result_treated_credentials[0][$this->_credentialColumn];
            } else {
                $supplied_credential_value = $this->_credential;
            }

            // all credential values must be provided for
            if (!array_key_exists($this->_credentialColumn, $identity)) {
                throw new Zend_Auth_Exception($this->_credentialColumn . ' does not appear to be a valid credential column.');
            }
            
            if ($identity[$this->_credentialColumn] != $supplied_credential_value) {
                $result['messages'][] = "Invalid credentials.";
                $result['messages'][] = "Credential '{$this->_credentialColumn}' does not match recorded value.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
            }

            $this->_resultRow = $identity;
            
            $result['is_valid'] = true;
            $result['messages'][] = "Authentication successful.";
            return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);

        } else {
            
            if ($this->_credentialTreatment && (strpos($this->_credentialTreatment, "?") !== false) ) {
                $supplied_credential = $this->_zendDb->quoteInto($this->_credentialTreatment, $this->_credential);
            } else {
                $supplied_credential = $this->_zendDb->quote($this->_credential);
            }
            
            $select->where("{$this->_credentialColumn} = $supplied_credential");
            
            // query for the identity
            try {
                $result_identities = $this->_zendDb->fetchAll($select->__toString());
            } catch (Exception $e) {
                throw new Zend_Auth_Exception($e->getMessage());
            }
            
            if (count($result_identities) < 1) {
                $result['messages'][] = "Identity not found.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
            } elseif (count($result_identities) > 1) {
                $result['messages'][] = "More than one record matches supplied identity.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);                
            } 

            $this->_resultRow = $result_identities[0];
            
            $result['is_valid'] = true;
            $result['messages'][] = "Authentication successful.";
            return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
        }

    }

}