<?php

require_once "Clinic/Entities/Clinic_Bed.php";
require_once "Clinic/Entities/Clinic_Occupancy.php";
require_once "Clinic/Entities/Clinic_Patient.php";
require_once "Clinic/Entities/Clinic_Station.php";

require_once "Zend/Test/PHPUnit/DatabaseTestCase.php";

class Zend_Entity_IntegrationTest_ClinicIntegrationTest extends Zend_Test_PHPUnit_DatabaseTestCase
{
    /**
     *
     * @var Zend_Entity_Manager
     */
    protected $_entityManager = null;

    protected function getConnection()
    {
        $db = Zend_Db::factory("pdo_mysql", array(
                "host" => ZEND_ENTITY_MYSQL_HOSTNAME,
                "username" => ZEND_ENTITY_MYSQL_USERNAME,
                "password" => ZEND_ENTITY_MYSQL_PASSWORD,
                "dbname" => ZEND_ENTITY_MYSQL_DATABASE
            ));
        return $this->createZendDbConnection($db, ZEND_ENTITY_MYSQL_DATABASE);
    }

    public function setUp()
    {
        parent::setUp();

        $path = dirname(__FILE__)."/Clinic/Definition/";
        $dbAdapter = $this->getAdapter();
        $this->_entityManager = new Zend_Entity_Manager($dbAdapter, array('metadataFactory' => new Zend_Entity_MetadataFactory_Code($path)));
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

        $ds = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable('patients', 'SELECT * FROM patients');

        $this->assertDataSetsEqual(
            $this->createFlatXMLDataSet(dirname(__FILE__)."/Clinic/Fixtures/TwoPatients.xml"),
            $ds
        );
    }

    public function testDeletePatient()
    {
        $this->markTestSkipped();
        $patient = $this->_entityManager->load("Clinic_Patient", 1);
        $this->_entityManager->delete($patient);

        $this->assertNull($patient->getId());

        $ds = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable('patients', 'SELECT * FROM patients');

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

        $this->_entityManager->save($patient);

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

        $this->_entityManager->save($patient);
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