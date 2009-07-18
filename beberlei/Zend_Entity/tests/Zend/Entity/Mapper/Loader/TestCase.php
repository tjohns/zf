<?php

abstract class Zend_Entity_Mapper_Loader_TestCase extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Manager
     */
    protected $entityManager = null;

    /**
     * @var Zend_Entity_MetadataFactory_Testing
     */
    protected $resourceMap = null;

    /**
     * @var Zend_Entity_IdentityMap
     */
    protected $identityMap = null;

    /**
     * @var Zend_Entity_Mapper_UnitOfWork
     */
    protected $unitOfWork = null;


    public function createEntityManager()
    {
        $this->entityManager = parent::createEntityManager($this->unitOfWork, $this->resourceMap, $this->identityMap);
        return $this->entityManager;
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function createLoaderWithIdAndManyToOneProperty()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_CLASS_B);
        $def->setTable(self::TEST_TABLE_B);

        $def->addPrimaryKey(self::TEST_IDPROPERTY);
        $def->addManyToOneRelation(self::TEST_PROPERTY_B, array('class' => self::TEST_CLASS, 'foreignKey' => self::TEST_PROPERTY_A));

        $def->compile($this->createResourceMapMock());

        return $this->createLoader($def);
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function createLoaderWithIdAndPropertyWithDifferentColumnNames()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_CLASS_B);
        $def->setTable(self::TEST_TABLE_B);

        $def->addPrimaryKey(self::TEST_IDPROPERTY, array('columnName' => self::TEST_IDCOLUMN));
        $def->addProperty(self::TEST_PROPERTY_A, array('columnName' => self::TEST_COLUMN_A));

        $def->compile($this->createResourceMapMock());

        return $this->createLoader($def);
    }

    /**
     * @return Zend_Entity_Mapper_Loader_Interface
     */
    public function createLoaderWithIdAndManyToManyProperty()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_CLASS_B);
        $def->setTable(self::TEST_TABLE_B);

        $def->addPrimaryKey(self::TEST_IDPROPERTY);
        $def->addCollection(self::TEST_PROPERTY_B, array(
            'table' => self::TEST_TABLE_A,
            'key' => self::TEST_PROPERTY_A,
            'relation' => new Zend_Entity_Definition_ManyToManyRelation(self::TEST_PROPERTY_B, array(
                'class' => self::TEST_CLASS,
                'foreignKey' => self::TEST_PROPERTY_A
            ))
        ));

        $def->compile($this->createResourceMapMock());

        return $this->createLoader($def);
    }
}