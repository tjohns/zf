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

class Zend_Entity_Definition_OneToOneRelation extends Zend_Entity_Definition_AbstractRelation
{
    /**
     * Compile OneToOne Relation Element
     *
     * @param Zend_Entity_Definition_Entity $entityDef
     * @param Zend_Entity_MetadataFactory_Interface $map
     */
    public function compile(Zend_Entity_Definition_Entity $entityDef, Zend_Entity_MetadataFactory_Interface $map)
    {
        parent::compile($entityDef, $map);
    }
}