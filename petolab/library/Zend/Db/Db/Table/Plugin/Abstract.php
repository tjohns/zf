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
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Zend_Db_Table_Plugin_Interface
 */
require_once 'Zend/Db/Table/Plugin/Interface.php';


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Table_Plugin_Abstract implements Zend_Db_Table_Plugin_Interface, SplObserver
{

    /**
     * Plugin options
     * @var array
     */
    protected $_options = array();

    /**
     * Constructor
     *
     * @param mixed $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        } elseif ($options instanceof Zend_Config) {
            $this->setConfig($options);
        }

        // Initialize plugin
        $this->init();
    }

    /**
     * Set plugin state from options array
     * 
     * @param  array $options 
     * @return Zend_Db_Table_Plugin_Abstract
     */
    public function setOptions(array $options)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            throw new Zend_Db_Table_Plugin_Exception('Invalid options');
        }

        $this->_options = $options;

        return $this;
    }

    /**
     * Set form state from config object
     * 
     * @param  Zend_Config $config 
     * @return Zend_Db_Table_Plugin_Abstract
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->setOptions($config->toArray());
    }

    /**
     * Initialize plugin (used by extending classes)
     * 
     * @return void
     */
    public function init()
    {
    }

    /**
     * Update plugin
     *
     * Implements the SplObserver interface
     * 
     * @return void
     */
    public function update(SplSubject $subject)
    {
    }

    /**
     * Called before Zend_Db_Table_Abstract begins its user-defined initialization.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function preInitTable(Zend_Db_Table_Abstract $table)
    {}

    /**
     * Called after Zend_Db_Table_Abstract completes its user-defined initialization.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function postInitTable(Zend_Db_Table_Abstract $table)
    {}

    /**
     * Called before Zend_Db_Table_Rowset_Abstract begins its user-defined initialization.
     *
     * @param Zend_Db_Table_Rowset_Abstract $rowset
     * @return void
     */
    public function preInitRowset(Zend_Db_Table_Rowset_Abstract $rowset)
    {}

    /**
     * Called after Zend_Db_Table_Rowset_Abstract completes its user-defined initialization.
     *
     * @param Zend_Db_Table_Rowset_Abstract $rowset
     * @return void
     */
    public function postInitRowset(Zend_Db_Table_Rowset_Abstract $rowset)
    {}

    /**
     * Called before Zend_Db_Table_Row_Abstract begins its user-defined initialization.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function preInitRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called after Zend_Db_Table_Row_Abstract completes its user-defined initialization.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function postInitRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called before Zend_Db_Table_Abstract begins a fetch operation (find/fetchRow/fetchAll).
     *
     * @param Zend_Db_Table_Abstract $table
     * @param Zend_Db_Table_Select $select
     * @return void
     */
    public function preFetchTable(Zend_Db_Table_Abstract $table, Zend_Db_Table_Select $select)
    {}

    /**
     * Called after Zend_Db_Table_Abstract completes a fetch operation (find/fetchRow/fetchAll).
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function postFetchTable(Zend_Db_Table_Abstract $table, array $data)
    {}

    /**
     * Called before Zend_Db_Table_Abstract begins a save or insert operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @param array $data
     * @return void
     */
    public function preSaveTable(Zend_Db_Table_Abstract $table, array $data)
    {}

    /**
     * Called after Zend_Db_Table_Abstract completes a save or insert operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function postSaveTable(Zend_Db_Table_Abstract $table)
    {}

    /**
     * Called before Zend_Db_Table_Abstract begins an insert operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @param array $data
     * @return void
     */
    public function preInsertTable(Zend_Db_Table_Abstract $table, array $data)
    {}

    /**
     * Called after Zend_Db_Table_Abstract completes an insert operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function postInsertTable(Zend_Db_Table_Abstract $table, array $data)
    {}

    /**
     * Called before Zend_Db_Table_Abstract begins an update operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @param array $data
     * @param mixed $where OPTIONAL
     * @return void
     */
    public function preUpdateTable(Zend_Db_Table_Abstract $table, array $data, $where = null)
    {}

    /**
     * Called after Zend_Db_Table_Abstract completes an update operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function postUpdateTable(Zend_Db_Table_Abstract $table, array $data, $where = null)
    {}

    /**
     * Called before Zend_Db_Table_Abstract begins a delete operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @param mixed $where OPTIONAL
     * @return void
     */
    public function preDeleteTable(Zend_Db_Table_Abstract $table, $where = null)
    {}

    /**
     * Called after Zend_Db_Table_Abstract completes a delete operation.
     *
     * @param Zend_Db_Table_Abstract $table
     * @return void
     */
    public function postDeleteTable(Zend_Db_Table_Abstract $table)
    {}

    /**
     * Called before Zend_Db_Table_Row_Abstract begins a save or insert operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function preSaveRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called after Zend_Db_Table_Row_Abstract completes a save or insert operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function postSaveRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called before Zend_Db_Table_Row_Abstract begins an insert operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function preInsertRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called after Zend_Db_Table_Row_Abstract completes an insert operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function postInsertRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called before Zend_Db_Table_Row_Abstract begins an update operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function preUpdateRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called after Zend_Db_Table_Row_Abstract completes an update operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function postUpdateRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called before Zend_Db_Table_Row_Abstract begins a delete operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function preDeleteRow(Zend_Db_Table_Row_Abstract $row)
    {}

    /**
     * Called after Zend_Db_Table_Row_Abstract completes a delete operation.
     *
     * @param Zend_Db_Table_Row_Abstract $row
     * @return void
     */
    public function postDeleteRow(Zend_Db_Table_Row_Abstract $row)
    {}

    public function getColumn(Zend_Db_Table_Row_Abstract $row, $columnName, $value)
    {
        return $value;
    }

    public function setColumn(Zend_Db_Table_Row_Abstract $row, $columnName, $value)
    {
        return $value;
    }
}