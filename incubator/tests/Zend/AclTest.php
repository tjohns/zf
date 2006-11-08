<?php

/**
 * @category   Zend
 * @package    Zend_Acl
 * @subpackage UnitTests
 */


/**
 * Zend_Acl
 */
require_once 'Zend/Acl.php';
require_once 'Zend/Acl/Exception.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Acl
 * @subpackage UnitTests
 */
class Zend_AclTest extends PHPUnit_Framework_TestCase
{
    protected $_acl;
    protected $_acl2;

    public function testCMSExample()
    {
        // Create new Zend_Acl instance
        $this->_acl = new Zend_Acl();

        // Fetch the ARO registry
        $aro = $this->_acl->aroRegistry();

        // Add some roles to the ARO registry
        $aro->add('guest');
        $aro->add('staff', $aro->guest);  // staff inherits permissions from guest
        $aro->add('editor', $aro->staff); // editor inherits permissions from staff
        $aro->add('administrator');

        // Whitelist implementation; ACL denies access by default
        $this->_acl->deny();

        // Guest may only view content
        $this->_acl->allow($aro->guest, 'view');

        // Staff inherits view privilege from guest, but also needs additional privileges
        $this->_acl->allow($aro->staff, array('edit', 'submit', 'revise'));

        // Editor inherits view, edit, submit, and revise privileges, but also needs additional privileges
        $this->_acl->allow($aro->editor, array('publish', 'archive', 'delete'));

        // Administrator inherits nothing but is allowed all privileges
        $this->_acl->allow($aro->administrator);

        // Access control checks based on above permission sets

        self::assertTrue($this->_acl->valid('guest', 'view'));
        self::assertFalse($this->_acl->valid('guest', 'edit'));
        self::assertFalse($this->_acl->valid('guest', 'submit'));
        self::assertFalse($this->_acl->valid($aro->guest, 'revise'));
        self::assertFalse($this->_acl->valid('guest', 'publish'));
        self::assertFalse($this->_acl->valid('guest', 'archive'));
        self::assertFalse($this->_acl->valid($aro->guest, 'delete'));
        self::assertFalse($this->_acl->valid('guest', 'unknown'));
        self::assertFalse($this->_acl->valid('guest'));

        self::assertTrue($this->_acl->valid('staff', 'view'));
        self::assertTrue($this->_acl->valid('staff', 'edit'));
        self::assertTrue($this->_acl->valid('staff', 'submit'));
        self::assertTrue($this->_acl->valid($aro->staff, 'revise'));
        self::assertFalse($this->_acl->valid('staff', 'publish'));
        self::assertFalse($this->_acl->valid('staff', 'archive'));
        self::assertFalse($this->_acl->valid($aro->staff, 'delete'));
        self::assertFalse($this->_acl->valid('staff', 'unknown'));
        self::assertFalse($this->_acl->valid('staff'));

        self::assertTrue($this->_acl->valid('editor', 'view'));
        self::assertTrue($this->_acl->valid('editor', 'edit'));
        self::assertTrue($this->_acl->valid('editor', 'submit'));
        self::assertTrue($this->_acl->valid($aro->editor, 'revise'));
        self::assertTrue($this->_acl->valid('editor', 'publish'));
        self::assertTrue($this->_acl->valid('editor', 'archive'));
        self::assertTrue($this->_acl->valid($aro->editor, 'delete'));
        self::assertFalse($this->_acl->valid('editor', 'unknown'));
        self::assertFalse($this->_acl->valid('editor'));

        self::assertTrue($this->_acl->valid('administrator', 'view'));
        self::assertTrue($this->_acl->valid('administrator', 'edit'));
        self::assertTrue($this->_acl->valid('administrator', 'submit'));
        self::assertTrue($this->_acl->valid($aro->administrator, 'revise'));
        self::assertTrue($this->_acl->valid('administrator', 'publish'));
        self::assertTrue($this->_acl->valid('administrator', 'archive'));
        self::assertTrue($this->_acl->valid($aro->administrator, 'delete'));
        self::assertTrue($this->_acl->valid('administrator', 'unknown'));
        self::assertTrue($this->_acl->valid('administrator'));

        // Some checks on specific areas, which inherit access controls from the root ACL node
        self::assertTrue($this->_acl->newsletter->pending->valid('guest', 'view'));
        self::assertTrue($this->_acl->gallery->profiles->valid($aro->staff, 'revise'));
        self::assertFalse($this->_acl->config->hosts->valid($aro->editor, 'unknown'));

        // Checking permissions from the perspective of an ARO
        self::assertTrue($aro->staff->canAccess($this->_acl->newsletter->pending, 'view'));
        self::assertTrue($aro->staff->canAccess($this->_acl->newsletter->pending, 'edit'));
        self::assertFalse($aro->staff->canAccess($this->_acl->newsletter->pending, 'publish'));
        self::assertFalse($aro->staff->canAccess($this->_acl->newsletter->pending));
        self::assertTrue($aro->administrator->canAccess($this->_acl->newsletter->pending));

        // Unknown ARO
        self::assertFalse($aro->unknown->canAccess($this->_acl->newsletter->pending));

        // Add a new group, marketing, which bases its permissions on staff
        $aro->add('marketing', 'staff');

        // Refine the privilege sets for more specific needs

        // Allow marketing to publish and archive newsletters
        $this->_acl->newsletter->allow($aro->marketing, array('publish', 'archive'));

        // Allow marketing to publish and archive latest news
        $this->_acl->news->latest->allow($aro->marketing, array('publish', 'archive'));

        // Deny staff (and marketing, by inheritance) rights to revise latest news
        $this->_acl->news->latest->deny($aro->staff, 'revise');

        // Deny everyone access to archive news announcements
        $this->_acl->news->announcement->deny(null, 'archive');

        // Access control checks for the above refined permission sets

        self::assertTrue($this->_acl->valid('marketing', 'view'));
        self::assertTrue($this->_acl->valid('marketing', 'edit'));
        self::assertTrue($this->_acl->valid('marketing', 'submit'));
        self::assertTrue($this->_acl->valid($aro->marketing, 'revise'));
        self::assertFalse($this->_acl->valid('marketing', 'publish'));
        self::assertFalse($this->_acl->valid('marketing', 'archive'));
        self::assertFalse($this->_acl->valid($aro->marketing, 'delete'));
        self::assertFalse($this->_acl->valid('marketing', 'unknown'));
        self::assertFalse($this->_acl->valid('marketing'));

        self::assertTrue($this->_acl->newsletter->valid('marketing', 'publish'));
        self::assertFalse($this->_acl->newsletter->pending->valid('staff', 'publish'));
        self::assertTrue($this->_acl->newsletter->pending->valid('marketing', 'publish'));
        self::assertTrue($this->_acl->newsletter->valid('marketing', 'archive'));
        self::assertFalse($this->_acl->newsletter->valid('marketing', 'delete'));
        self::assertFalse($this->_acl->newsletter->valid('marketing'));

        self::assertTrue($this->_acl->news->latest->valid('marketing', 'publish'));
        self::assertTrue($this->_acl->news->latest->valid('marketing', 'archive'));
        self::assertFalse($this->_acl->news->latest->valid('marketing', 'delete'));
        self::assertFalse($this->_acl->news->latest->valid('marketing', 'revise'));
        self::assertFalse($this->_acl->news->latest->valid('staging', 'revise'));
        self::assertFalse($this->_acl->news->latest->valid('marketing'));

        self::assertFalse($this->_acl->news->announcement->valid('marketing', 'archive'));
        self::assertFalse($this->_acl->news->announcement->valid('staff', 'archive'));
        self::assertFalse($this->_acl->news->announcement->valid('administrator', 'archive'));

        self::assertFalse($aro->staff->canAccess($this->_acl->news->latest, 'publish'));
        self::assertTrue($aro->marketing->canAccess($this->_acl->news->latest, 'publish'));
        self::assertFalse($aro->editor->canAccess($this->_acl->news->announcement, 'archive'));

        // Remove some previous permission specifications

        // Marketing can no longer publish and archive newsletters
        $this->_acl->newsletter->removeAllow('marketing', array('publish', 'archive'));

        // Marketing can no longer archive the latest news
        $this->_acl->news->latest->removeAllow($aro->marketing, 'archive');

        // Now staff (and marketing, by inheritance) may revise latest news
        $this->_acl->news->latest->removeDeny($aro->staff, 'revise');

        // Access control checks for the above refinements

        self::assertFalse($this->_acl->newsletter->valid('marketing', 'publish'));
        self::assertFalse($this->_acl->newsletter->valid('marketing', 'archive'));

        self::assertFalse($this->_acl->news->latest->valid('marketing', 'archive'));

        self::assertTrue($this->_acl->news->latest->valid('staff', 'revise'));
        self::assertTrue($this->_acl->news->latest->valid($aro->marketing, 'revise'));

        // Grant marketing all permissions on the latest news
        $this->_acl->news->latest->allow('marketing');

        // Access control checks for the above refinement
        self::assertTrue($this->_acl->news->latest->valid('marketing', 'archive'));
        self::assertTrue($this->_acl->news->latest->valid('marketing', 'publish'));
        self::assertTrue($this->_acl->news->latest->valid('marketing', 'edit'));
        self::assertTrue($this->_acl->news->latest->valid('marketing'));

        // Create second Zend_Acl instance
        $this->_acl2 = new Zend_Acl();

        // Fetch a new ARO registry
        $aro2 = $this->_acl2->aroRegistry();

        // Ensure registries are unique instances
        self::assertTrue($aro !== $aro2);

    }

