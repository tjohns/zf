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
     * @param string $credentialTreatment
     */
    public function __construct(Zend_Db_Adapter_Abstract $zendDb, $tableName = null, $identityColumn = null, $credentialColumn = null, $credentialTreatment = null)
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
     * @param string $treatment
     */
    public function setCredentialTreatment($treatment)
    {
        $this->_credentialTreatment = $treatment;
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
    public function getResultRow($returnKeys = array())
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
        $exception = null;
        
        if ($this->_tableName == '') {
            $exception = 'A table must be supplied authentication adapter.';
        } elseif ($this->_identityColumn == '') {
            $exception = 'A table column must be supplied for the identity.';
        } elseif ($this->_identity == '') {
            $exception = 'A value for the identity must be provided to authenticate.';
        } elseif ($this->_credentialColumn == '') {
            $exception = 'A credential column must be supplied to autheticate against.';
        } elseif ($this->_credential === null) {
            $exception = 'A credential value must be provided to authenticate.';
        }
        
        if (!is_null($exception)) {
            require_once 'Zend/Auth/Exception.php';
            throw new Zend_Auth_Exception($exception);
        }
        
        // create result array
        $auth_result = array(
            'code'     => Zend_Auth_Result::FAILURE,
            'identity' => $this->_identity,
            'messages' => array()
            );
        
        // build credential expression
        if (empty($this->_credentialTreatment) || (strpos($this->_credentialTreatment, "?") === false)) {
            $this->_credentialTreatment = '?';
        }
        
        $credential_expression = new Zend_Db_Expr(
            $this->_zendDb->quoteInto(
                $this->_zendDb->quoteIdentifier($this->_credentialColumn)
                . ' = ' . $this->_credentialTreatment, $this->_credential
                )
            . ' AS zend_auth_credential_match'
            );

        // get select
        $db_select = $this->_zendDb->select();
        $db_select->from($this->_tableName, array('*', $credential_expression))
                  ->where($this->_zendDb->quoteIdentifier($this->_identityColumn) . ' = ?', $this->_identity);
        
        // query for the identity
        try {
            $result_identities = $this->_zendDb->fetchAll($db_select->__toString());
        } catch (Exception $e) {
            throw new Zend_Auth_Exception($e->getMessage());
        }
        
        if (count($result_identities) < 1) {
            $auth_result['code'] = Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND;
            $auth_result['messages'][] = 'A record with the supplied identity could not be found.';
            return new Zend_Auth_Result($auth_result['code'], $auth_result['identity'], $auth_result['messages']);
        } elseif (count($result_identities) > 1) {
            $auth_result['code'] = Zend_Auth_Result::FAILURE_IDENTITY_TOO_AMBIGIOUS;
            $auth_result['messages'][] = 'More than one record matches the supplied identity.';
            return new Zend_Auth_Result($auth_result['code'], $auth_result['identity'], $auth_result['messages']);                
        }
        
        $result_identity = $result_identities[0];
        
        if ($result_identity['zend_auth_credential_match'] != '1') {
            $auth_result['code'] = Zend_Auth_Result::FAILURE_INVALID_CREDENTIAL;
            $auth_result['messages'][] = 'Supplied credential is invalid.';
            return new Zend_Auth_Result($auth_result['code'], $auth_result['identity'], $auth_result['messages']);
        }
        
        unset($result_identity['zend_auth_credential_match']);
        $this->_resultRow = $result_identity;
        
        $auth_result['code'] = Zend_Auth_Result::SUCCESS;
        $auth_result['messages'][] = 'Authentication successful.';
        return new Zend_Auth_Result($auth_result['code'], $auth_result['identity'], $auth_result['messages']);   
    }

}