<?php

abstract class Zend_Entity_Query_QueryAbstract implements Zend_Paginator_Adapter_Interface
{
    abstract public function getResultList();

    /**
     *
     * @throws Zend_Entity_Exception
     * @return Zend_Entity_Interface
     */
    public function getSingleResult()
    {
        $collection = $this->getResultList();
        if(count($collection) == 1) {
            return $collection[0];
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                count($collection)." elements found, but exactly one was asked for."
            );
        }
    }

    abstract public function setFirstResult($offset);

    abstract public function setMaxResults($itemCountPerPage);

    /**
     * @param int $offset
     * @param int $itemCountPerPage
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->setMaxResults($itemCountPerPage);
        $this->setFirstResult($offset);
        return $this->getResultList();
    }

    abstract public function __toString();
}