    public function testRegression()
    {
        $acl = new Zend_Acl();
        
        // retrieve an instance of the ARO registry
        $aro = $acl->aroRegistry();
        $aro->add('guest');
        $aro->add('staff', $aro->guest);
        
        // deny access to all unknown AROs
        $acl->deny();
        $acl->allow('staff');
        $acl->deny('staff', array('task1', 'task2'));

        // Access control checks for the above refinement
        self::assertFalse($acl->valid('staff', 'task1'));
    }

    public function testAclAroManagement()
    {
        $acl = new Zend_Acl();
        
        // retrieve an instance of the ARO registry
        $aro = $acl->aroRegistry();
        $aro->add('guest');

        // ensure we cannot create duplicates
        try {
            $guest = $aro->add('guest');
            $this->fail('Cannot create duplicate aros');
        } catch (Exception $e) {
            // success
        }
        
        // ARO returns a default ARO for non-existant member
        self::assertTrue(($aro->nonexistent instanceof Zend_Acl_Aro));
        self::assertTrue(($aro->nonexistent->getId() == '_default'));

        // ARO returns a correct object for existing member
        $guest = $aro->guest;
        self::assertTrue(($guest instanceof Zend_Acl_Aro));
        
        // Ensure ARO returns a correct reference to parent registry
        self::assertTrue(($guest->getRegistry() === $aro));
        
        // Add permissions to ACL, remove ARO and ensure permissions are wiped
        $acl->deny($guest);
        $acl->allow($guest, array('task1', 'task2'));
        $acl->testbranch->allow($guest, array('task3'));
        $acl->forbidden->deny($guest);
        $acl->temporary->allow($guest);
        $acl->allow(array('guest', 'nonexistent'), 'task4', '/temporary/folder');
        $acl->deny(array('guest', 'nonexistent'), 'task4', '/temporary/folder');
        $acl->allow(array('guest', 'nonexistent'), 'task5', '/temporary/folder');
        $acl->deny(array('guest', 'nonexistent'), 'task5', '/temporary/folder');
        
        // ensure we cannot create get permissions for multiple aros
        try {
            $result = $acl->valid(array('guest', 'staff'));
            $this->fail('Cannot request multiple aros');
        } catch (Exception $e) {
            // success
        }
        
        // Ensure we can query the types of permissions set on an ACO
        $allow = $acl->getAllow();
        self::assertTrue(isset($allow['guest']));
        self::assertTrue(in_array('task1', $allow['guest']));
        self::assertTrue(in_array('task2', $allow['guest']));
        
        // Reset testbranch node and allow all
        $acl->testbranch->allow($guest);
        $allow = $acl->testbranch->getAllow();
        self::assertFalse(in_array('task3', $allow['guest']));
        
        $acl->removeAllow($guest, 'task2');
        $allow = $acl->getAllow();
        self::assertFalse(in_array('task2', $allow['guest']));
        self::assertFalse($acl->valid($guest, 'task2'));
        $deny = $acl->getDeny();
        self::assertTrue(isset($deny['guest']));
        
        // Remove the temporary node and test for non-existent node
        self::assertTrue($acl->temporary->remove());
        self::assertFalse($acl->remove('nonexistent'));
        
        // ensure we cannot remove root node
        try {
            $result = $acl->remove();
            $this->fail('Cannot remove root node');
        } catch (Exception $e) {
            // success
        }
        
        // Get a view of the ACL through an ARO's set of permissions
        $acl2 = $guest->getValidAco($acl, 'task3');
        self::assertTrue($acl2->testbranch->valid($guest, 'task3'));
        self::assertFalse($acl2->temporary->valid($guest, 'task4'));
        self::assertFalse($acl2->valid($guest, 'task3'));
        self::assertFalse($acl2->forbidden->valid($guest));
        
        // See if we can reverse-lookup the valid ARO for this new acl
        $group = $acl->getValidAro('task1');
        self::assertTrue(isset($group['guest']));
        
        // Remove guest from registry and test return results
        self::assertTrue($aro->remove($guest, $acl));
        self::assertFalse($aro->remove('nonexistent', $acl));
        
        // Helps code coverage
        $acl->removeAro('othernonexistent', null, '/nonexistent/path');
        
        // Ensure reference to guest now returns default ARO
        self::assertTrue($aro->guest->getId() == '_default');
        
        // Reset all permissions on root node for Guest and check for defaults
        $acl2->removeAllow('guest');
        $acl2->removeDeny('guest');
        self::assertTrue($acl2->testnode->valid($guest) === Zend_Acl::PERM_DEFAULT);
        
        // Return an array of ARO members from the registry
        $list = $aro->toArray();
        self::assertTrue(is_array($list));
    }
}
