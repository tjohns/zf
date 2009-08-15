<?php

abstract class Zend_Entity_Definition_AbstractRelation extends Zend_Entity_Definition_Property
{
    /**
     * @var array
     */
    static protected $_allowedFetchValues = array(
        Zend_Entity_Definition_Property::FETCH_LAZY,
        Zend_Entity_Definition_Property::FETCH_SELECT,
    );

    /**
     * @var array
     */
    static protected $_allowedCascadeValues = array(
        Zend_Entity_Definition_Property::CASCADE_ALL,
        Zend_Entity_Definition_Property::CASCADE_NONE,
        Zend_Entity_Definition_Property::CASCADE_SAVE,
        Zend_Entity_Definition_Property::CASCADE_DELETE,
    );

    /**
     * @var array
     */
    static protected $_allowedNotFoundValues = array(
        Zend_Entity_Definition_Property::NOTFOUND_EXCEPTION,
        Zend_Entity_Definition_Property::NOTFOUND_NULL,
    );

    /**
     * @var string
     */
    public $class = null;

    /**
     * @var string
     */
    public $fetch = Zend_Entity_Definition_Property::FETCH_LAZY;

    /**
     * @var string
     */
    public $cascade = Zend_Entity_Definition_Property::CASCADE_NONE;

    /**
     * @var string
     */
    public $notFound = Zend_Entity_Definition_Property::NOTFOUND_EXCEPTION;

    /**
     * @var boolean
     */
    public $inverse = false;

    /**
     * @var string
     */
    public $mappedBy = null;

    /**
     * Return class name of the related entity
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set class name of the related entity.
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return string
     */
    public function getFetch()
    {
        return $this->fetch;
    }

    /**
     * @param string $fetch
     */
    public function setFetch($fetch)
    {
        if(in_array($fetch, self::$_allowedFetchValues)) {
            $this->fetch = $fetch;
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
        return $this->cascade;
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
        $this->cascade = $cascade;
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

        $this->notFound = $notFound;
    }

    /**
     * @return string
     */
    public function getNotFound()
    {
        return $this->notFound;
    }

    /**
     * @return boolean
     */
    public function isInverse()
    {
        return ($this->inverse==true);
    }

    /**
     * @return boolean
     */
    public function isOwning()
    {
        return ($this->inverse==false);
    }

    /**
     * @param boolean $inverse
     * @return void
     */
    public function setInverse($inverse)
    {
        $this->inverse = $inverse;
    }

    /**
     * @param string $mapByPropertyName
     */
    public function setMappedBy($mapByPropertyName)
    {
        $this->mappedBy = $mapByPropertyName;
    }

    public function getMappedBy()
    {
        return $this->mappedBy;
    }
}