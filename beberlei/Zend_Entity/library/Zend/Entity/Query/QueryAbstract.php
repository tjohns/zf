<?php

abstract class Zend_Entity_Query_QueryAbstract implements Zend_Paginator_Adapter_Interface
{
    /**
     * @var array
     */
    protected $_params = array();

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

    /**
     * @param  string|int $name
     * @param  mixed $value
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function setParameter($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     *
     * @param  array $params
     * @return Zend_Entity_Query_QueryAbstract
     */
    public function setParameters($params)
    {
        foreach($params AS $k => $v) {
            $this->setParameter($k, $v);
        }
        return $this;
    }

    /**
     * @param  string $name
     * @return mixed
     */
    public function getParameter($name)
    {
        if(isset($this->_params[$name])) {
            return $this->_params[$name];
        }
        return null;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->_params;
    }

    abstract public function __toString();
}