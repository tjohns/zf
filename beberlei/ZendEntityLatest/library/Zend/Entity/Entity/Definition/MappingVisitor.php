<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * A Definition Visitor is an interface that transforms the general metadata into storage specifc data.
 *
 * The purpose of the definition visitor is the decoupling of the metadata package from the specific
 * access layer that is required for a storage/mapper implementation. Also for performance reasons
 * it is necessary to restructure the metadata object graph into something quite different at runtime.
 *
 * @category   Zend
 * @package    Zend_Entity
 * @subpackage Definition
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Entity_Definition_MappingVisitor
{
    /**
     * Accept an entity definition
     *
     * @param Zend_Entity_Definition_Entity $entity
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptEntity(Zend_Entity_Definition_Entity $entity, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory);

    /**
     * Accept a property definition
     *
     * @param Zend_Entity_Definition_Property $property
     * @param Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory
     */
    public function acceptProperty(Zend_Entity_Definition_Property $property, Zend_Entity_MetadataFactory_FactoryAbstract $metadataFactory);

    /**
     * Helper function that finalizes the visitor process.
     *
     * @return void
     */
    public function finalize();
}