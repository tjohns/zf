<?php
/**
 * Mapper
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.
 * 
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to kontakt@beberlei.de so we can send you a copy immediately.
 *
 * @category   Zend
 * @category   Zend_Entity
 * @copyright  Copyright (c) 2009 Benjamin Eberlei
 * @license    New BSD License
 */

class Zend_Entity_Mapper_Definition_Relation_ManyToMany extends Zend_Entity_Mapper_Definition_Property
    implements Zend_Entity_Mapper_Definition_Relation_Interface
{
    protected $class;

    protected $notFound = self::NOTFOUND_NULL;

    protected $fetch    = self::FETCH_SELECT;

    public function getClass()
    {
        return $this->class;
    }

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function setFetch($fetch)
    {
        $this->fetch = $fetch;
    }

    public function getFetch()
    {
        return $this->fetch;
    }

    public function setNotFound($notFound)
    {
        $this->notFound = $notFound;
    }

    public function getNotFound()
    {
        return $this->notFound;
    }

    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        if($this->getClass() == null) {
            throw new Exception("Class is a requried field in Many-To-Many Collection Description of entity '".$entityDef->getClass()."'");
        }

        if($this->getColumnName() === null) {
            $this->setColumnName($this->getPropertyName());
        }
    }
}