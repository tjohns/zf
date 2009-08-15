<?php

class Zend_Entity_Mapper_NativeQuery extends Zend_Entity_Query_QueryAbstract
{
    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * @var Zend_Entity_Mapper_Abstract
     */
    protected $_loader = null;

    /**
     * @var Zend_Entity_Manager_Interface
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_Mapper_Select
     */
    protected $_select = null;

    /**
     * @var int
     */
    protected $_offset = null;

    /**
     * @var int
     */
    protected $_itemCountPerPage = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    /**
     *
     * @param Zend_Entity_Mapper_Select $select
     * @param Zend_Entity_Mapper_Loader_Interface $loader
     * @param Zend_Entity_Manager_Interface $manager
     */
    public function __construct(Zend_Entity_Mapper_Select $select, Zend_Entity_Mapper_Loader_Interface $loader, Zend_Entity_Manager_Interface $manager)
    {
        $this->_select = $select;
        $this->_loader = $loader;
        $this->_entityManager = $manager;

        $loader->initSelect($select);
        $loader->initColumns($select);
    }

    public function getResultList()
    {
        $stmt = $this->_select->query(null, $this->getParameters());
        $resultSet = $stmt->fetchAll();
        $stmt->closeCursor();

        return $this->_loader->processResultset($resultSet, $this->_entityManager);
    }

    public function setFirstResult($offset)
    {
        $this->_offset = $offset;
        $this->_select->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    public function setMaxResults($itemCountPerPage)
    {
        $this->_itemCountPerPage = $itemCountPerPage;
        $this->_select->limit($this->_itemCountPerPage, $this->_offset);
        return $this;
    }

    /**
     * Sets the total row count, either directly or through a supplied
     * query.  Without setting this, {@link getPages()} selects the count
     * as a subquery (SELECT COUNT ... FROM (SELECT ...)).  While this
     * yields an accurate count even with queries containing clauses like
     * LIMIT, it can be slow in some circumstances.  For example, in MySQL,
     * subqueries are generally slow when using the InnoDB storage engine.
     * Users are therefore encouraged to profile their queries to find
     * the solution that best meets their needs.
     *
     * @param  Zend_Db_Select|integer $totalRowCount Total row count integer
     *                                               or query
     * @return Zend_Paginator_Adapter_DbSelect $this
     * @throws Zend_Paginator_Exception
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Zend_Db_Select) {
            $columns = $rowCount->getPart(Zend_Db_Select::COLUMNS);

            $countColumnPart = $columns[0][1];

            if ($countColumnPart instanceof Zend_Db_Expr) {
                $countColumnPart = $countColumnPart->__toString();
            }

            $rowCountColumn = $this->_select->getAdapter()->foldCase(self::ROW_COUNT_COLUMN);

            // The select query can contain only one column, which should be the row count column
            if (false === strpos($countColumnPart, $rowCountColumn)) {
                /**
                 * @see Zend_Paginator_Exception
                 */
                require_once 'Zend/Paginator/Exception.php';

                throw new Zend_Paginator_Exception('Row count column not found');
            }

            $result = $rowCount->query(Zend_Db::FETCH_ASSOC)->fetch();

            $this->_rowCount = count($result) > 0 ? $result[$rowCountColumn] : 0;
        } else if (is_integer($rowCount)) {
            $this->_rowCount = $rowCount;
        } else {
            /**
             * @see Zend_Paginator_Exception
             */
            require_once 'Zend/Paginator/Exception.php';

            throw new Zend_Paginator_Exception('Invalid row count');
        }

        return $this;
    }

    public function count()
    {
        if ($this->_rowCount === null) {
            $rowCount = clone $this->_select;
            $db = $rowCount->getAdapter();

            /**
             * The DISTINCT and GROUP BY queries only work when selecting one column.
             * The question is whether any RDBMS supports DISTINCT for multiple columns, without workarounds.
             */
            if (true === $rowCount->getPart(Zend_Db_Select::DISTINCT)) {
                $columnParts = $rowCount->getPart(Zend_Db_Select::COLUMNS);

                $columns = array();

                foreach ($columnParts as $part) {
                    if ($part[1] == Zend_Db_Select::SQL_WILDCARD || $part[1] instanceof Zend_Db_Expr) {
                        $columns[] = $part[1];
                    } else {
                        $column = $db->quoteIdentifier($part[1], true);

                        if (!empty($part[0])) {
                            $column = $db->quoteIdentifier($part[0], true) . '.' . $column;
                        }

                        $columns[] = $column;
                    }
                }

                if (count($columns) == 1 && $columns[0] == Zend_Db_Select::SQL_WILDCARD) {
                    $groupPart = null;
                } else {
                    $groupPart = implode(',', $columns);
                }
            } else {
                $groupParts = $rowCount->getPart(Zend_Db_Select::GROUP);

                foreach ($groupParts as &$part) {
                    if (!($part == Zend_Db_Select::SQL_WILDCARD || $part instanceof Zend_Db_Expr)) {
                        $part = $db->quoteIdentifier($part, true);
                    }
                }

                $groupPart = implode(',', $groupParts);
            }

            $countPart  = empty($groupPart) ? 'COUNT(*)' : 'COUNT(DISTINCT ' . $groupPart . ')';
            $expression = new Zend_Db_Expr(
                $countPart . ' AS ' . $db->quoteIdentifier($db->foldCase(self::ROW_COUNT_COLUMN))
            );

            $rowCount->__toString(); // Workaround for ZF-3719 and related
            $rowCount->reset(Zend_Db_Select::COLUMNS)
                     ->reset(Zend_Db_Select::ORDER)
                     ->reset(Zend_Db_Select::LIMIT_OFFSET)
                     ->reset(Zend_Db_Select::GROUP)
                     ->reset(Zend_Db_Select::DISTINCT)
                     ->columns($expression);

            $this->setRowCount($rowCount);
        }

        return $this->_rowCount;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->_select, $method), $args);
    }

    public function __toString()
    {
        return $this->_select->assemble();
    }
}