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
 * this implies the Zend_Db_Select as well.
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';


class Zend_Auth_Adapter_DbTable_Select implements Zend_Auth_Adapter_Interface 
{

    /**
     * $_zendDbSelect
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_zendDb= null;
    
    /**
     * $_zendDbSelect
     *
     * @var Zend_Db_Select
     */
    protected $_zendDbSelect = null;
    
    /**
     * $_zendDbSelect
     *
     * @var string
     */
    protected $_identityColumn = null;
    
    /**
     * $_resultRow
     *
     * @var array
     */
    protected $_resultRow = null;
    
    /**
     * __construct()
     *
     * @param Zend_Db_Adapter_Abstract $zendDb
     * @param Zend_Db_Select $select
     * @param string $identityColumn
     */
    public function __construct(Zend_Db_Adapter_Abstract $zendDb, Zend_Db_Select $select, $identityColumn)
    {
        $this->_zendDb = $zendDb;
        $this->_zendDbSelect = $select;
        $this->_identityColumn = $identityColumn;
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
     * authenticate() - Complete the Zend_Auth_Adapter interface.
     *
     * @return Zend_Auth_Result
     */
    public function authenticate()
    {
        
        $identities = $this->_zendDb->fetchAll($this->_zendDbSelect);
        
        // create result array
        $result = array(
            'is_valid'  => false,
            'identity' => null,
            'messages' => array()
            );
        
        if (count($identities) < 1) {
            $result['messages'][] = "Identity not found.";
            return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
        } elseif (count($identities) > 1) {
            $result['messages'][] = "More than one record matches supplied identity/credentials.";
            return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);                
        } 
        
        $identity = $identities[0];
        
        if (!array_key_exists($this->_identityColumn, $identity)) {
            throw new Zend_Auth_Exception('Supplied identity column does not match any of the columns returned from the select statement.');
        }
        
        $this->_resultRow = $identity;
        
        $result['is_valid'] = true;
        $result['identity'] = $identity[$this->_identityColumn];
        $result['messages'][] = "Authentication successful.";
        return new Zend_Auth_Result($result['is_valid'], $result['identity'], $result['messages']);
    }
    
}