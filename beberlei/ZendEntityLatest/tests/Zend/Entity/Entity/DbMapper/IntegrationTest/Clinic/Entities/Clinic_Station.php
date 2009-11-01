<?php

/**
 * A station of a clinic. Based on its type of patients different allocation strategies can be used.
 *
 * @entity
 */
class Clinic_Station implements Zend_Entity_Interface
{
    /**
     * Station Id
     *
     * @var int
     */
    protected $id;

    /**
     * Station Name
     *
     * @var string
     */
    protected $name;

    /**
     * Current Occupancies
     *
     * @var Clinic_Occupancy[]
     */
    protected $currentOccupancies;

    /**
     * Beds of this station
     * 
     * @var Clinic_Bed[]
     */
    protected $beds;

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getBeds()
    {
        return $this->beds;
    }

    public function getBedAllocationStrategy()
    {
        return $this->bedAllocationStrategy;
    }

    public function setBedAllocationStrategy($bedAllocationStrategy)
    {
        $this->bedAllocationStrategy = $bedAllocationStrategy;
    }

    public function getCurrentOccupancies()
    {
        return $this->currentOccupancies;
    }

    /**
     * Request an emergency occupancy for this clinic patient.
     * 
     * @param  Clinic_Patient $patient
     * @return Clinic_Occupancy
     */
    public function requestEmergencyOccupancy(Clinic_Patient $patient, $requiredDays)
    {
        if(count($this->getCurrentOccupancies()) < count($this->getBeds())) {
            $today             = time();
            $expectedLeaveDate = $today + $requiredDays * 3600 * 24;
            $bed = $this->getEmptyBed();
            return $bed->addOccupancy($patient, $today, $expectedLeaveDate);
        }
        return null;
    }

    /**
     * Get currently empty bed or throw exception.
     * 
     * @throws Exception
     * @return Clinic_Bed
     */
    protected function getEmptyBed()
    {
        foreach($this->getBeds() AS $bed) {
            if($bed->isCurrentlyEmpty() == true) {
                return $bed;
            }
        }
        throw new Exception("No empty bed found! Please refer to other clinic/station!");
    }

    /**
     * Request a routine occupancy for this patient.
     *
     * An occupancy is returned that has the date where the patients stay starts.
     *
     * @param  Clinic_Patient $patient
     * @return Clinic_Occupancy
     */
    public function requestRoutineOccupancy(Clinic_Patient $patient, $requiredDays)
    {
        // Not implemented: This algorihm should look to find current and future free time
        // to place the patient.
    }

    /**
     * Increase the number of beds of the station by the given number.
     *
     * Used by management to increase number of beds when capacity needs to be extended.
     *
     * @param  integer $byCount
     * @return Clinic_Bed[]
     */
    public function increaseNumberOfBeds($byCount)
    {
        for($i = 0; $i  < $byCount; $i++) {
            $bed = new Clinic_Bed();
            $bed->setStation($this);
            $this->beds[] = $bed;
        }
        return $this->getBeds();
    }

    /**
     * Decrease the number of beds of the station by the given number.
     *
     * Used by management to decrease number of beds when capacity needs to be reduced.
     *
     * @param  integer $byCount
     * @return Clinic_Bed[]
     */
    public function decreaseNumberOfBeds($byCount)
    {
        // Only non-occupied beds can be removed
    }

    public function setState(array $state)
    {
        foreach($state AS $k => $v) {
            if(property_exists($this, $k)) {
                $this->$k = $v;
            }
        }
    }

    public function getState()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'beds' => $this->beds,
            'currentOccupancies' => $this->currentOccupancies,
        );
    }
}