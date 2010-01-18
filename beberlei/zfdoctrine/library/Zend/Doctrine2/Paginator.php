<?php

class Zend_Doctrine2_Paginator implements Zend_Paginator_Adapter_Interface, Countable
{
    /**
     * @var \Doctrine\ORM\QueryBuilder
     */
    protected $_qb = null;

    /**
     * @var int
     */
    protected $_rowCount = null;

    /**
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    public function __construct(\Doctrine\ORM\QueryBuilder $qb)
    {
        $this->_qb = $qb;
    }

    /**
     * @param int $count
     * @return Zend_Doctrine2_Paginator
     */
    public function setRowCount($count)
    {
        $this->_rowCount = (int)$count;
        return $this;
    }

    /**
     * Returns the total number of rows in the collection.
     *
     * @return integer
     */
    public function count()
    {
        if($this->_rowCount > 0) {
            return $this->_rowCount;
        } else {
            
        }
    }

    /**
     * Returns an collection of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_qb->setFirstResult($offset)->setMaxResults($itemCountPerPage);
        return $this->_qb->getQuery()->getResult();
    }
}