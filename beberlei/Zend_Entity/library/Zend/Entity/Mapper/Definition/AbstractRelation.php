<?php

abstract class Zend_Entity_Mapper_Definition_AbstractRelation extends Zend_Entity_Mapper_Definition_Property_Abstract
{
    /**
     * @var array
     */
    static protected $_allowedFetchValues = array(
        Zend_Entity_Mapper_Definition_Property::FETCH_LAZY,
        Zend_Entity_Mapper_Definition_Property::FETCH_SELECT,
    );

    /**
     * @var array
     */
    static protected $_allowedCascadeValues = array(
        Zend_Entity_Mapper_Definition_Property::CASCADE_ALL,
        Zend_Entity_Mapper_Definition_Property::CASCADE_NONE,
        Zend_Entity_Mapper_Definition_Property::CASCADE_SAVE,
        Zend_Entity_Mapper_Definition_Property::CASCADE_DELETE,
    );

    /**
     * @var array
     */
    static protected $_allowedNotFoundValues = array(
        Zend_Entity_Mapper_Definition_Property::NOTFOUND_EXCEPTION,
        Zend_Entity_Mapper_Definition_Property::NOTFOUND_NULL,
    );

    /**
     * @var string
     */
    protected $_class = null;

    /**
     * @var string
     */
    protected $_fetch = Zend_Entity_Mapper_Definition_Property::FETCH_LAZY;

    /**
     * @var string
     */
    protected $_cascade = Zend_Entity_Mapper_Definition_Property::CASCADE_NONE;

    /**
     * @var string
     */
    protected $_notFound = Zend_Entity_Mapper_Definition_Property::NOTFOUND_EXCEPTION;

    /**
     * @var string
     */
    protected $_columnName = null;

    /**
     * @var boolean
     */
    protected $_inverse = false;

    /**
     * Return class name of the related entity
     *
     * @return string
     */
    public function getClass()
    {
        return $this->_class;
    }

    /**
     * Set class name of the related entity.
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->_class = $class;
    }

    /**
     * @return string
     */
    public function getFetch()
    {
        return $this->_fetch;
    }

    /**
     * @param string $fetch
     */
    public function setFetch($fetch)
    {
        if(in_array($fetch, self::$_allowedFetchValues)) {
            $this->_fetch = $fetch;
        } else {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Cannot set fetching-strategy of collection ".
                "'".$this->getPropertyName()."' to unknown value '".$fetch."'"
            );
        }
    }


    /**
     * Get Cascading type of Collection
     *
     * @return string
     */
    public function getCascade()
    {
        return $this->_cascade;
    }

    /**
     * Set Cascading type of collection
     *
     * @param string $cascade
     * @throws Zend_Entity_Exception
     */
    public function setCascade($cascade)
    {
        if(!in_array($cascade, self::$_allowedCascadeValues)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "An invalid cascade value '".$cascade."' is set in collection ".
                "definition '".$this->getPropertyName()."'."
            );
        }
        $this->_cascade = $cascade;
    }

    /**
     * @param string $notFound
     */
    public function setNotFound($notFound)
    {
        if(!in_array($notFound, self::$_allowedNotFoundValues)) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "An invalid notFound value '".$notFound."' is set in collection ".
                "definition '".$this->getPropertyName()."'."
            );
        }

        $this->_notFound = $notFound;
    }

    /**
     * @return string
     */
    public function getNotFound()
    {
        return $this->_notFound;
    }

    /**
     * @return boolean
     */
    public function isInverse()
    {
        return (!$this->isOwning());
    }

    /**
     * @return boolean
     */
    public function isOwning()
    {
        return ($this->_inverse==false);
    }

    /**
     * @param boolean $inverse
     * @return void
     */
    public function setInverse($inverse)
    {
        $this->_inverse = $inverse;
    }

    /**
     * Compile Abstract Relation Element
     *
     * @param Zend_Entity_Mapper_Definition_Entity $entityDef
     * @param Zend_Entity_MetadataFactory_Interface $map
     */
    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_MetadataFactory_Interface $map)
    {
        if($this->getClass() == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "Cannot compile relation due to missing class reference for property: ".$this->getPropertyName()
            );
        }
    }
}