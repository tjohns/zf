<?php

class Zend_Entity_UnitOfWorkTest extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_UnitOfWork
     */
    protected $uow;

    static public function dataGetState()
    {
        return array(
            array(Zend_Entity_UnitOfWork::STATE_DELETED, 'markDeleted'),
            array(Zend_Entity_UnitOfWork::STATE_DIRTY, 'markDirty'),
            array(Zend_Entity_UnitOfWork::STATE_MANAGED, 'markManaged'),
            array(Zend_Entity_UnitOfWork::STATE_NEW, 'markNew'),
        );
    }

    public function setUp()
    {
        $this->uow = new Zend_Entity_UnitOfWork();
    }

    /**
     * @dataProvider dataGetState
     * @param int $state
     */
    public function testGetState($state, $method)
    {
        $entity = new Zend_TestEntity1();
        $this->assertEquals(Zend_Entity_UnitOfWork::STATE_DETACHED, $this->uow->getState($entity));

        $this->uow->$method($entity);

        $this->assertEquals($state, $this->uow->getState($entity));
    }

    public function testCount_ToFlushItems()
    {
        $entityA = new Zend_TestEntity1();
        $entityB = new Zend_TestEntity1();
        $entityC = new Zend_TestEntity1();

        $this->uow->markDeleted($entityA);
        $this->uow->markDirty($entityB);
        $this->uow->markNew($entityC);

        $this->assertEquals(3, count($this->uow));
    }

    private function _attachLifecycleMapperMock($lifecycleMethod, $entity)
    {
        $tx = $this->getMock('Zend_Entity_Transaction');
        $mapper = $this->getMock('Zend_Db_Mapper_Mapper', array(), array(), '', false);
        $mapper->expects($this->once())
               ->method('getTransaction')
               ->will($this->returnValue($tx));
        $mapper->expects($this->once())
               ->method($lifecycleMethod)
               ->with($this->equalTo($entity));

        $em = $this->createTestingEntityManager();
        $em->setMapper($mapper);
        $this->uow->setEntityManager($em);
    }

    public function testMarkForDeleteEntities_DelegatedToMapper_OnFlush()
    {
        $entityA = new Zend_TestEntity1();
        $this->uow->markDeleted($entityA);

        $this->_attachLifecycleMapperMock('delete', $entityA);

        $this->uow->flush();
        $this->assertEquals(Zend_Entity_UnitOfWork::STATE_DETACHED, $this->uow->getState($entityA));
    }

    public function testMarkNewEntities_DelegatedToMapper_OnFlush()
    {
        $entityA = new Zend_TestEntity1();
        $this->uow->markNew($entityA);

        $this->_attachLifecycleMapperMock('save', $entityA);

        $this->uow->flush();
        $this->assertEquals(Zend_Entity_UnitOfWork::STATE_MANAGED, $this->uow->getState($entityA));
    }

    public function testMarkDirtyEntities_DelegatedToMapper_OnFlush()
    {
        $entityA = new Zend_TestEntity1();
        $this->uow->markDirty($entityA);

        $this->_attachLifecycleMapperMock('save', $entityA);

        $this->uow->flush();
        $this->assertEquals(Zend_Entity_UnitOfWork::STATE_MANAGED, $this->uow->getState($entityA));
    }

    /**
     *
     * @param  Zend_Entity_MetadataFactory_FactoryAbstract $fixture
     * @return Zend_Entity_Manager_Interface
     */
    protected function getEntityManager($fixture)
    {
        $mf = $fixture->getResourceMap();

        $em = new Zend_Entity_TestUtil_Manager();
        $em->setMetadataFactory($mf);
        $em->getMapper()->initializeMappings($mf);
        $this->uow->setEntityManager($em);
        return $em;
    }

    public function testFlush_DetectArrayFromNewEntity_AndPersist()
    {
        $fixture = new Zend_Entity_Fixture_CollectionElementDefs();
        $em = $this->getEntityManager($fixture);

        $array = new Zend_Entity_Collection_Array();

        $entity = new Zend_TestEntity1;
        $entity->elements = $array;

        $this->uow->markNew($entity);
        $this->uow->flush();
    }

    public function testFlush_DetectArrayFromKnownEntity_AndPersist()
    {
        $fixture = new Zend_Entity_Fixture_CollectionElementDefs();
        $em = $this->getEntityManager($fixture);

        $array = new Zend_Entity_Collection_Array();

        $entity = new Zend_TestEntity1;
        $entity->elements = $array;

        $this->uow->markDirty($entity);
        $this->uow->flush();
    }

    public function testFlush_DetectArrayFromRemovedEntity_AndRemove()
    {
        $fixture = new Zend_Entity_Fixture_CollectionElementDefs();
        $em = $this->getEntityManager($fixture);

        $array = new Zend_Entity_Collection_Array();

        $entity = new Zend_TestEntity1;
        $entity->elements = $array;

        $this->uow->markDeleted($entity);
        $this->uow->flush();
    }

    public function testFlush_DetectCollectionFromEntity_AndPersist()
    {
        $this->markTestIncomplete();
    }

    public function testFlush_DetectCascadeRelation_AndPersist()
    {
        $this->markTestIncomplete();
    }

    public function testFlush_DetectCascadeCollection_AndPersist()
    {
        $this->markTestIncomplete();
    }

    public function testBeginCommitTransactionInsideFlush()
    {
        $this->markTestIncomplete();
    }

    public function testMapperThrowsException_IdentityMapIsCleared_TransactionRolledBack_MarkedAsRollbackOnly()
    {
        $this->markTestIncomplete();
    }

    public function testSaveKnownEntity_CollectionNotMatchingIdentityMap_ThrowsException()
    {
        $this->markTestIncomplete();
    }

    public function testSaveNewEntity_UpdateEntityCollection_IdentityMapState()
    {
        $this->markTestIncomplete();
    }

    public function testSaveNewEntity_UpdateRelatedEntity_IdentityMapState()
    {
        $this->markTestIncomplete();
    }

    public function testSaveKnownEntity_RelatedEntityNotMatchingIdentityMap_IsAllowed()
    {
        $this->markTestIncomplete();
    }

    public function testSaveNewEntity_UpdateArray_IdentityMapState()
    {
        $this->markTestIncomplete();
    }

    public function testSaveKnownEntity_RelatedArrayNotMatchingIdentityMap_ThrowsException()
    {
        $this->markTestIncomplete();
    }
}