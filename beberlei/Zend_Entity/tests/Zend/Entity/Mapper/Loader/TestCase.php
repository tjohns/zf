<?php

abstract class Zend_Entity_Mapper_Loader_TestCase extends Zend_Entity_TestCase
{
    /**
     * @var Zend_Entity_Manager
     */
    protected $entityManager = null;

    /**
     * @var Zend_Entity_Fixture_Abstract
     */
    protected $fixture = null;

    /**
     * @var Zend_Entity_IdentityMap
     */
    protected $identityMap = null;

    /**
     * @var Zend_Entity_Mapper_UnitOfWork
     */
    protected $unitOfWork = null;

    abstract public function getLoaderClassName();

    public function createLoader(Zend_Entity_Definition_Entity $def)
    {
        $loaderClassName = $this->getLoaderClassName();
        $mi = $this->fixture->getResourceMap()->transform('Zend_Entity_Mapper_MappingInstruction');
        
        return new $loaderClassName($def, $mi[$def->getClass()]);
    }

    public function createEntityManager()
    {
        $this->entityManager = parent::createEntityManager($this->unitOfWork, $this->fixture->getResourceMap(), $this->identityMap);
        return $this->entityManager;
    }

    /**
     * @return Zend_Entity_Mapper_Loader_LoaderAbstract
     */
    public function createLoaderWithIdAndManyToOneProperty()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_CLASS_B);
        $def->setTable(self::TEST_TABLE_B);

        $def->addPrimaryKey(self::TEST_IDPROPERTY);
        $def->addManyToOneRelation(self::TEST_PROPERTY_B, array('class' => self::TEST_CLASS, 'foreignKey' => self::TEST_PROPERTY_A));

        return $this->createLoader($def);
    }

    /**
     * @return Zend_Entity_Mapper_Loader_LoaderAbstract
     */
    public function createLoaderWithIdAndPropertyWithDifferentColumnNames()
    {
        $def = new Zend_Entity_Definition_Entity(self::TEST_CLASS_B);
        $def->setTable(self::TEST_TABLE_B);

        $def->addPrimaryKey(self::TEST_IDPROPERTY, array('columnName' => self::TEST_IDCOLUMN));
        $def->addProperty(self::TEST_PROPERTY_A, array('columnName' => self::TEST_COLUMN_A));

        return $this->createLoader($def);
    }

    /**
     * @return Zend_Entity_Mapper_Loader_LoaderAbstract
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

        return $this->createLoader($def);
    }
}