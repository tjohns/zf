<?php
/*
 * Invoice Entity
 */
class Invoice implements Zend_Entity_Interface
{
    /*
     * Internal state
     */
    protected $_id;
    protected $_date;
    protected $_description;
    protected $_items;
    protected $_customer;

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
            'date' => $this->_date,
            'description' => $this->_description,
            'items' => $this->_items,
            'customer' => $this->_customer,
        );
    }

    /*
     * Property Accessors
     */
    public function getId()
    {
        return $this->_id;
    }
    public function getDate()
    {
        return $this->_date;
    }
    public function setDate($date)
    {
        $this->_date = $date;
    }
    public function getDescription()
    {
        return $this->_description;
    }
    public function setDescription($descr)
    {
        $this->_description = $descr;
    }
    public function getItems()
    {
        return $this->_items;
    }
    public function setItems(Zend_Entity_List $items)
    {
        $this->_items = $items;
    }
    public function getCustomer()
    {
        return $this->_customer;
    }
    public function setCustomer(Customer $customer)
    {
        $this->_customer = $customer;
    }

    /*
     * Operations
     */

    /**
     * Calculates and returns the total value of this Invoice
     *
     * @returns float
     */
    public function calculateTotal()
    {
        $total = 0;
        foreach ($this->_items as $invItem) {
            $total += $invItem->getAmount();
        }
        return $total;
    }
}
