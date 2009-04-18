<?php
/*
 * Address Entity
 */
class Address implements Zend_Entity_Interface
{
    /*
     * Internal state
     */
    protected $_id;
    protected $_streetName;
    protected $_unitNo;
    protected $_addrLine2;
    protected $_city;
    protected $_state;
    protected $_zipcode;

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
            'streetName' => $this->_streetName,
            'unitNo' => $this->_unitNo,
            'addrLine2' => $this->_addrLine2,
            'city' => $this->_city,
            'state' => $this->_state,
            'zipcode' => $this->_zipcode,
        );
    }

    /*
     * Property Accessors
     */
    public function getId()
    {
        return $this->_id;
    }
    public function getStreetName()
    {
        return $this->_streetName;
    }
    public function setStreetName($name)
    {
        $this->_streetName = $name;
    }
    public function getUnitNo()
    {
        return $this->_unitNo;
    }
    public function setUnitNo($no)
    {
        $this->_unitNo = $no;
    }
    public function getAddrLine2()
    {
        return $this->_addrLine2;
    }
    public function setAddrLine2($line)
    {
        $this->_addrLine2 = $line;
    }
    public function getCity()
    {
        return $this->_city;
    }
    public function setCity($city)
    {
        $this->_city = $city;
    }
    public function getZipcode()
    {
        return $this->_zipcode;
    }
    public function setZipcode($zip)
    {
        $this->_zipcode = $zip;
    }
}
