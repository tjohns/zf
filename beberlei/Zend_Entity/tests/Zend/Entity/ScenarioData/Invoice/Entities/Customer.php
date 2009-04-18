<?php
/*
 * Customer Entity
 */
class Customer implements Zend_Entity_Interface
{
    /*
     * Internal state
     */
    protected $_id;
    protected $_name;
    protected $_billingAddr;
    protected $_physicalAddr;
    protected $_account;

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
            'billingAddr' => $this->_billingAddr,
            'physicalAddr' => $this->_physicalAddr,
            'account' => $this->_account,
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
    public function getBillingAddr()
    {
        return $this->_billingAddr;
    }
    public function setBillingAddr(Address $addr)
    {
        $this->_billingAddr = $addr;
    }
    public function getPhysicalAddr()
    {
        return $this->_physicalAddr;
    }
    public function setPhysicalAddr(Address $addr)
    {
        $this->_physicalAddr = $addr;
    }
    public function getAccount()
    {
        return $this->_account;
    }
    public function setAccount(Account $acct)
    {
        $this->_account = $acct;
    }
}
