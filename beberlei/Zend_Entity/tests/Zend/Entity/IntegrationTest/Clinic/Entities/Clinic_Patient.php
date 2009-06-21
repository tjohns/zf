<?php

/**
 * A clinic patient.
 *
 * @entity
 */
class Clinic_Patient implements Zend_Entity_Interface
{
    /**
     * Patient Id
     *
     * @var integer
     * @primary
     */
    protected $id;

    /**
     * Patient Name
     *
     * @var string
     */
    protected $name;

    /**
     * Patients Social Security Number
     *
     * @var string
     */
    protected $socialSecurityNumber;

    /**
     * Patients Birth Date
     *
     * @var Zend_Date
     */
    protected $birthDate;

    /**
     * Occupancies
     *
     * @return Clinic_Occupancy[]
     */
    protected $occupancies;

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getSocialSecurityNumber() {
        return $this->socialSecurityNumber;
    }

    public function setSocialSecurityNumber($socialSecurityNumber) {
        $this->socialSecurityNumber = $socialSecurityNumber;
    }

    public function getBirthDate() {
        return $this->birthDate;
    }

    public function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;
    }
    
    public function getState()
    {
        return array(
            'id'                    => $this->id,
            'name'                  => $this->name,
            'socialSecurityNumber'  => $this->socialSecurityNumber,
            'birthDate'             => $this->birthDate,
            'occupancies'           => $this->occupancies,
        );
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            if(property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }
}