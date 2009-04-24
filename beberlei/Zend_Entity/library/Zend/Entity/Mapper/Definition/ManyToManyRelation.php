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

class Zend_Entity_Mapper_Definition_ManyToManyRelation extends Zend_Entity_Mapper_Definition_AbstractRelation
{
    public function compile(Zend_Entity_Mapper_Definition_Entity $entityDef, Zend_Entity_Resource_Interface $map)
    {
        parent::compile($entityDef, $map);

        if($this->getForeignKey() == null) {
            require_once "Zend/Entity/Exception.php";
            throw new Zend_Entity_Exception(
                "OneToMany Relation '".$this->getPropertyName()."' requires name of foreign key field."
            );
        }
    }
}