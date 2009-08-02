<?php

class Clinic_Bed implements Zend_Entity_Interface
{
    /**
     * Bed Id
     * 
     * @var int
     */
    protected $id;

    /**
     * Station
     *
     * @var Clinic_Station
     */
    protected $station;

    /**
     * OccupancyPlan
     * 
     * @var Zend_Entity_Collection
     */
    protected $occupancyPlan;

    public function getId()
    {
        return $this->id;
    }

    public function getStation()
    {
        return $this->station;
    }

    public function setStation($station)
    {
        $this->station = $station;
    }

    /**
     * Return all planed occupancies.
     *
     * @return Clinic_Occupancy[]
     */
    public function getOccupancyPlan()
    {
        return $this->occupancyPlan;
    }

    /**
     * Add patient as new occupancy on the given time interval.
     *
     * @param  Clinic_Patient $patient
     * @param  integer $from
     * @param  integer $to
     * @return Clinic_Occupancy
     */
    public function addOccupancy(Clinic_Patient $patient, $from, $to)
    {
        if($this->isCurrentlyEmpty()) {
            $occupancy = new Clinic_Occupancy();
            $occupancy->setBed($this);
            $occupancy->setPatient($patient);
            $occupancy->setStation($this->getStation());
            $occupancy->setOccupiedFrom($from);
            $occupancy->setOccupiedTo($to);

            // TODO: check if this does not collide with already "booked" occupancies

            $this->occupancyPlan[] = $occupancy;
            return $occupancy;
        }
        return null;
    }

    public function isCurrentlyEmpty()
    {
        foreach($this->getOccupancyPlan() AS $occupancy) {
            $from = $occupancy->getOccupiedFrom();
            $to   = $occupancy->getOccupiedTo();
            if($from <= time() && $to >= time()) {
                return false;
            }
        }
        return true;
    }

    public function getState()
    {
        return array(
            'id'      => $this->id,
            'station' => $this->station,
            'occupancyPlan' => $this->occupancyPlan,
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