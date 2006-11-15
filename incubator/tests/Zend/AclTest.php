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

    public function testCMSExample()
    {
        // Create new Zend_Acl instance
        $acl = new Zend_Acl();

        // Fetch the ARO registry
        $aro = $acl->getAroRegistry();

        // Add some roles to the ARO registry
        $aro->add('guest');
        $aro->add('staff', $aro->guest);  // staff inherits permissions from guest
        $aro->add('editor', $aro->staff); // editor inherits permissions from staff
        $aro->add('administrator');

        // Whitelist implementation; ACL denies access by default
        $acl->deny();

        // Guest may only view content
        $acl->allow($aro->guest, 'view');

        // Staff inherits view privilege from guest, but also needs additional privileges
        $acl->allow($aro->staff, array('edit', 'submit', 'revise'));

        // Editor inherits view, edit, submit, and revise privileges, but also needs additional privileges
        $acl->allow($aro->editor, array('publish', 'archive', 'delete'));

        // Administrator inherits nothing but is allowed all privileges
        $acl->allow($aro->administrator);

        // Access control checks based on above permission sets

        self::assertTrue($acl->valid('guest', 'view'));
        self::assertFalse($acl->valid('guest', 'edit'));
        self::assertFalse($acl->valid('guest', 'submit'));
        self::assertFalse($acl->valid($aro->guest, 'revise'));
        self::assertFalse($acl->valid('guest', 'publish'));
        self::assertFalse($acl->valid('guest', 'archive'));
        self::assertFalse($acl->valid($aro->guest, 'delete'));
        self::assertFalse($acl->valid('guest', 'unknown'));
        self::assertFalse($acl->valid('guest'));

        self::assertTrue($acl->valid('staff', 'view'));
        self::assertTrue($acl->valid('staff', 'edit'));
        self::assertTrue($acl->valid('staff', 'submit'));
        self::assertTrue($acl->valid($aro->staff, 'revise'));
        self::assertFalse($acl->valid('staff', 'publish'));
        self::assertFalse($acl->valid('staff', 'archive'));
        self::assertFalse($acl->valid($aro->staff, 'delete'));
        self::assertFalse($acl->valid('staff', 'unknown'));
        self::assertFalse($acl->valid('staff'));

        self::assertTrue($acl->valid('editor', 'view'));
        self::assertTrue($acl->valid('editor', 'edit'));
        self::assertTrue($acl->valid('editor', 'submit'));
        self::assertTrue($acl->valid($aro->editor, 'revise'));
        self::assertTrue($acl->valid('editor', 'publish'));
        self::assertTrue($acl->valid('editor', 'archive'));
        self::assertTrue($acl->valid($aro->editor, 'delete'));
        self::assertFalse($acl->valid('editor', 'unknown'));
        self::assertFalse($acl->valid('editor'));

        self::assertTrue($acl->valid('administrator', 'view'));
        self::assertTrue($acl->valid('administrator', 'edit'));
        self::assertTrue($acl->valid('administrator', 'submit'));
        self::assertTrue($acl->valid($aro->administrator, 'revise'));
        self::assertTrue($acl->valid('administrator', 'publish'));
        self::assertTrue($acl->valid('administrator', 'archive'));
        self::assertTrue($acl->valid($aro->administrator, 'delete'));
        self::assertTrue($acl->valid('administrator', 'unknown'));
        self::assertTrue($acl->valid('administrator'));

        // Some checks on specific areas, which inherit access controls from the root ACL node
        self::assertTrue($acl->newsletter->pending->valid('guest', 'view'));
        self::assertTrue($acl->gallery->profiles->valid($aro->staff, 'revise'));
        self::assertFalse($acl->config->hosts->valid($aro->editor, 'unknown'));

        // Checking permissions from the perspective of an ARO
        self::assertTrue($aro->staff->canAccess($acl->newsletter->pending, 'view'));
        self::assertTrue($aro->staff->canAccess($acl->newsletter->pending, 'edit'));
        self::assertFalse($aro->staff->canAccess($acl->newsletter->pending, 'publish'));
        self::assertFalse($aro->staff->canAccess($acl->newsletter->pending));
        self::assertTrue($aro->administrator->canAccess($acl->newsletter->pending));

        // Unknown ARO
        self::assertFalse($aro->unknown->canAccess($acl->newsletter->pending));

        // Add a new group, marketing, which bases its permissions on staff
        $aro->add('marketing', 'staff');

        // Refine the privilege sets for more specific needs

        // Allow marketing to publish and archive newsletters
        $acl->newsletter->allow($aro->marketing, array('publish', 'archive'));

        // Allow marketing to publish and archive latest news
        $acl->news->latest->allow($aro->marketing, array('publish', 'archive'));

        // Deny staff (and marketing, by inheritance) rights to revise latest news
        $acl->news->latest->deny($aro->staff, 'revise');

        // Deny everyone access to archive news announcements
        $acl->news->announcement->deny(null, 'archive');

        // Access control checks for the above refined permission sets

        self::assertTrue($acl->valid('marketing', 'view'));
        self::assertTrue($acl->valid('marketing', 'edit'));
        self::assertTrue($acl->valid('marketing', 'submit'));
        self::assertTrue($acl->valid($aro->marketing, 'revise'));
        self::assertFalse($acl->valid('marketing', 'publish'));
        self::assertFalse($acl->valid('marketing', 'archive'));
        self::assertFalse($acl->valid($aro->marketing, 'delete'));
        self::assertFalse($acl->valid('marketing', 'unknown'));
        self::assertFalse($acl->valid('marketing'));

        self::assertTrue($acl->newsletter->valid('marketing', 'publish'));
        self::assertFalse($acl->newsletter->pending->valid('staff', 'publish'));
        self::assertTrue($acl->newsletter->pending->valid('marketing', 'publish'));
        self::assertTrue($acl->newsletter->valid('marketing', 'archive'));
        self::assertFalse($acl->newsletter->valid('marketing', 'delete'));
        self::assertFalse($acl->newsletter->valid('marketing'));

        self::assertTrue($acl->news->latest->valid('marketing', 'publish'));
        self::assertTrue($acl->news->latest->valid('marketing', 'archive'));
        self::assertFalse($acl->news->latest->valid('marketing', 'delete'));
        self::assertFalse($acl->news->latest->valid('marketing', 'revise'));
        self::assertFalse($acl->news->latest->valid('staging', 'revise'));
        self::assertFalse($acl->news->latest->valid('marketing'));

        self::assertFalse($acl->news->announcement->valid('marketing', 'archive'));
        self::assertFalse($acl->news->announcement->valid('staff', 'archive'));
        self::assertFalse($acl->news->announcement->valid('administrator', 'archive'));

        self::assertFalse($aro->staff->canAccess($acl->news->latest, 'publish'));
        self::assertTrue($aro->marketing->canAccess($acl->news->latest, 'publish'));
        self::assertFalse($aro->editor->canAccess($acl->news->announcement, 'archive'));

        // Remove some previous permission specifications

        // Marketing can no longer publish and archive newsletters
        $acl->newsletter->removeAllow('marketing', array('publish', 'archive'));

        // Marketing can no longer archive the latest news
        $acl->news->latest->removeAllow($aro->marketing, 'archive');

        // Now staff (and marketing, by inheritance) may revise latest news
        $acl->news->latest->removeDeny($aro->staff, 'revise');

        // Access control checks for the above refinements

        self::assertFalse($acl->newsletter->valid('marketing', 'publish'));
        self::assertFalse($acl->newsletter->valid('marketing', 'archive'));

        self::assertFalse($acl->news->latest->valid('marketing', 'archive'));

        self::assertTrue($acl->news->latest->valid('staff', 'revise'));
        self::assertTrue($acl->news->latest->valid($aro->marketing, 'revise'));

        // Grant marketing all permissions on the latest news
        $acl->news->latest->allow('marketing');

        // Access control checks for the above refinement
        self::assertTrue($acl->news->latest->valid('marketing', 'archive'));
        self::assertTrue($acl->news->latest->valid('marketing', 'publish'));
        self::assertTrue($acl->news->latest->valid('marketing', 'edit'));
        self::assertTrue($acl->news->latest->valid('marketing'));

        // Create second Zend_Acl instance
        $acl2 = new Zend_Acl();

        // Fetch a new ARO registry
        $aro2 = $acl2->getAroRegistry();

        // Ensure registries are unique instances
        self::assertTrue($aro !== $aro2);

    }

    public function testRegression()
    {
        $acl = new Zend_Acl();

        // retrieve an instance of the ARO registry
        $aro = $acl->getAroRegistry();
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
        $aro = $acl->getAroRegistry();
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

        // Remove the temporary node
        try {
            $acl->temporary->remove();
        } catch (Exception $e) {
            $this->fail('Cannot remove temporary node: ' . $e->getMessage());
        }

        // Test for non-existent node
        try {
            $path = 'nonexistent';
            $acl->remove($path);
            $this->fail('Expected exception not thrown when removing non-existent node');
        } catch (Zend_Acl_Exception $expected) {
            $this->assertContains($path, $expected->getMessage());
        } catch (Exception $e) {
            $this->fail('Unexpected exception caught: ' . $e->getMessage());
        }

        // Ensure we cannot remove root node
        try {
            $acl->remove();
            $this->fail('Expected exception not thrown when removing root node');
        } catch (Zend_Acl_Exception $expected) {
            $this->assertContains('root node', $expected->getMessage());
        } catch (Exception $e) {
            $this->fail('Unexpected exception caught: ' . $e->getMessage());
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
