<?php

abstract class Zend_Entity_ScenarioData_Clinic_ScenarioTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     *
     * @var Zend_Entity_Manager
     */
    protected $_entityManager = null;

    public function setUp()
    {
        parent::setUp();
        
        $path = dirname(__FILE__)."/Definition/";
        $dbAdapter = $this->getAdapter();
        $this->_entityManager = new Zend_Entity_Manager($dbAdapter, array('resource' => new Zend_Entity_Resource_Code($path)));
    }

    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/Fixtures/OnePatientTwoStationFourBeds.xml');
    }

    public function testFindById()
    {
        $patient = $this->_entityManager->load("Clinic_Patient", 1);

        $this->assertTrue($patient instanceof Zend_Entity_Interface);
        $this->assertEquals(1,            $patient->getId());
        $this->assertEquals("John Doe", $patient->getName());
        $this->assertEquals("123456789",  $patient->getSocialSecurityNumber());
        $this->assertEquals("1972-01-01", $patient->getBirthDate());
    }

    public function testFindByQuery()
    {
        $select = $this->_entityManager->select("Clinic_Patient");
        $select->where("name = ?", "John Doe");

        $patients = $this->_entityManager->find("Clinic_Patient", $select);
        $this->assertTrue($patients instanceof Zend_Entity_Collection, "EntityManager::find() has to return an Entity Collection");
        $this->assertFalse($patients instanceof Zend_Entity_Mapper_LazyLoad_Collection, "EntityManager::find() never returns a lazy load collection as root node.");

        $this->assertEquals(1, count($patients), "Database contains exactly 1 patient in its unmodified state.");

        $patient = $patients->current();

        $this->assertTrue($patient instanceof Zend_Entity_Interface, "Collection has to return Entities.");
        $this->assertEquals(1,            $patient->getId());
        $this->assertEquals("John Doe", $patient->getName());
        $this->assertEquals("123456789",  $patient->getSocialSecurityNumber());
        $this->assertEquals("1972-01-01", $patient->getBirthDate());
    }

    public function testFindOneByQuery()
    {
        $select = $this->_entityManager->select("Clinic_Patient");
        $select->where("name = ?", "John Doe");

        $patient = $this->_entityManager->findOne("Clinic_Patient", $select);

        $this->assertTrue($patient instanceof Zend_Entity_Interface, "EntityManager::findOne has to return an entity.");
        $this->assertEquals(1,            $patient->getId());
        $this->assertEquals("John Doe", $patient->getName());
        $this->assertEquals("123456789",  $patient->getSocialSecurityNumber());
        $this->assertEquals("1972-01-01", $patient->getBirthDate());
    }

    public function testInsertNewPatient()
    {
        $patient = new Clinic_Patient();
        $patient->setName("Daniel Daniellson");
        $patient->setBirthDate("1980-01-15");
        $patient->setSocialSecurityNumber("9027498");

        $this->_entityManager->save($patient);

        $newPatientId = $patient->getId();
        $this->assertNotNull($newPatientId);
        $this->assertTrue( is_numeric($newPatientId) && ($newPatientId>0) );

        $this->_entityManager->save($patient);
        $this->assertEquals($newPatientId, $patient->getId());
    }

    public function testDeletePatient()
    {
        $patient = $this->_entityManager->load("Clinic_Patient", 1);
        $this->_entityManager->delete($patient);

        $this->assertNull($patient->getId());
    }

    public function testIdentityMapWorksAfterInsertAndThenRetrievalOfNewPatient()
    {
        $patient = new Clinic_Patient();
        $patient->setName("Daniel Daniellson");
        $patient->setBirthDate("1980-01-15");
        $patient->setSocialSecurityNumber("9027498");

        $this->_entityManager->save($patient);

        $newPatientId = $patient->getId();

        $retrievedPatient = $this->_entityManager->load("Clinic_Patient", $newPatientId);

        $this->assertTrue($patient === $retrievedPatient, "References of newly created patient has to match with its retrieved counterpart because of identity map.");
    }

    public function testFetchingRelatedBedsOfAnyGivenStation()
    {
        $station = $this->_entityManager->load("Clinic_Station", 1);
        $this->assertEquals(2, count($station->getBeds()));
    }

    public function testNoCurrentOccupanciesInStation()
    {
        $station = $this->_entityManager->load("Clinic_Station", 1);
        $this->assertEquals(0, count($station->getCurrentOccupancies()));
    }

    public function testAddEmergencyOcuupancyToStation()
    {
        $station = $this->_entityManager->load("Clinic_Station", 1);

        $patient = new Clinic_Patient();
        $patient->setName("John Doe");
        $patient->setBirthDate("2008-01-04");
        $patient->setSocialSecurityNumber("98203481");

        $occupancy = $station->requestEmergencyOccupancy($patient, 7);

        // Some hazzle to get around the Lazy Load Container != original problem :-)
        // TODO: If entity is already loaded no lazy load container is required
        $station2  = $occupancy->getStation();
        $patient2  = $occupancy->getPatient();
        $this->assertEquals($station->getId(), $station2->getId());
        $this->assertEquals($station, $station2);
        $this->assertEquals($patient->getId(), $patient2->getId());
        $this->assertEquals($patient, $patient2);

        // Saving $patient is not required, since $occupancy cascades the save operation!
        //$this->_entityManager->save($patient);
        $this->_entityManager->save($occupancy);
    }

    public function testAddBedsToStationAndCascadeSaving()
    {
        $station = $this->_entityManager->load("Clinic_Station", 1);
        $station->increaseNumberOfBeds(10);

        $this->_entityManager->save($station);

        $this->assertEquals(12, count($station->getBeds()));
        foreach($station->getBeds() AS $bed) {
            $this->assertNotNull($bed->getId());
        }
    }
}