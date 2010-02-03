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
 * @package    Zend_Rbac
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: RbacTest.php 20096 2010-01-06 02:05:09Z bkarwin $
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Rbac.php';
require_once 'Zend/Rbac/Subject.php';
require_once 'Zend/Rbac/Role.php';
require_once 'Zend/Rbac/Resource.php';
//require_once 'Zend/Rbac/Assertion/Abstract.php';

/**
 * @category   Zend
 * @package    Zend_Rbac
 * @subpackage UnitTests
 * @group      Zend_Rbac
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Rbac_RbacTest extends PHPUnit_Framework_TestCase
{
    public function testAddingOfSubjects() {
    	$rbac = new Zend_Rbac(array('subjects' => array('John', 'User')));
    	$rbac->addSubject('Jane');
    	$rbac->addSubjects(array(new Zend_Rbac_Subject('User2'), 'user3'));
    	
    	$expected = array('John', 'User', 'Jane', 'User2', 'user3');
    	$this->assertEquals($expected, $rbac->getSubjects());
            
        $subjects = $rbac->getSubjects(Zend_Rbac::AS_OBJECT);
        foreach($subjects as $subject) {
            $this->assertTrue($subject instanceof Zend_Rbac_Subject);
            $this->assertTrue(in_array((string)$subject,$expected));
        }
    }
    
    public function testAddingOfRoles() {
    	$rbac = new Zend_Rbac(array('roles' => array('President','slave')));
    	$rbac->addRole('citizen');
    	$rbac->addRoles(array(new Zend_Rbac_Role('role2'), 'role3'));

        $expected = array('President', 'slave', 'citizen', 'role2', 'role3');
        $this->assertEquals($expected, $rbac->getRoles());
            
        $roles = $rbac->getRoles(Zend_Rbac::AS_OBJECT);
        foreach($roles as $role) {
            $this->assertTrue($role instanceof Zend_Rbac_Role);
            $this->assertTrue(in_array((string)$role,$expected));
        }
    }

    public function testAddingOfResources()
    {
        $rbac = new Zend_Rbac(array('resources' => array('Nuclear bombs','landmines')));
        $rbac->addResources('Rockets'); 
        $rbac->addResources(array(new Zend_Rbac_Resource('Rifles'), 'handgun'));
        
        $expected = array('Nuclear bombs','landmines','Rockets','Rifles','handgun');
        $this->assertEquals($expected, $rbac->getResources());
        
        $resources = $rbac->getResources(Zend_Rbac::AS_OBJECT);
        foreach($resources as $resource) {
        	$this->assertTrue($resource instanceof Zend_Rbac_Resource);
        	$this->assertTrue(in_array((string)$resource,$expected));
        }
    }
    
    public function testDuplicateSubjectThrowsException() {
        $rbac = new Zend_Rbac(array('subjects' => 'John'));
        try {
            $rbac->addSubject(new Zend_Rbac_Subject('John'));
            $this->fail('No exception thrown');
        } catch(Zend_Rbac_Exception $e) {
            // foobar
        }
    }

    public function testDuplicateRoleThrowsException() {
        $rbac = new Zend_Rbac(array('roles' => 'President'));
        try {
            $rbac->addRole(new Zend_Rbac_Role('President'));
            $this->fail('Cannot have two presidents (think about that one!) - No exception thrown');
        } catch(Zend_Rbac_Exception $e) {
            // foobar
        }
    }
    
    public function testDuplicateResourceThrowsException() {
        $rbac = new Zend_Rbac(array('resources' => 'Landmines'));
        try {
            $rbac->addResource(new Zend_Rbac_Resource('Landmines'));
            $this->fail('No exception thrown');
        } catch(Zend_Rbac_Exception $e) {
            // foobar
        }
    }
    
    public function testRoleInheritance() {
    	$rbac = new Zend_Rbac(array(
    	   'roles' => array('President','minister','citizen'),
    	   'resources' => array('pay_taxes', 'raise_taxes','blow_world_up'),
    	   'subjects' => array('Obama', 'You')));
    	
    	$rbac->assignRoles('citizen', 'You');
    	$rbac->assignRoles('President', 'Obama');
    	$rbac->subscribe('pay_taxes', 'citizen');
    	$rbac->subscribe('blow_world_up', 'President');
    	$rbac->subscribe('raise_taxes', 'minister');
    	$rbac->setChild('President', 'minister');
    	$rbac->setChild('minister', 'citizen');
    	
        $this->assertFalse($rbac->isAllowed('Obama', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('Obama', 'blow_world_up'));
        $this->assertTrue($rbac->isAllowed('Obama', array('pay_taxes', 'raise_taxes')));
        $this->assertTrue($rbac->isAllowed('Obama', 'pay_taxes'));
        $this->assertTrue($rbac->isAllowed('Obama', array('raise_taxes')));
        
        $this->assertFalse($rbac->isAllowed('You', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('You', array('raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('You', array('pay_taxes', 'raise_taxes')));
        $this->assertTrue($rbac->isAllowed('You', 'pay_taxes'));
        
        $this->assertFalse($rbac->isAllowedRole('minister', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertTrue($rbac->isAllowedRole('minister', array('pay_taxes')));
        $this->assertTrue($rbac->isAllowedRole('minister', array('raise_taxes')));
    }
    
   /*        public function testDifferentObjectsSameStringAndStrict()
    {
    	$rbac = new Zend_Rbac(array(
    	   'roles' => array('President', 'citizen'),
    	   'resources' => array('drop_bombs', 'pay_taxes'),
    	   'subjects' => array('Obama', 'You'),
    	));
    	
        $rbac->assignRoles(array('President', 'citizen'), array('Obama', 'You'), true);
        $rbac->subscribe('drop_bombs', 'President');
        $rbac->subscribe('pay_taxes', 'citizen');
        
    	
        $this->assertFalse($rbac->isAllowed('nonExisiting', 'pay_taxes'));
        $this->assertFalse($rbac->isAllowed('You', 'nonExisting'));
        $this->assertTrue($rbac->isAllowed(new Zend_Rbac_Subject('You'), new Zend_Rbac_Resource('pay_taxes')));
        $this->assertFalse($rbac->isAllowed(new Zend_Rbac_Subject('Obama'), 'pay_taxes'));
        $this->assertTrue($rbac->isAllowedRole(new Zend_Rbac_Role('citizen'), 'pay_taxes'));
        $this->assertFalse($rbac->isAllowedRole(new Zend_Rbac_Subject('president'), 'pay_taxes'));  

        $rbac->setStrictMode(true);
        
        try {
            $rbac->isAllowed('nonExisiting', 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
        
        try {
            $rbac->isAllowed('pay_taxes', 'nonExisiting');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
        
        try {
        	$rbac->isAllowed(new Zend_Rbac_Subject('You'), 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }        	
        	
        try {
        	$rbac->isAllowed('You', new Zend_Rbac_Resource('pay_taxes'));
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }

        try {
        	$rbac->isAllowed(new Zend_Rbac_Subject('Obama'), 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
                	
        try {
            $rbac->isAllowedRole(new Zend_Rbac_Role('citizen'), 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
            
        try {
            $rbac->isAllowedRole(new Zend_Rbac_Subject('citizen'), 'pay_taxes');  
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }

    }*/
    
    //@TODO Assertions
}
