<?php
/*
 * Item Entity
 */
class Item implements Zend_Entity_Interface
{
    /*
     * Internal state
     */
    protected $_id;
    protected $_name;
    protected $_sku;

    /*
     * From Zend_Entity_Interface
     */
    public function setState(array $state)
    {
        foreach ($state as $k => $v) {
            $this->{'_'.$k} = $v;
        }
    }
    public function getState()
    {
        return array(
            'id' => $this->_id,
            'name' => $this->_name,
            'sku' => $this->_billingAddr,
        );
    }

    /*
     * Property Accessors
     */
    public function getId()
    {
        return $this->_id;
    }
    public function getName()
    {
        return $this->_name;
    }
    public function setName($name)
    {
        $this->_name = $name;
    }
    public function getSku()
    {
        return $this->_sku;
    }
    public function setSku($sku)
    {
        $this->_sku = $sku;
    }
}
