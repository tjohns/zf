<?php

require_once dirname(__FILE__)."/../../TestHelper.php";

require_once "University/Entities/Student.php";
require_once "University/Entities/Course.php";

class Zend_Entity_IntegrationTest_UniversityIntegrationTest extends Zend_Test_PHPUnit_DatabaseTestCase
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
    
    protected function getDataSet()
    {
        return $this->createFlatXMLDataSet(dirname(__FILE__).'/University/Fixtures/universitySeed.xml');
    }

    public function setUp()
    {
        parent::setUp();

        $path = dirname(__FILE__)."/University/Definitions/";
        $dbAdapter = $this->getAdapter();
        $this->_entityManager = new Zend_Entity_Manager($dbAdapter, array('metadataFactory' => new Zend_Entity_MetadataFactory_Code($path)));
    }

    /**
     * @dataProvider dataStudents
     * @param int $id
     * @param string $name
     * @param int $studentId
     */
    public function testStudentGetById($id, $name, $studentId)
    {
        $student = $this->_entityManager->load("ZendEntity_Student", $id);
        $this->assertEquals($id, $student->id);
        $this->assertEquals($name, $student->name);
        $this->assertEquals($studentId, $student->studentId);
    }

    static public function dataStudents()
    {
        return array(
            array(1, 'Albert Einstein', '1234'),
            array(2, 'Ludwig von Mises', '1078'),
            array(3, 'Adam Smith', '1776'),
        );
    }

    /**
     * @dataProvider dataCourses
     * @param int $id
     * @param string $name
     */
    public function testCourseGetById($id, $name)
    {
        $course = $this->_entityManager->load("ZendEntity_Course", $id);
        $this->assertEquals($id, $course->id);
        $this->assertEquals($name, $course->name);
    }

    static public function dataCourses()
    {
        return array(
            array(1, 'Human Action'),
            array(2, 'Applied Financial Markets'),
        );
    }

    /**
     * @dataProvider dataStudentCurrentCourses
     * @param int $id
     * @param array $courseIds
     */
    public function testStudentCurrentCourses($id, $courseIds)
    {
        $student = $this->_entityManager->load("ZendEntity_Student", $id);
        $this->assertEquals(count($courseIds), count($student->currentCourses));
        foreach($student->currentCourses AS $course) {
            $courseId = array_shift($courseIds);
            $this->assertEquals($courseId, $course->id);
        }
    }

    static public function dataStudentCurrentCourses()
    {
        return array(
            array(1, array(1, 2)),
            array(2, array(1, 3)),
            array(3, array(2)),
        );
    }

    public function testGetStudent_ConditionalOnCourseId()
    {
        $select = $this->_entityManager->createNativeQuery("ZendEntity_Student");
        $select->joinInner(
            "university_students_semester_courses",
            "university_students_semester_courses.student_id = university_students.student_id"
        )->where("university_students_semester_courses.course_id = 1");

        $students = $select->getResultList();

        $this->assertEquals(2, count($students));
        $this->assertEquals(1, $students[0]->id);
        $this->assertEquals(2, $students[1]->id);
    }

    public function testFindOneStudent_ConditionalOnCourseId()
    {
        $select = $this->_entityManager->createNativeQuery("ZendEntity_Student");
        $select->joinInner(
            "university_students_semester_courses",
            "university_students_semester_courses.student_id = university_students.student_id"
        )->where("university_students_semester_courses.course_id = 3");

        $student = $select->getSingleResult();

        $this->assertEquals(2, $student->id);
        $this->assertEquals("Ludwig von Mises", $student->name);
    }

    public function testForExistantStudent_SaveAdditionalCourse()
    {
        $student = $this->_entityManager->load("ZendEntity_Student", 2);
        $course = $this->_entityManager->load("ZendEntity_Course", 2);
        $student->currentCourses[] = $course;

        $this->_entityManager->save($student);

        $ds = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable("university_students_semester_courses");

        $this->assertDataSetsEqual(
            $this->createFlatXMLDataSet(dirname(__FILE__)."/University/Fixtures/ExistingStudentAdditionalCoursesAssertion.xml"),
            $ds
        );
    }

    public function testNewStudent_SaveWithCourses()
    {
        $course = $this->_entityManager->load("ZendEntity_Course", 1);

        $student = new ZendEntity_Student();
        $student->id = 4;
        $student->name = "Friedrich August von Hayek";
        $student->studentId = "9876";
        $student->currentCourses = new Zend_Entity_Collection();
        $student->currentCourses[] = $course;

        $this->_entityManager->save($student);

        $ds = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable("university_students");
        $ds->addTable("university_students_semester_courses");

        $this->assertDataSetsEqual(
            $this->createFlatXMLDataSet(dirname(__FILE__)."/University/Fixtures/NewStudentWithCoursesAssertion.xml"),
            $ds
        );
    }

    public function testExistingStudent_RemoveCourse()
    {
        $student = $this->_entityManager->load("ZendEntity_Student", 2);
        unset($student->currentCourses[1]);

        $this->_entityManager->save($student);

        $ds = new Zend_Test_PHPUnit_Database_DataSet_QueryDataSet($this->getConnection());
        $ds->addTable("university_students");
        $ds->addTable("university_students_semester_courses");

        $this->assertDataSetsEqual(
            $this->createFlatXMLDataSet(dirname(__FILE__)."/University/Fixtures/ExistingStudentRemoveCourseAssertion.xml"),
            $ds
        );
    }
}
