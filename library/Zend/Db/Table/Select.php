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
 * @subpackage Select
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Select.php 5308 2007-06-14 17:18:45Z bkarwin $
 */


/**
 * @see Zend_Db_Select
 */
require_once 'Zend/Db/Select.php';


/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';


/**
 * Class for SQL SELECT query manipulation for the Zend_Db_Table component.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Table
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Table_Select extends Zend_Db_Select
{
    /**
     * Flag to see if the query has been modified through the from()
     * or columns() method.
     *
     * @var boolean
     */
    protected $_queryModified = false;

    /**
     * Table schema for parent Zend_Db_Table.
     *
     * @var array
     */
    protected $_info;

    /**
     * Table integrity override.
     *
     * @var array
     */
    protected $_integrityCheck = true;

    /**
     * Table instance that created this select object
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_table;

    /**
     * Class constructor
     *
     * @param Zend_Db_Table_Abstract $adapter
     */
    public function __construct(Zend_Db_Table_Abstract $table)
    {
        parent::__construct($table->getAdapter());

        $this->setTable($table);

        $name   = $table->info(Zend_Db_Table_Abstract::NAME);
        $schema = $table->info(Zend_Db_Table_Abstract::SCHEMA);

        $this->joinInner($name, null, self::SQL_WILDCARD, $schema);
    }

    /**
     * Return the table that created this select object
     *
     * @return Zend_Db_Table_Abstract
     */
    public function getTable()
    {
        return $this->_table;
    }

    /**
     * Sets the primary table name and retrieves the table schema.
     *
     * @param Zend_Db_Table_Abstract $adapter
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function setTable(Zend_Db_Table_Abstract $table)
    {
        $this->_adapter = $table->getAdapter();
        $this->_info    = $table->info();
        $this->_table   = $table;

        return $this;
    }

    /**
     * Sets the integrity check flag.
     *
     * Setting this flag to false skips the checks for table joins, allowing
     * 'hybrid' table rows to be created.
     *
     * @param Zend_Db_Table_Abstract $adapter
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function setIntegrityCheck($flag = true)
    {
        $this->_integrityCheck = $flag;
        return $this;
    }

    /**
     * Tests query to determine if expressions or aliases columns exist.
     *
     * @return boolean
     */
    public function isReadOnly()
    {
        $readOnly = false;
        $fields   = $this->getPart(Zend_Db_Table_Select::COLUMNS);
        $cols     = $this->_info[Zend_Db_Table_Abstract::COLS];

        if (!count($fields)) {
            return $readOnly;
        }

        foreach ($fields as $columnEntry) {
            $column = $columnEntry[1];
            $alias = $columnEntry[2];

            if ($alias !== null) {
                $column = $alias;
            }

            switch (true) {
                case ($column == self::SQL_WILDCARD):
                    break;

                case ($column instanceof Zend_Db_Expr):
                case (!in_array($column, $cols)):
                    $readOnly = true;
                    break 2;
            }
        }

        return $readOnly;
    }

    /**
     * Adds a FROM table and optional columns to the query.
     *
     * The table name can be expressed
     *
     * @param  array|string|Zend_Db_Expr|Zend_Db_Table_Abstract $name The table name or an
                                                                      associative array relating
                                                                      table name to correlation
                                                                      name.
     * @param  array|string|Zend_Db_Expr $cols The columns to select from this table.
     * @param  string $schema The schema name to specify, if any.
     * @return Zend_Db_Table_Select This Zend_Db_Table_Select object.
     */
    public function from($name, $cols = self::SQL_WILDCARD, $schema = null)
    {
        if ($name instanceof Zend_Db_Table_Abstract) {
            $info = $name->info();
            $name = $info[Zend_Db_Table_Abstract::NAME];
            if (isset($info[Zend_Db_Table_Abstract::SCHEMA])) {
                $schema = $info[Zend_Db_Table_Abstract::SCHEMA];
            }
        }

        $tableName = $this->getTable()->info(Zend_Db_Table_Abstract::NAME);

        if (!$this->_queryModified && in_array($tableName, (array) $name)) {
            $this->reset(self::FROM);
            $this->reset(self::COLUMNS);
        }

        $this->_queryModified = true;

        return $this->joinInner($name, null, $cols, $schema);
    }

    /**
     * Specifies the columns used in the FROM clause.
     *
     * The parameter can be a single string or Zend_Db_Expr object,
     * or else an array of strings or Zend_Db_Expr objects.
     *
     * @param  array|string|Zend_Db_Expr $cols The columns to select from this table.
     * @param  string $correlationName Correlation name of target table. OPTIONAL
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function columns($cols = '*', $correlationName = null)
    {
        if (!$this->_queryModified) {
            $this->reset(self::COLUMNS);
        }

        $this->_queryModified = true;

        return parent::columns($cols, $correlationName);
    }

    /**
     * Performs a validation on the select query before passing back to the parent class.
     * Ensures that only columns from the primary Zend_Db_Table are returned in the result.
     *
     * @return string This object as a SELECT string.
     */
    public function assemble()
    {
        $fields  = $this->getPart(Zend_Db_Table_Select::COLUMNS);
        $primary = $this->_info[Zend_Db_Table_Abstract::NAME];
        $schema  = $this->_info[Zend_Db_Table_Abstract::SCHEMA];

        // If no fields are specified we assume all fields from primary table
        if (!count($fields)) {
            $this->from($primary, self::SQL_WILDCARD, $schema);
            $fields = $this->getPart(Zend_Db_Table_Select::COLUMNS);
        }

        $from = $this->getPart(Zend_Db_Table_Select::FROM);

        if ($this->_integrityCheck !== false) {
            foreach ($fields as $columnEntry) {
                list($table, $column) = $columnEntry;

                // Check each column to ensure it only references the primary table
                if ($column) {
                    if (!isset($from[$table]) || $from[$table]['tableName'] != $primary) {
                        require_once 'Zend/Db/Table/Select/Exception.php';
                        throw new Zend_Db_Table_Select_Exception('Select query cannot join with another table');
                    }
                }
            }
        }

        return parent::assemble();
    }
}
