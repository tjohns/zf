<?php

/**
 * An occupancy defines which patient lies in which bed during which timespan.
 *
 * @entity
 */
class Clinic_Occupancy implements Zend_Entity_Interface
{
    /**
     * Occupancy Id
     *
     * @var integer
     */
    protected $id;

    /**
     * Patient
     *
     * @var Clinic_Patient
     * @hasOne
     */
    protected $patient;

    /**
     * Bed
     *
     * @var Clinic_Bed
     * @hasOne
     */
    protected $bed;

    /**
     * Station
     *
     * @var Clinic_Station
     * @hasOne
     */
    protected $station;

    /**
     * Day of first occupation
     * 
     * @var integer
     */
    protected $occupiedFrom;

    /**
     * Estimated day of leave
     * 
     * @var integer
     */
    protected $occupiedTo;

    public function getId() {
        return $this->id;
    }

    public function getPatient() {
        return $this->patient;
    }

    public function setPatient(Clinic_Patient $patient) {
        $this->patient = $patient;
    }

    public function getBed() {
        return $this->bed;
    }

    public function setBed(Clinic_Bed $bed) {
        $this->bed = $bed;
    }

    public function getOccupiedFrom() {
        return $this->occupiedFrom;
    }

    public function setOccupiedFrom($occupiedFrom) {
        $this->occupiedFrom = $occupiedFrom;
    }

    public function getOccupiedTo() {
        return $this->occupiedTo;
    }

    public function setOccupiedTo($occupiedTo) {
        $this->occupiedTo = $occupiedTo;
    }

    public function getStation()
    {
        return $this->station;
    }

    public function setStation($station)
    {
        $this->station = $station;
    }

    public function getState()
    {
        return array(
            'id'            => $this->id,
            'bed'           => $this->bed,
            'patient'       => $this->patient,
            'station'       => $this->station,
            'occupiedTo'    => $this->occupiedTo,
            'occupiedFrom'  => $this->occupiedFrom,
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