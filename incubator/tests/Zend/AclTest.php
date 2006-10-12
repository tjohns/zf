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

}
