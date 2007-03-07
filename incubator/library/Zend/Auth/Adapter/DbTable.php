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
    protected $_credentialInfo = null;

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
    public function __construct(Zend_Db_Adapter_Abstract $zendDb, $tableName = null, $identityColumn = null, $credentialColumn = null, $strictCheck = true)
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
     * @param string $value
     * @param Zend_Db_Expr $expr
     * @return Zend_Auth_Adapter_DbTable
     */
    public function setCredential($value, $treatment = null)
    {
        $info['value']     = $value;
        $info['treatment'] = $treatment;
        
        $this->_credentialInfo = $info;
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
        if ($this->_credentialInfo === null) {
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
        
        $credential_treatment = $credential_value = false;
        
        // if strict check enabled, identity is processed before credentials
        if ($this->_strictCheck) {
            
            // query for the identity
            $result_identities = $this->_zendDb->fetchAll($select->__toString());
            
            if (count($result_identities) < 1) {
                $result['messages'][] = "Identity not found.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
            } elseif (count($result_identities) > 1) {
                $result['messages'][] = "More than one record matches supplied identity.";
                return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);                
            }
            
            $identity = $result_identities[0];
            
            // query for the answer to the credential
            if ($this->_credentialInfo['treatment'] && (strpos($this->_credentialInfo['treatment'], "?") !== false) ) {
                $sql_supplied_credentials = 'SELECT ' 
                    . $this->_zendDb->quoteInto($this->_credentialInfo['treatment'], $this->_credentialInfo['value']) 
                    . ' AS ' . $this->_credentialColumn;
                $result_treated_credentials = $this->_zendDb->fetchAll($sql_supplied_credentials);
                $supplied_credential_value = $result_treated_credentials[0][$this->_credentialColumn];
            } else {
                $supplied_credential_value = $this->_credentialInfo['value'];
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
            
            if ($this->_credentialInfo['treatment'] && (strpos($this->_credentialInfo['treatment'], "?") !== false) ) {
                $supplied_credential = $this->_zendDb->quoteInto($this->_credentialInfo['treatment'], $this->_credentialInfo['value']);
            } else {
                $supplied_credential = $this->_zendDb->quote($this->_credential['value']);
            }
            
            $select->where("{$this->_credentialColumn} = $supplied_credential");
            
            // query for the identity
            $result_identities = $this->_zendDb->fetchAll($select->__toString());
            
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