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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
 
/**
* Test helper
*/
require_once dirname(__FILE__) . '/../../../TestHelper.php';
 
/**
 * Zend_Entity_Tool_EntityGenerator
 */
require_once 'Zend/Entity/Tool/EntityGenerator.php';

/**
 * Zend_Entity_Definition_Entity
 */
require_once 'Zend/Entity/Definition/Entity.php';

//tmp includes as dependencies are not set in Zend_Entity_* classes for development
require_once 'Zend/Entity/Definition/Utility.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Entity/Definition/Property.php';
require_once 'Zend/Entity/Definition/RelationAbstract.php';
 
/**
* @category   Zend
* @package    Zend_Entity
* @subpackage UnitTests
* @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
* @license    http://framework.zend.com/license/new-bsd     New BSD License
*/
class Zend_Entity_Tool_EntityGeneratorTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->_generator = new Zend_Entity_Tool_EntityGenerator();
    }
    
    public function testSetAbstractSuffixIsValid()
    {
        $abstractSuffix = 'Base';
        $this->_generator->setAbstractSuffix($abstractSuffix);
        $this->assertEquals($abstractSuffix, $this->_generator->getAbstractSuffix());
    }
    
    public function testGenerateClassWithBasicPropertiesIsValid()
    {
        $bugDef = new Zend_Entity_Definition_Entity('Bug');
        $bugDef->setTable('zfbugs');
        $bugDef->addProperty('description', array('columnName' => 'bug_description'));
        $bugDef->addProperty('created', array(
            'columnName' => 'bug_created',
            'propertyType' => Zend_Entity_Definition_Property::TYPE_DATETIME,
        ));
        $this->_generator->setEntityDefinition($bugDef);
        $class = $this->_generator->generateClass();
        $this->assertType('Zend_CodeGenerator_Php_Class', $class);
        $this->assertTrue($class->hasProperty('description'));
        $this->assertTrue($class->hasProperty('created'));
    }
    
    public function testGenerateClassWithManyToOneRelationPropertiesIsValid()
    {
        $bugDef = new Zend_Entity_Definition_Entity("Bug");
        $bugDef->setTable('zfbugs');
        $bugDef->addManyToOneRelation("reporter", array(
            "columnName" => "reported_by",
            "class" => "User",
            "fetch" => Zend_Entity_Definition_Property::FETCH_SELECT,
        ));
        $this->_generator->setEntityDefinition($bugDef);
        $class = $this->_generator->generateClass();
        $this->assertType('Zend_CodeGenerator_Php_Class', $class);
    }
}