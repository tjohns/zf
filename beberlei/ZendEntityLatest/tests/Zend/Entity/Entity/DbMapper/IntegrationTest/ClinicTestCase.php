<?php

require_once dirname(__FILE__)."/../../../../TestHelper.php";

require_once "Clinic/Entities/Clinic_Bed.php";
require_once "Clinic/Entities/Clinic_Occupancy.php";
require_once "Clinic/Entities/Clinic_Patient.php";
require_once "Clinic/Entities/Clinic_Station.php";

require_once "Zend/Test/PHPUnit/DatabaseTestCase.php";

abstract class Zend_Entity_DbMapper_IntegrationTest_ClinicTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     * @var Zend_Entity_Manager
     */
    protected $_entityManager = null;

    /**
     * @var Zend_Entity_MetadataFactory_Code
     */
    protected $_metadataFactory = null;

    public function tearDown()
    {
        $this->getAdapter()->closeConnection();
    }

    public function setUp()
    {
        parent::setUp();

        $path = dirname(__FILE__)."/Clinic/Definition/";
        $dbAdapter = $this->getAdapter();
        $this->_metadataFactory = new Zend_Entity_MetadataFactory_Code($path);
        $this->init($this->_metadataFactory);
        $this->_entityManager = new Zend_Entity_Manager(array('adapter' => $dbAdapter, 'metadataFactory' => $this->_metadataFactory));
    }

    public function init(Zend_Entity_MetadataFactory_Code $mf)
    {
        
    }


    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/Clinic/Fixtures/OnePatientTwoStationFourBeds.xml');
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

    public function testPatientGetResultList()
    {
        $query = new Zend_Db_Mapper_SqlQueryBuilder($this->_entityManager);
        $query->from("zfclinic_patients")
              ->with("Clinic_Patient")
              ->where("name = ?", "John Doe");

        $patients = $query->getResultList();
        $this->assertType('array', $patients, "AbstractQuery::getResultList() has to return an array as collection");

        $this->assertEquals(1, count($patients), "Database contains exactly 1 patient in its unmodified state.");

        $patient = $patients[0];

        $this->assertTrue($patient instanceof Zend_Entity_Interface, "Collection has to return Entities.");
        $this->assertEquals(1, $patient->getId());
        $this->assertEquals("John Doe", $patient->getName());
        $this->assertEquals("123456789", $patient->getSocialSecurityNumber());
        $this->assertEquals("1972-01-01", $patient->getBirthDate());
    }

    public function testPatientGetSingleResult()
    {
        $query = new Zend_Db_Mapper_SqlQueryBuilder($this->_entityManager);
        $query->from("zfclinic_patients")
              ->with("Clinic_Patient")
              ->where("name = ?", "John Doe");

        $patient = $query->getSingleResult();

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

        $this->_entityManager->persist($patient);

        $newPatientId = $patient->getId();
        $this->assertNotNull($newPatientId);
        $this->assertTrue( is_numeric($newPatientId) && ($newPatientId>0) );

        $this->_entityManager->persist($patient);
        $this->assertEquals($newPatientId, $patient->getId());

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable('zfclinic_patients', 'SELECT name, social_security_number, birth_date FROM zfclinic_patients');

        $this->assertDataSetsEqual(
            $this->createFlatXMLDataSet(dirname(__FILE__)."/Clinic/Fixtures/TwoPatients.xml"),
            $ds
        );
    }

    public function testDeletePatient()
    {        
        $patient = $this->_entityManager->load("Clinic_Patient", 1);
        $this->_entityManager->remove($patient);

        $this->assertNull($patient->getId());

        $ds = new Zend_Test_PHPUnit_Db_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable('zfclinic_patients', 'SELECT * FROM zfclinic_patients');

        $this->assertDataSetsEqual(
            $this->createFlatXMLDataSet(dirname(__FILE__)."/Clinic/Fixtures/NoPatients.xml"),
            $ds
        );
    }

    public function testIdentityMapWorksAfterInsertAndThenRetrievalOfNewPatient()
    {
        $patient = new Clinic_Patient();
        $patient->setName("Daniel Daniellson");
        $patient->setBirthDate("1980-01-15");
        $patient->setSocialSecurityNumber("9027498");

        $this->_entityManager->persist($patient);

        $newPatientId = $patient->getId();

        $retrievedPatient = $this->_entityManager->load("Clinic_Patient", $newPatientId);

        $this->assertSame($patient, $retrievedPatient);
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

    public function testAddEmergencyOccupancyToStation()
    {
        $station = $this->_entityManager->load("Clinic_Station", 1);

        $patient = new Clinic_Patient();
        $patient->setName("John Doe");
        $patient->setBirthDate("2008-01-04");
        $patient->setSocialSecurityNumber("98203481");

        $occupancy = $station->requestEmergencyOccupancy($patient, 7);

        $this->assertSame($occupancy->getPatient(), $patient);
        $this->assertSame($occupancy->getStation(), $station);

        $this->_entityManager->persist($patient);
        $this->_entityManager->persist($occupancy);
    }

    public function testAddBedsToStationAndCascadeSaving()
    {
        $station = $this->_entityManager->load("Clinic_Station", 1);
        $station->increaseNumberOfBeds(10);

        $this->_entityManager->persist($station);

        $this->assertEquals(12, count($station->getBeds()));
        foreach($station->getBeds() AS $bed) {
            $this->assertNotNull($bed->getId());
        }
    }

    public function testSqlNativeQuery_OccupancyStationPatient()
    {
        $sql = "SELECT o.id AS id1, o.patient_id AS patient_id2, ".
            "o.bed_id AS bed_id3, o.station_id AS station_id4, ".
            "o.occupied_from AS occupied_from5, o.occupied_to AS occupied_to6, ".
            "s.id AS id7, s.name AS name8, ".
            "b.id AS id9, b.station_id AS station_id10, ".
            "p.id AS id11, p.name AS name12, ".
            "p.social_security_number AS social_security_number13, ".
            "p.birth_date AS birth_date14 ".
            "FROM zfclinic_occupancies o ".
            "INNER JOIN zfclinic_stations s ON s.id = o.station_id ".
            "INNER JOIN zfclinic_beds b ON b.id = o.bed_id ".
            "INNER JOIN zfclinic_patients p ON p.id = o.patient_id ".
            "WHERE o.id = ?";

        $rsm = new Zend_Entity_Query_ResultSetMapping();
        $rsm->addEntity("Clinic_Occupancy", "o")
            ->addProperty("o", "id1", "id")
            ->addProperty("o", "patient_id2", "patient")
            ->addProperty("o", "bed_id3", "bed")
            ->addProperty("o", "station_id4", "station")
            ->addProperty("o", "occupied_from5", "occupied_from")
            ->addProperty("o", "occupied_to6", "occupied_to")
            ->addJoinedEntity("Clinic_Station", "s")
            ->addProperty("s", "id7", "id")
            ->addProperty("s", "name8", "name")
            ->addJoinedEntity("Clinic_Bed", "b")
            ->addProperty("b", "id9", "id")
            ->addProperty("b", "station_id10", "station")
            ->addJoinedEntity("Clinic_Patient", "p")
            ->addProperty("p", "id11", "id")
            ->addProperty("p", "name12", "name")
            ->addProperty("p", "social_security_number13", "social_security_number")
            ->addProperty("p", "birth_date14", "birth_date");

        $q = $this->_entityManager->createNativeQuery($sql, $rsm);
        $q->bindParam(1, 1);

        $occupancy = $q->getSingleResult();
        /* @var $occupancy Clinic_Occupancy */

        $this->assertType('Clinic_Occupancy', $occupancy);
        $this->assertEquals(1, $occupancy->getId());
        $this->assertType('Clinic_Station', $occupancy->getStation());
        $this->assertType('Clinic_Bed', $occupancy->getBed());
        $this->assertType('Clinic_Patient', $occupancy->getPatient());
    }
}