<?php
/*
 * InvoiceItem Entity
 */
class InvoiceItem implements Zend_Entity_Interface
{
    /*
     * Internal state
     */
    protected $_invoice;
    protected $_lineNo;
    protected $_taxable;
    protected $_description;
    protected $_amount;
    protected $_item;

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
            'invoice' => $this->_invoice,
            'lineNo' => $this->_lineNo,
            'taxable' => $this->_taxable,
            'description' => $this->_description,
            'amount' => $this->_amount,
            'item' => $this->_item,
        );
    }

    /*
     * Property Accessors
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }
    public function getLineNo()
    {
        return $this->_lineNo;
    }
    public function setLineNo($no)
    {
        $this->_lineNo = $no;
    }
    public function getTaxable()
    {
        return $this->_taxable;
    }
    public function setTaxable($taxable)
    {
        $this->_taxable = $taxable;
    }
    public function getDescription()
    {
        return $this->_description;
    }
    public function setDescription($descr)
    {
        $this->_description = $descr;
    }
    public function getAmount()
    {
        return $this->_amount;
    }
    public function setAmount($amt)
    {
        $this->_amount = $amt;
    }
    public function getItem()
    {
        return $this->_item;
    }
    public function setItem(Item $item)
    {
        $this->_item = $item;
    }
}
