<?php
/*
 * Account Entity
 */
class Account implements Zend_Entity_Interface
{
    /*
     * Internal state
     */
    protected $_id;
    protected $_accountNo;

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
            'accountNo' => $this->_accountNo,
        );
    }

    /*
     * Property Accessors
     */
    public function getId()
    {
        return $this->_id;
    }
    public function getAccountNo()
    {
        return $this->_accountNo;
    }
    public function setAccountNo($no)
    {
        $this->_accountNo = $no;
    }

    /*
     * Operations
     */

    /**
     * Calculates and returns this Account's current balance
     *
     * @returns float
     */
    public function getBalance()
    {
    }

    /**
     * Records a partial or whole payment of the given Invoice to this Account
     *
     * @param Invoice Invoice to pay
     * @param float Payment amount
     */
    public function payInvoice(Invoice $inv, $amount)
    {
    }
}
