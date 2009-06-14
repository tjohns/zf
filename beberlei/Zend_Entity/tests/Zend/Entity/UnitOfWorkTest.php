<?php

require_once dirname(__FILE__)."/../TestHelper.php";

class Zend_Entity_UnitOfWorkTest extends PHPUnit_Framework_TestCase
{
    protected $_db;

    protected $_entityManager;

    public function setUp()
    {
        $args = array(array("dbname" => "dbname", "password" => "password", "username" => "user"));
        $this->_db = $this->getMock("Zend_Db_Adapter_Pdo_Mysql", array("beginTransaction", "commit"), $args);
        $this->_entityManager = $this->getMock("Zend_Entity_Manager", array(), array($this->_db, array('mappingsPath' => '')));
    }

    /**
     * @return Zend_Entity_Mapper_UnitOfWork
     */
    protected function getNewUnitOfWork()
    {
        return new Zend_Entity_Mapper_UnitOfWork($this->_db, $this->_entityManager);
    }

    public function testIfEnabledDefaultStateOUnknownEntityIsDirty()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $patient = new Zend_TestEntity1();
        $this->assertEquals(Zend_Entity_Mapper_UnitOfWork::STATE_DIRTY, $uow->getState($patient), "Entity is not marked dirty.");
    }

    public function testIfEnabledSetRegisterNewIsRecognized()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $patient = new Zend_TestEntity1();
        $uow->registerNew($patient);
        $this->assertEquals(Zend_Entity_Mapper_UnitOfWork::STATE_NEW, $uow->getState($patient), "Entity is not marked new.");
    }

    public function testIfEnabledSetRegisterCleanIsRecognized()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $patient = new Zend_TestEntity1();
        $uow->registerClean($patient);
        $this->assertEquals(Zend_Entity_Mapper_UnitOfWork::STATE_CLEAN, $uow->getState($patient), "Entity is not marked clean.");
    }

    public function testIfEnabledSetRegisterDirtyIsRecognized()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $patient = new Zend_TestEntity1();
        $uow->registerClean($patient);
        $uow->registerDirty($patient);
        $this->assertEquals(Zend_Entity_Mapper_UnitOfWork::STATE_DIRTY, $uow->getState($patient), "Entity is not marked dirty.");
    }

    public function testIfEnabledSetRegisterDeletedIsRecognized()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $patient = new Zend_TestEntity1();
        $uow->registerDeleted($patient);
        $this->assertEquals(Zend_Entity_Mapper_UnitOfWork::STATE_DELETED, $uow->getState($patient), "Entity is not marked deleted.");
    }

    public function testDefaultManagingTransactionBooleanFunctionFalse()
    {
        $uow = $this->getNewUnitOfWork();
        $this->assertFalse($uow->isManagingCurrentTransaction());
    }

    public function testBeginTransactionEvaluatesManagingTransactionBooleanFunctionTrue()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $this->assertTrue($uow->isManagingCurrentTransaction());
    }

    public function testNotStartedTransactionThrowsException()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $uow = $this->getNewUnitOfWork();
        $uow->commit();
    }

    public function testDefaultUnitOfWorkIsNotReadOnly()
    {
        $uow = $this->getNewUnitOfWork();
        $this->assertFalse($uow->isReadOnly());
    }

    public function testSetReadOnlyIsRecognizedByBooleanFunction()
    {
        $uow = $this->getNewUnitOfWork();
        $uow->setReadOnly();
        $this->assertTrue($uow->isReadOnly());
    }

    public function testSetReadOnlyThrowsExceptionOnBeginTransaction()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $uow = $this->getNewUnitOfWork();
        $uow->setReadOnly();

        $uow->beginTransaction();
    }

    public function testSetReadOnlyThrowsExceptionOnCommit()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $uow = $this->getNewUnitOfWork();
        $uow->setReadOnly();

        $uow->commit();
    }

    public function testSetReadOnlyThrowsExceptionOnRollback()
    {
        $this->setExpectedException("Zend_Entity_Exception");

        $uow = $this->getNewUnitOfWork();
        $uow->setReadOnly();

        $uow->rollBack();
    }

    public function testCommitWithManagedTransactionCallsMappersODeleteEntities()
    {
        $className = 'Zend_Entity_MapperMock'.md5(microtime(true));
        $mapperMock = $this->getMock('Zend_Entity_Mapper', array(), array(), $className, false);
        $mapperMock->expects($this->once())
                   ->method('delete')
                   ->will($this->returnValue(true));

        $this->_entityManager->expects($this->once())
                             ->method('getMapperByEntity')
                             ->will($this->returnValue($mapperMock));

        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $entity = new Zend_TestEntity1();
        $uow->registerDeleted($entity);
        $uow->commit();
    }

    public function testCommitWithManagedTransactionCallsMappersOfDirtyEntities()
    {
        $className = 'Zend_Entity_MapperMock'.md5(microtime(true));
        $mapperMock = $this->getMock('Zend_Entity_Mapper', array(), array(), $className, false);
        $mapperMock->expects($this->once())
                   ->method('save')
                   ->will($this->returnValue(true));

        $this->_entityManager->expects($this->once())
                             ->method('getMapperByEntity')
                             ->will($this->returnValue($mapperMock));

        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $entity = new Zend_TestEntity1();
        $uow->registerDirty($entity);
        $uow->commit();
    }

    public function testCommitWithManagedTransactionCallsMappersOfNewEntities()
    {
        $className = 'Zend_Entity_MapperMock'.md5(microtime(true));
        $mapperMock = $this->getMock('Zend_Entity_Mapper', array(), array(), $className, false);
        $mapperMock->expects($this->exactly(2))
                   ->method('save')
                   ->will($this->returnValue(true));

        $this->_entityManager->expects($this->exactly(2))
                             ->method('getMapperByEntity')
                             ->will($this->returnValue($mapperMock));

        $uow = $this->getNewUnitOfWork();
        $uow->beginTransaction();

        $entity = new Zend_TestEntity1();
        $entity2 = new Zend_TestEntity1();
        $uow->registerNew($entity);
        $uow->registerNew($entity2);
        $uow->commit();
    }
}