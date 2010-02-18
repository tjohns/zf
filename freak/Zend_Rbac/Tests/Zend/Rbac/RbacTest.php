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
    	$rbac->addSubjects(array(new Zend_Rbac_Object_Subject('User2'), 'user3'));

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
    	$rbac->addRoles(array(new Zend_Rbac_Object_Role('role2'), 'role3'));

        $expected = array('President', 'slave', 'citizen', 'role2', 'role3');
        $this->assertEquals($expected, $rbac->getRoles());
            
        $roles = $rbac->getRoles(Zend_Rbac::AS_OBJECT);
        foreach($roles as $role) {
            $this->assertTrue($role instanceof Zend_Rbac_Object_Role);
            $this->assertTrue(in_array((string)$role,$expected));
        }
    }

    public function testAddingOfResources()
    {
        $rbac = new Zend_Rbac(array('resources' => array('Nuclear bombs','landmines')));
        $rbac->addResources('Rockets'); 
        $rbac->addResources(array(new Zend_Rbac_Object_Resource('Rifles'), 'handgun'));
        
        $expected = array('Nuclear bombs','landmines','Rockets','Rifles','handgun');
        $this->assertEquals($expected, $rbac->getResources());
        
        $resources = $rbac->getResources(Zend_Rbac::AS_OBJECT);
        foreach($resources as $resource) {
        	$this->assertTrue($resource instanceof Zend_Rbac_Object_Resource);
        	$this->assertTrue(in_array((string)$resource,$expected));
        }
    }
    
   public function testDuplicateSubjectThrowsException() {
        $rbac = new Zend_Rbac(array('subjects' => 'John'));
        try {
            $rbac->addSubject(new Zend_Rbac_Object_Subject('John'));
            $this->fail('No exception thrown');
        } catch(Zend_Rbac_Exception $e) {
            // foobar
        }
    }

    public function testDuplicateRoleThrowsException() {
        $rbac = new Zend_Rbac(array('roles' => 'President'));
        try {
            $rbac->addRole(new Zend_Rbac_Object_Role('President'));
            $this->fail('Cannot have two presidents (think about that one!) - No exception thrown');
        } catch(Zend_Rbac_Exception $e) {
            // foobar
        }
    }
    
    public function testDuplicateResourceThrowsException() {
        $rbac = new Zend_Rbac(array('resources' => 'Landmines'));
        try {
            $rbac->addResource(new Zend_Rbac_Object_Resource('Landmines'));
            $this->fail('No exception thrown');
        } catch(Zend_Rbac_Exception $e) {
            // foobar
        }
    }

    public function testSimpleWithoutInheritance()
    {
//    	return; //////////////////////////////////////////
        $rbac = new Zend_Rbac(array(
           'roles' => array('President','minister','citizen'),
           'resources' => array('pay_taxes', 'raise_taxes','blow_world_up'),
           'subjects' => array('Obama', 'You')));
        
        $rbac->assignRoles('citizen', 'You');
        $rbac->assignRoles('President', 'Obama'); //@todo Update with next elections(!)

        $rbac->subscribe('pay_taxes', 'citizen');
        $rbac->subscribe('blow_world_up', 'President');
        $rbac->subscribe('raise_taxes', 'minister');

        
//        $this->assertTrue($rbac->isAllowedRole(array('minister','citizen'), array('pay_taxes')));
        $this->assertFalse($rbac->isAllowedRole('minister', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowedRole('minister', array('pay_taxes')));
        $this->assertTrue($rbac->isAllowedRole('minister', array('raise_taxes')));

        $this->assertFalse($rbac->isAllowed('Obama', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertTrue($rbac->isAllowed('Obama', 'blow_world_up'));
        $this->assertFalse($rbac->isAllowed('Obama', array('pay_taxes', 'raise_taxes')));
        $this->assertFalse($rbac->isAllowed('Obama', 'pay_taxes'));
        $this->assertFalse($rbac->isAllowed('Obama', array('raise_taxes')));
        
        $this->assertFalse($rbac->isAllowed('You', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('You', array('raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('You', array('pay_taxes', 'raise_taxes')));
        $this->assertTrue($rbac->isAllowed('You', 'pay_taxes'));
    }

    public function testRoleInheritance() {
    	$resource = new Zend_Rbac_Object_Resource('pay_taxes');
    	$rbac = new Zend_Rbac(array(
    	   'roles' => array('President','minister','citizen'),
    	   'resources' => array($resource, 'raise_taxes','blow_world_up'),
    	   'subjects' => array('Obama', 'You')));
    	
    	
    	$rbac->assignRoles('citizen', 'You');
    	$rbac->assignRoles('President', 'Obama');
    	
    	$rbac->subscribe('pay_taxes', 'citizen');
       	$rbac->subscribe('blow_world_up', 'President');
    	$rbac->subscribe('raise_taxes', 'minister');
    	
    	$rbac->addChild('President', 'minister');
    	$rbac->addChild('minister', 'citizen');
    	
        $this->assertTrue($rbac->isAllowed('Obama', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertTrue($rbac->isAllowed('Obama', 'blow_world_up'));
        $this->assertTrue($rbac->isAllowed('Obama', array('pay_taxes', 'raise_taxes')));
        $this->assertTrue($rbac->isAllowed('Obama', $resource));
        $this->assertTrue($rbac->isAllowed('Obama', array('raise_taxes')));

        $this->assertFalse($rbac->isAllowed('You', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('You', array('raise_taxes', 'blow_world_up')));
        $this->assertFalse($rbac->isAllowed('You', array('pay_taxes', 'raise_taxes')));
        $this->assertTrue($rbac->isAllowed('You', 'pay_taxes'));

        $this->assertFalse($rbac->isAllowedRole('minister', array('pay_taxes', 'raise_taxes', 'blow_world_up')));
        $this->assertTrue($rbac->isAllowedRole('minister', array('pay_taxes')));
        $this->assertTrue($rbac->isAllowedRole('minister', array('raise_taxes')));
    }

/*    public function testDifferentObjectsSameStringAndStrict()
    {
    	$rbac = new Zend_Rbac(array(
    	   'roles' => array('President', 'citizen'),
    	   'resources' => array('drop_bombs', 'pay_taxes'),
    	   'subjects' => array('Obama', 'You'),
    	));
    	
        $rbac->assignRoles(array('President', 'citizen'), array('Obama', 'You'));
        $rbac->subscribe('drop_bombs', 'President');
        $rbac->subscribe('pay_taxes', 'citizen');
        
//        $this->assertFalse($rbac->isAllowed('nonExisiting', 'pay_taxes'));
//        $this->assertFalse($rbac->isAllowed('You', 'nonExisting'));
//        $this->assertTrue($rbac->isAllowed(new Zend_Rbac_Subject('You'), new Zend_Rbac_Resource('pay_taxes')));
//        $this->assertFalse($rbac->isAllowed(new Zend_Rbac_Subject('Obama'), 'pay_taxes'));
//        $this->assertTrue($rbac->isAllowedRole(new Zend_Rbac_Role('citizen'), 'pay_taxes'));
//        $this->assertFalse($rbac->isAllowedRole(new Zend_Rbac_Subject('president'), 'pay_taxes'));  
//
//        $rbac->setStrictMode(true);

        try {
            $rbac->isAllowed('nonExisiting', 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
        
        try {
            $rbac->isAllowed('pay_taxes', 'nonExisiting');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
        
        try {
        	$rbac->isAllowed(new Zend_Rbac_Object_Subject('You'), 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }        	
        	
        try {
        	$rbac->isAllowed(new Zend_Rbac_Object_Subject('Obama'), 'pay_taxes');
            $this->fail('Exception expected');
        } catch(Zend_Rbac_Exception $e) { }
        
    }*/
    /*

    public function testAssertionsResource()
    {
        $resAllowed = new Zend_Rbac_Object_Resource('Allowed');
        $resForbidden = new Zend_Rbac_Object_Resource('Forbidden');
        
        $rbac = new Zend_Rbac(array(
           'roles' => array('Access','NoAccess'),
           'resources' => array($resAllowed, $resForbidden),
           'subjects' => array('WithAccess', 'WithoutAccess'))
        );
        
            $rbac->assignRoles('Access', 'WithAccess');
        $rbac->assignRoles('NoAccess', 'WithoutAccess');
        $rbac->subscribe($resAllowed, 'Access');
        $rbac->subscribe($resForbidden, 'NoAccess');

        // basic stuff
        $this->assertTrue($rbac->isAllowed('WithAccess', $resAllowed));
        $this->assertFalse($rbac->isAllowed('WithAccess', $resForbidden));
        $this->assertTrue($rbac->isAllowed('WithoutAccess', $resForbidden));
        $this->assertFalse($rbac->isAllowed('WithoutAccess', $resAllowed));

        $resForbidden->addAssertion('Zend_Rbac_Assert_BlockAllTest');
        $resAllowed->addAssertion('Zend_Rbac_Assert_AllowOnceTest');
        $this->assertFalse($rbac->isAllowed('WithoutAccess', 'Forbidden'));
        $this->assertTrue($rbac->isAllowed('WithAccess', $resAllowed));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', $resAllowed));        
        
        $assertions = $rbac->getResource('Forbidden')->getAssertions();
        $this->assertEquals(array('0' => 'assertResource'), $assertions['Zend_Rbac_Assert_BlockAllTest']->testMethodsCalled);

        $assertions = $rbac->getResource('Allowed')->getAssertions();
        $this->assertEquals(
            array_fill(0, 3, 'assertResource'), 
            $assertions['Zend_Rbac_Assert_AllowOnceTest']->testMethodsCalled
        );
        
    }
    
    public function testAssertionsRole()
    {
        $roleAccess = new Zend_Rbac_Object_Role('Access');
        $roleNoAccess = new Zend_Rbac_Object_Role('NoAccess');
        
        $rbac = new Zend_Rbac(array(
           'roles' => array($roleAccess, $roleNoAccess),
           'resources' => array('Allowed', 'Forbidden'),
           'subjects' => array('WithAccess', 'WithoutAccess'))
        );
        
        $rbac->assignRoles('Access', 'WithAccess');
        $rbac->assignRoles('NoAccess', 'WithoutAccess');
        $rbac->subscribe('Allowed', 'Access');
        $rbac->subscribe('Forbidden', 'NoAccess');

        // basic stuff
        $this->assertTrue($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Forbidden'));
        $this->assertTrue($rbac->isAllowed('WithoutAccess', 'Forbidden'));
        $this->assertFalse($rbac->isAllowed('WithoutAccess', 'Allowed'));

        $roleNoAccess->addAssertion('Zend_Rbac_Assert_BlockAllTest');
        $roleAccess->addAssertion('Zend_Rbac_Assert_AllowOnceTest');
        $this->assertFalse($rbac->isAllowed('WithoutAccess', 'Forbidden' ));
        $this->assertTrue($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Allowed'));        
        
        $assertions = $roleNoAccess->getAssertions();
        $this->assertEquals(array('0' => 'assertRole'), $assertions['Zend_Rbac_Assert_BlockAllTest']->testMethodsCalled);

        $assertions = $rbac->getRole('Access')->getAssertions();
        $this->assertEquals(
            array_fill(0, 3, 'assertRole'), 
            $assertions['Zend_Rbac_Assert_AllowOnceTest']->testMethodsCalled
        );
    }
    
    public function testAssertionsSubject()
    {
        $subjWithAccess = new Zend_Rbac_Object_Subject('WithAccess');
        $subjWithoutAccess = new Zend_Rbac_Object_Subject('WithoutAccess');
        
        $rbac = new Zend_Rbac(array(
           'roles' => array('Access', 'NoAccess'),
           'resources' => array('Allowed', 'Forbidden'),
           'subjects' => array($subjWithAccess, $subjWithoutAccess))
        );
        
        $rbac->assignRoles('Access', 'WithAccess');
        $rbac->assignRoles('NoAccess', 'WithoutAccess');
        $rbac->subscribe('Allowed', 'Access');
        $rbac->subscribe('Forbidden', 'NoAccess');

        // basic stuff
        $this->assertTrue($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Forbidden'));
        $this->assertTrue($rbac->isAllowed('WithoutAccess', 'Forbidden'));
        $this->assertFalse($rbac->isAllowed('WithoutAccess', 'Allowed'));

        $subjWithoutAccess->addAssertion('Zend_Rbac_Assert_BlockAllTest');
        $subjWithAccess->addAssertion('Zend_Rbac_Assert_AllowOnceTest');
        $this->assertFalse($rbac->isAllowed($subjWithoutAccess, 'Forbidden' ));
        $this->assertTrue($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Allowed'));
        $this->assertFalse($rbac->isAllowed('WithAccess', 'Allowed'));        
        
        $assertions = $subjWithoutAccess->getAssertions();
        $this->assertEquals(array('0' => 'assertSubject'), $assertions['Zend_Rbac_Assert_BlockAllTest']->testMethodsCalled);

        $assertions = $rbac->getSubject('WithAccess')->getAssertions();
        $this->assertEquals(
            array_fill(0, 3, 'assertSubject'), 
            $assertions['Zend_Rbac_Assert_AllowOnceTest']->testMethodsCalled
        );
    }*/
    
}

class Zend_Rbac_Assert_MockTest implements Zend_Rbac_Assert_Interface {
	public $testMethodsCalled = array();
	public $testArgumentsCalled = array();

    public function assertResource(Zend_Rbac_Resource $resource, $role, $subject = null) {
        $this->testMethodsCalled[] = 'assertResource';
        $this->testArgumentsCalled[] = array('resource' => $resource, $role, $subject);
    }
    
    public function assertRole(Zend_Rbac_Role $role, $resource, $subject = null) {
        $this->testMethodsCalled[] = 'assertRole';
        $this->testArgumentsCalled[] = array('role' => $role, $resource, $subject);
    }
    
    public function assertSubject(Zend_Rbac_Subject $subject, $resource, $role) {
        $this->testMethodsCalled[] = 'assertSubject';
        $this->testArgumentsCalled[] = array('subject' => $subject, $resource, $role);
    }
	
}

class Zend_Rbac_Assert_BlockAllTest extends zend_Rbac_Assert_MockTest {
    public function assertResource(Zend_Rbac_Resource $resource, $role, $subject = null)
    {
        parent::assertResource($resource, $role, $subject);
        
        return false;
    }
    
    public function assertRole(Zend_Rbac_Role $role, $resource, $subject = null) {
        parent::assertRole($role, $resource, $subject);
        
        return false;
    }
    
    public function assertSubject(Zend_Rbac_Subject $subject, $resource, $role) {
    	parent::assertSubject($subject, $resource, $role);
        
        return false;
    }
}

class Zend_Rbac_Assert_AllowOnceTest extends zend_Rbac_Assert_MockTest {
	public $_allow = true;
	
	private function _assert() {
        if($this->_allow) {
            $this->_allow= false;
            return true;
        }
        
        return false;
	}
	
    public function assertResource(Zend_Rbac_Resource $resource, $role, $subject = null)
    {
        parent::assertResource($resource, $role, $subject);
        return $this->_assert();
    }
    
    public function assertRole(Zend_Rbac_Role $role, $resource, $subject = null) {
        parent::assertRole($role, $resource, $subject);
        return $this->_assert();
    }
    
    public function assertSubject(Zend_Rbac_Subject $subject, $resource, $role) {
        parent::assertSubject($subject, $resource, $role);
        return $this->_assert();
    }
    
}
