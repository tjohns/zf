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
    protected $social_security_number;

    /**
     * Patients Birth Date
     *
     * @var Zend_Date
     */
    protected $birth_date;

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
        return $this->social_security_number;
    }

    public function setSocialSecurityNumber($socialSecurityNumber) {
        $this->social_security_number = $socialSecurityNumber;
    }

    public function getBirthDate() {
        return $this->birth_date;
    }

    public function setBirthDate($birthDate) {
        $this->birth_date = $birthDate;
    }
    
    public function getState()
    {
        return array(
            'id'                    => $this->id,
            'name'                  => $this->name,
            'social_security_number'  => $this->social_security_number,
            'birth_date'             => $this->birth_date,
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