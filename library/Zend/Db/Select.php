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
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/** Zend_Db_Adapter_Abstract */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * Class for SQL SELECT generation and results.
 *
 * @category   Zend
 * @package    Zend_Db
 * @subpackage Select
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Db_Select
{

    const DISTINCT     = 'distinct';
    const FOR_UPDATE   = 'forUpdate';
    const COLUMNS      = 'columns';
    const FROM         = 'from';
    const JOIN         = 'join';
    const  INNER       = 'inner';
    const  LEFT        = 'left';
    const  RIGHT       = 'right';
    const  FULL        = 'full';
    const  CROSS       = 'cross';
    const  NATURAL     = 'natural';
    const WHERE        = 'where';
    const GROUP        = 'group';
    const HAVING       = 'having';
    const ORDER        = 'order';
    const LIMIT_COUNT  = 'limitCount';
    const LIMIT_OFFSET = 'limitOffset';

    /**
     * Zend_Db_Adapter_Abstract object.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_adapter;

    /**
     * The initial values for the $_parts array.
     *
     * @var array
     */
    protected static $_partsInit = array(
        self::DISTINCT     => false,
        self::FOR_UPDATE   => false,
        self::COLUMNS      => array(),
        self::FROM         => array(),
        self::JOIN         => array(),
        self::WHERE        => array(),
        self::GROUP        => array(),
        self::HAVING       => array(),
        self::ORDER        => array(),
        self::LIMIT_COUNT  => null,
        self::LIMIT_OFFSET => null,
    );

    /**
     * The component parts of a SELECT statement.
     * Initialized to the $_partsInit array in the constructor.
     *
     * @var array
     */
    protected $_parts = array();

    /**
     * Tracks which columns are being select from each table and join.
     *
     * @var array
     */
    protected $_tableCols = array();

    /**
     * Class constructor
     *
     * @param Zend_Db_Adapter_Abstract $adapter
     */
    public function __construct(Zend_Db_Adapter_Abstract $adapter)
    {
        $this->_adapter = $adapter;
        $this->_parts = self::$_partsInit;
    }

    /**
     * Converts this object to an SQL SELECT string.
     *
     * @todo use $this->_adapter->quoteColumns() for non-PDO adapters
     * @todo use $this->_adapter->quoteTableNames() for non-PDO adapters
     * @todo use prepared queries for PDO adapters instead of constructing all the SQL ourselves
     *           like in Adapter/Abstract.php.html:query()
     * @return string This object as a SELECT string.
     */
    public function __toString()
    {
        // initial SELECT [DISTINCT] [FOR UPDATE]
        $sql = "SELECT";
        if ($this->_parts[self::DISTINCT]) {
            $sql .= " DISTINCT";
        }
        if ($this->_parts[self::FOR_UPDATE]) {
            $sql .= " FOR UPDATE";
        }
        $sql .= "\t";

        // add columns
        if ($this->_parts[self::COLUMNS]) {
            $columns = array();
            foreach ($this->_parts[self::COLUMNS] as $correlationName => $columnList) {
                if (empty($correlationName)) {
                    foreach ($columnList as $expr) {
                        $columns[] = "$expr";
                    }
                } else {
                    foreach ($columnList as $columnName) {
                        $columns[] = "$correlationName.$columnName";
                    }
                }
            }
            $sql .= implode(",\n\t", $columns);
        }

        // from these joined tables
        if ($this->_parts[self::FROM]) {
            $from = array();
            // array_pop()
            foreach ($this->_parts[self::FROM] as $correlationName => $table) {
                $tmp = '';
                if (empty($from)) {
                    // First table is named alone ignoring join information
                    $tmp .= $table['tableName'] . ' ' . $correlationName;
                } else {
                    // Subsequent tables may have joins
                    if (! empty($table['joinType'])) {
                        $tmp .= strtoupper($table['joinType']);
                    }
                    $tmp .= ' JOIN ' .  $table['tableName'] . ' ' . $correlationName;
                    if (! empty($table['joinCondition'])) {
                        $tmp .= ' ON ' . $table['joinCondition'];
                    }
                }
                // add the table name and condition
                // add to the list
                $from[] = $tmp;
            }
            // add the list of all joins
            if (!empty($from)) {
                $sql .= "\nFROM " . implode("\n", $from);
            }

            // with these where conditions
            if ($this->_parts[self::WHERE]) {
                $sql .= "\nWHERE\n\t";
                $sql .= implode("\n\t", $this->_parts[self::WHERE]);
            }

            // grouped by these columns
            if ($this->_parts[self::GROUP]) {
                $sql .= "\nGROUP BY\n\t";
                $sql .= implode(",\n\t", $this->_parts[self::GROUP]);
            }

            // having these conditions
            if ($this->_parts[self::HAVING]) {
                $sql .= "\nHAVING\n\t";
                $sql .= implode("\n\t", $this->_parts[self::HAVING]);
            }

        }

        // ordered by these columns
        if ($this->_parts[self::ORDER]) {
            $sql .= "\nORDER BY\n\t";
            $sql .= implode(",\n\t", $this->_parts[self::ORDER]);
        }

        // determine offset
        $count = 0;
        $offset = 0;
        if (!empty($this->_parts[self::LIMIT_OFFSET])) {
            $offset = (int) $this->_parts[self::LIMIT_OFFSET];
            // this should be reduced to the max integer PHP can support
            $count = intval(9223372036854775807);
        }

        // determine count
        if (!empty($this->_parts[self::LIMIT_COUNT])) {
            $count = (int) $this->_parts[self::LIMIT_COUNT];
        }

        // add limits clause
        if ($count > 0) {
            $sql .= "\n";
            $sql = trim($this->_adapter->limit($sql, $count, $offset));
        }

        return $sql;
    }

    /**
     * Makes the query SELECT DISTINCT.
     *
     * @param bool $flag Whether or not the SELECT is DISTINCT (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function distinct($flag = true)
    {
        $this->_parts[self::DISTINCT] = (bool) $flag;
        return $this;
    }

    /**
     * Makes the query SELECT FOR UPDATE.
     *
     * @param bool $flag Whether or not the SELECT is DISTINCT (default true).
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function forUpdate($flag = true)
    {
        $this->_parts[self::FOR_UPDATE] = (bool) $flag;
        return $this;
    }

    /**
     * Adds a FROM table and optional columns to the query.
     *
     * @param string $name The table name.
     * @param array|string $cols The columns to select from this table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function from($name, $cols = '*')
    {
        return $this->_join(self::CROSS, $name, null, $cols);
    }

    /**
     * Populate the {@link $_parts} 'join' key
     *
     * Does the dirty work of populating the join key.
     *
     * @access protected
     * @param null|string $type Type of join; inner, left, and null are
     * currently supported
     * @param string $name Table name
     * @param string $cond Join on this condition
     * @param array|string $cols The columns to select from the joined table
     * @return Zend_Db_Select This Zend_Db_Select object
     * @throws Zend_Db_Select_Exception
     */
    protected function _join($type, $name, $cond, $cols)
    {
        if (!in_array($type, array(self::INNER, self::LEFT, self::RIGHT, self::FULL, self::CROSS, self::NATURAL))) {
            throw new Zend_Db_Select_Exception("Invalid join type '$type'");
        }

        if (is_array($name)) {
            // Must be array($tableName => $correlationName)
            foreach ($name as $_tableName => $_correlationName) {
                $tableName = $_tableName;
                $correlationName = $_correlationName;
                break;
            }
        } else if (empty($name)) {
            $correlationName = $tableName = '';
        } else {
            // Generate a correlation name like $tableName_1, $tableName_2, etc.
            // We can assume no more than 64 tables in a join.
            $correlationName = $tableName = $name;
            for ($i = 1; $i <= 64; ++$i) {
                $c = $correlationName . '_' . (string) $i;
                if (!array_key_exists($c, $this->_parts[self::FROM])) {
                    $correlationName = $c;
                    break;
                }
            }
        }

        if (!empty($correlationName)) {
            if (array_key_exists($correlationName, $this->_parts[self::FROM])) {
                throw new Zend_Db_Select_Exception("You cannot define a correlation name '$correlationName' more than once");
            }

            $this->_parts[self::FROM][$correlationName] = array(
                'joinType' => $type,
                'tableName' => $name,
                'joinCondition' => $cond
            );
        }

        // add to the columns from this joined table
        $this->_tableCols($correlationName, $cols);
        return $this;
    }

    /**
     * Adds a JOIN table and columns to the query.
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function join($name, $cond, $cols = '*')
    {
        return $this->joinInner($name, $cond, $cols);
    }

    /**
     * Add an INNER JOIN table and colums to the query
     * Rows in both tables are matched according to the expression
     * in the $cond argument.  The result set is comprised
     * of all cases where rows from the left table match
     * rows from the right table.
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinInner($name, $cond, $cols = '*')
    {
        return $this->_join(self::INNER, $name, $cond, $cols);
    }

    /**
     * Add a LEFT OUTER JOIN table and colums to the query
     * All rows from the left operand table are included,
     * matching rows from the right operand table included,
     * and the columns from the right operand table are filled
     * with NULLs if no row exists matching the left table.
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinLeft($name, $cond, $cols = '*')
    {
        return $this->_join(self::LEFT, $name, $cond, $cols);
    }

    /**
     * Add a RIGHT OUTER JOIN table and colums to the query.
     * Right outer join is the complement of left outer join.
     * All rows from the right operand table are included,
     * matching rows from the left operand table included,
     * and the columns from the left operand table are filled
     * with NULLs if no row exists matching the right table.
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinRight($name, $cond, $cols = '*')
    {
        return $this->_join(self::RIGHT, $name, $cond, $cols);
    }

    /**
     * Add a FULL OUTER JOIN table and colums to the query.
     * A full outer join is like combining a left outer join
     * and a right outer join.  All rows from both tables are
     * included, 
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinFull($name, $cond, $cols = '*')
    {
        return $this->_join(self::FULL, $name, $cond, $cols);
    }

    /**
     * Add a CROSS JOIN table and colums to the query.
     * A cross join is a cartesian product; there is no join condition.
     *
     * @param string $name The table name.
     * @param string $cond Join on this condition.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinCross($name, $cols = '*')
    {
        return $this->_join(self::CROSS, $name, null, $cols);
    }

    /**
     * Add a NATURAL JOIN table and colums to the query.
     * A natural join assumes an equi-join across any column(s)
     * that appear with the same name in both tables.
     * Only natural inner joins are supported by this API,
     * even though SQL permits natural outer joins as well.
     *
     * @param string $name The table name.
     * @param array|string $cols The columns to select from the joined table.
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function joinNatural($name, $cols = '*')
    {
        return $this->_join(self::NATURAL, $name, null, $cols);
    }

    /**
     * Adds a WHERE condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears. Array values are quoted and comma-separated.
     *
     * <code>
     * // simplest but non-secure
     * $select->where("id = $id");
     *
     * // secure (ID is quoted but matched anyway)
     * $select->where('id = ?', $id);
     *
     * // alternatively, with named binding
     * $select->where('id = :id');
     * </code>
     *
     * Note that it is more correct to use named bindings in your
     * queries for values other than strings. When you use named
     * bindings, don't forget to pass the values when actually
     * making a query:
     *
     * <code>
     * $db->fetchAll($select, array('id' => 5));
     * </code>
     *
     * @param string $cond The WHERE condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     */
    public function where($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts[self::WHERE]) {
            $this->_parts[self::WHERE][] = "AND $cond";
        } else {
            $this->_parts[self::WHERE][] = $cond;
        }

        return $this;
    }

    /**
     * Adds a WHERE condition to the query by OR.
     *
     * Otherwise identical to where().
     *
     * @param string $cond The WHERE condition.
     * @param string $val A value to quote into the condition.
     * @return void
     *
     * @see where()
     */
    public function orWhere($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts[self::WHERE]) {
            $this->_parts[self::WHERE][] = "OR $cond";
        } else {
            $this->_parts[self::WHERE][] = $cond;
        }

        return $this;
    }

    /**
     * Adds grouping to the query.
     *
     * @param string|array $spec The column(s) to group by.
     * @return void
     */
    public function group($spec)
    {
        settype($spec, 'array');

        foreach ($spec as $val) {
            $this->_parts[self::GROUP][] = trim($val);
        }

        return $this;
    }

    /**
     * Adds a HAVING condition to the query by AND.
     *
     * If a value is passed as the second param, it will be quoted
     * and replaced into the condition wherever a question-mark
     * appears. See {@link where()} for an example
     *
     * @param string $cond The HAVING condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     */
    public function having($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts[self::HAVING]) {
            $this->_parts[self::HAVING][] = "AND $cond";
        } else {
            $this->_parts[self::HAVING][] = $cond;
        }

        return $this;
    }

    /**
     * Adds a HAVING condition to the query by OR.
     *
     * Otherwise identical to orHaving().
     *
     * @param string $cond The HAVING condition.
     * @param string $val A single value to quote into the condition.
     * @return void
     *
     * @see having()
     */
    public function orHaving($cond)
    {
        if (func_num_args() > 1) {
            $val = func_get_arg(1);
            $cond = $this->_adapter->quoteInto($cond, $val);
        }

        if ($this->_parts[self::HAVING]) {
            $this->_parts[self::HAVING][] = "OR $cond";
        } else {
            $this->_parts[self::HAVING][] = $cond;
        }

        return $this;
    }

    /**
     * Adds a row order to the query.
     *
     * @param string|array $spec The column(s) and direction to order by.
     * @return void
     */
    public function order($spec)
    {
        settype($spec, 'array');

        // force 'ASC' or 'DESC' on each order spec, default is ASC.
        foreach ($spec as $key => $val) {
            if (preg_match('/\b(ASC|DESC)\s*$/i', $val, $matches)) {
                $direction = $matches[1];
            } else {
                $val .= ' ASC';
            }
            $this->_parts[self::ORDER][] = trim($val);
        }

        return $this;
    }

    /**
     * Sets a limit count and offset to the query.
     *
     * @param int $count The number of rows to return.
     * @param int $offset Start returning after this many rows.
     * @return void
     */
    public function limit($count = null, $offset = null)
    {
        $this->_parts[self::LIMIT_COUNT]  = (int) $count;
        $this->_parts[self::LIMIT_OFFSET] = (int) $offset;
        return $this;
    }

    /**
     * Sets the limit and count by page number.
     *
     * @param int $page Limit results to this page number.
     * @param int $rowCount Use this many rows per page.
     * @return void
     */
    public function limitPage($page, $rowCount)
    {
        $page     = ($page > 0)     ? $page     : 1;
        $rowCount = ($rowCount > 0) ? $rowCount : 1;
        $this->_parts[self::LIMIT_COUNT]  = (int) $rowCount;
        $this->_parts[self::LIMIT_OFFSET] = (int) $rowCount * ($page - 1);
        return $this;
    }

    /**
     * Adds to the internal table-to-column mapping array.
     *
     * @param string $tbl The table/join the columns come from.
     * @param string|array $cols The list of columns; preferably as
     * an array, but possibly as a string containing one column.
     * @return void
     */
    protected function _tableCols($correlationName, $cols)
    {
        settype($cols, 'array');

        foreach ($cols as $col) {
            $this->_parts[self::COLUMNS][$correlationName][] = trim($col);
        }
    }

    /**
     * Get part of the structured information for the currect query.
     *
     * @param string $part
     * @return mixed
     */
    public function getPart($part)
    {
        return $this->_parts[ strtolower($part) ];
    }

    /**
     * @param integer $fetchMode OPTIONAL
     * @return PDO_Statement|Zend_Db_Statement
     */
    public function query($fetchMode = null)
    {
        $stmt = $this->_adapter->query($this);
        if ($fetchMode == null) {
            $fetchMode = $this->_adapter->getFetchMode();
        }
        $stmt->setFetchMode($fetchMode);
        return $stmt;
    }

    /**
     * Clear parts of the Select object, or an individual part.
     *
     * @param string $part OPTIONAL
     * @return void
     */
    public function reset($part = null)
    {
        if ($part == null) {
            $this->_parts = $this->_partsInit;
        } else if (array_key_exists($part, $this->_partsInit)) {
            $this->_parts[$part] = $this->_partsInit[$part];
        }
    } 

}
