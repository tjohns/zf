<?php

class Zend_Db_Mapper_SqlQueryPaginator extends Zend_Paginator_Adapter_DbSelect
{
    protected $_loader = null;

    protected $_rsm = null;

    public function __construct(Zend_Db_Mapper_QueryObject $qo, $loader, $rsm)
    {
        parent::__construct($qo);
        $this->_loader = $loader;
        $this->_rsm = $rsm;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_select->limit($itemCountPerPage, $offset);

        $result = $this->_select->query()->fetchAll();
        return $this->_loader->processResultset($result, $this->_rsm);
    }
}