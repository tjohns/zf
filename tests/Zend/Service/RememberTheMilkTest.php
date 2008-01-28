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
 * @package    Zend_Service_RememberTheMilk
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: RememberTheMilkTest.php 5393 2007-06-20 21:16:06Z darby $
 */


/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Zend_Service_RememberTheMilk
 */
require_once 'Zend/Service/RememberTheMilk.php';

/**
 * @see Zend_Http_Client
 */
require_once 'Zend/Http/Client.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Service_RememberTheMilk
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilkTest extends PHPUnit_Framework_TestCase
{
    /**
     * Remember The Milk service consumer
     *
     * @var Zend_Service_RememberTheMilk
     */
    protected static $_rtm;

    /**
     * Frob used to obtain the authentication token
     *
     * @var string
     */
    protected static $_frob;

    /**
     * Authentication token
     *
     * @var Zend_Service_RememberTheMilk_Token
     */
    protected static $_token;

    /**
     * Username of the user to use for authentication
     *
     * @var string
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    protected static $_username = 'zfrtm';

    /**
     * Password of the user to use for authentication
     *
     * @var string
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    protected static $_password = 'mgt37ge';

    /**
     * Username used in tests where one is required
     *
     * @var string
     */
    protected static $_testUser = 'tobias382';

    /**
     * Group name used in tests where one is required
     *
     * @var string
     */
    protected static $_testGroup = 'test group';

    /**
     * List name used in tests where one is required
     *
     * @var string
     */
    protected static $_testList = 'test list';

    /**
     * Task name used in tests where one is required
     *
     * @var string
     */
    protected static $_testTask = 'test task';

    public function setUp()
    {
        self::$_rtm = new Zend_Service_RememberTheMilk(
            '10594f671e7a83afc935b5973ec9db42',
            '57ec469f390eec00'
        );

        self::$_rtm->setThrottleTime(1);

        if (self::$_token !== null) {
            self::$_rtm->setToken(self::$_token);
        }
    }

    protected function _getGroup()
    {
        $groups = self::$_rtm->getGroupList();
        $group = $groups->getGroupByName(self::$_testGroup);

        if ($group === null) {
            self::$_rtm->addGroup(self::$_testGroup);
            $groups = self::$_rtm->getGroupList();
            $group = $groups->getGroupByName(self::$_testGroup);
            if ($group === null) {
                $this->markTestSkipped('Group does not exist and creation failed');
            }
        }

        return $group;
    }

    protected function _getContact()
    {
        $contacts = self::$_rtm->getContactList();
        $contact = $contacts->getContactByUsername(self::$_testUser);

        if ($contact === null) {
            self::$_rtm->addContact(self::$_testUser);
            $contacts = self::$_rtm->getContactList();
            $contact = $contacts->getContactByUsername(self::$_testUser);
            if ($contact === null) {
                $this->markTestSkipped('Contact does not exist and creation failed');
            }
        }

        return $contact;
    }

    protected function _getList()
    {
        $lists = self::$_rtm->getListList();
        $list = $lists->getListByName(self::$_testList);

        if ($list === null) {
            self::$_rtm->addList(self::$_testList);
            $lists = self::$_rtm->getListList();
            $list = $lists->getListByName(self::$_testList);
            if ($list === null) {
                $this->markTestSkipped('List does not exist and creation failed');
            }
        }

        return $list;
    }

    protected function _getTaskSeries()
    {
        $seriesList = self::$_rtm->getTaskList();
        $series = $seriesList->getSeriesByName(self::$_testTask);

        if ($series === null) {
            self::$_rtm->addTask(self::$_testTask);
            $seriesList = self::$_rtm->getTaskList();
            $series = $seriesList->getSeriesByName(self::$_testTask);
            if ($series === null) {
                $this->markTestSkipped('Task series does not exist and creation failed');
            }
        }

        return $series;
    }

    protected function _getTask()
    {
        return reset($this->_getTaskSeries()->getTaskList());
    }

    protected function _getNote($id = null)
    {
        if ($id === null) {
            $series = $this->_getTaskSeries();
            $task = reset($series->getTaskList());
            $note = reset($task->getNoteList());
            if ($note === null) {
                $note = self::$_rtm->addNote(
                    $series->getListId(),
                    $series->getId(),
                    $task->getId(),
                    'test note',
                    'test note text'
                );
                if ($note === null) {
                    $this->markTestSkipped('Note does not exist and creation failed');
                }
            }
            return $note;
        } else {
            return $this->_getTaskSeries()->getNoteList()->getNoteById($id);
        }
    }

    public function testGetFrob()
    {
        self::$_frob = self::$_rtm->getFrob();

        $this->assertRegExp(
            '/^[a-f0-9]{40}$/',
            self::$_frob,
            'Frob appears to be invalid'
        );
    }

    /**
     * @todo Figure out how to handle authentication and authorization
     * using human intervention and outside of the test functions
     */
    public function testGetToken()
    {
        /**
         * Scrape the web site to authenticate and just get delete
         * permissions so all operations will be allowed
         */

        $url = self::$_rtm->getAuthUrl(
            Zend_Service_RememberTheMilk::PERMS_DELETE,
            self::$_frob
        );
        $parsed = parse_url($url);
        $query = $parsed['query'];

        $params = array(
            'username' => self::$_username,
            'password' => self::$_password,
            'remember' => 'on',
            'login' => 'Login',
            'continue' => 'home',
            'api' => $query,
            'u' => '1'
        );

        $http = new Zend_Http_Client();
        $http->setCookieJar();
        $http->setMethod(Zend_Http_Client::POST);
        $http->setConfig(array('strictredirects' => true));

        $http->setUri('https://www.rememberthemilk.com/auth.rtm');
        $http->setParameterPost($params);
        $response = $http->request();

        if (!$response->isSuccessful()) {
            throw new Zend_Service_Exception('Authentication failed');
        }

        sleep(1);

        $params = array(
            'authorize_yes' => 'Yes, go for it!'
        );

        $http->setUri('http://www.rememberthemilk.com/services/auth/?' . $query);
        $http->setParameterPost($params);
        $response = $http->request();

        if (!$response->isSuccessful()
            || !strpos($response->getBody(), 'Application successfully authorized')) {
            throw new Zend_Service_Exception('Authorization failed');
        }

        sleep(1);

        /**
         * Authentication and authorization logic ends here
         */

        self::$_token = self::$_rtm->getToken(self::$_frob);

        $this->assertTrue(
            self::$_token instanceof Zend_Service_RememberTheMilk_Token,
            'Returned token is not an instance'
        );

        $this->assertRegExp(
            '/^[a-f0-9]{40}$/',
            self::$_token->getToken(),
            'Returned token string appears to be invalid'
        );

        $this->assertEquals(
            self::$_token->getPerms(),
            Zend_Service_RememberTheMilk::PERMS_DELETE,
            'Permissions are inconsistent'
        );

        $user = self::$_token->getUser();

        $this->assertTrue(
            $user instanceof Zend_Service_RememberTheMilk_Contact,
            'Token user is not an instance'
        );
    }

    public function testCheckToken()
    {
        $this->markTestSkipped();

        $local = self::$_token;
        $remote = self::$_rtm->checkToken($local->getToken());

        $this->assertEquals(
            $local->getToken(),
            $remote->getToken(),
            'Token strings do not match'
        );

        $this->assertEquals(
            $local->getPerms(),
            $remote->getPerms(),
            'Permissions strings do not match'
        );

        $localUser = $local->getUser();
        $remoteUser = $remote->getUser();

        $this->assertEquals(
            $localUser->getId(),
            $remoteUser->getId(),
            'User identifiers do not match'
        );

        $this->assertEquals(
            $localUser->getUsername(),
            $remoteUser->getUsername(),
            'Usernames do not match'
        );

        $this->assertEquals(
            $localUser->getFullName(),
            $remoteUser->getFullName(),
            'User full names do not match'
        );
    }

    public function testGetContactList()
    {
        $this->markTestSkipped();

        $contactList = self::$_rtm->getContactList();

        $this->assertTrue(
            $contactList instanceof Zend_Service_RememberTheMilk_ContactList,
            'Contact list is not an instance'
        );
    }

    public function testAddContact()
    {
        $this->markTestSkipped();

        $before = self::$_rtm->getContactList();
        $contact = $before->getContactByUsername(self::$_testUser);
        if ($contact !== null) {
            self::$_rtm->deleteContact($contact->getId());
            $before = self::$_rtm->getContactList();
            $contact = $before->getContactByUsername(self::$_testUser);
            if ($contact !== null) {
                $this->markTestSkipped('User exists and deletion failed');
            }
        }

        $added = self::$_rtm->addContact(self::$_testUser);

        $after = self::$_rtm->getContactList();
        $contact = $after->getContactByUsername(self::$_testUser);

        $this->assertTrue(
            $contact instanceof Zend_Service_RememberTheMilk_Contact,
            'Contact was not added'
        );

        $this->assertEquals(
            $contact->getId(),
            $added->getId(),
            'Contact identifiers do not match'
        );

        $this->assertEquals(
            $contact->getFullName(),
            $added->getFullName(),
            'Contact full names do not match'
        );

        $this->assertEquals(
            $contact->getUsername(),
            $added->getUsername(),
            'Contact usernames do not match'
        );
    }

    public function testDeleteContact()
    {
        $this->markTestSkipped();

        $before = self::$_rtm->getContactList();
        $contact = $before->getContactByUsername(self::$_testUser);

        if ($contact === null) {
            self::$_rtm->addContact(self::$_testUser);
            $before = self::$_rtm->getContactList();
            $contact = $before->getContactByUsername(self::$_testUser);
            if ($contact === null) {
                $this->markTestSkipped('User does not exist and creation failed');
            }
        }

        self::$_rtm->deleteContact($contact->getId());

        $after = self::$_rtm->getContactList();
        $contact = $after->getContactByUsername(self::$_testUser);

        $this->assertNull(
            $contact,
            'Contact was not deleted'
        );
    }

    public function testGetGroupList()
    {
        $this->markTestSkipped();

        $groupList = self::$_rtm->getGroupList();

        $this->assertTrue(
            $groupList instanceof Zend_Service_RememberTheMilk_GroupList,
            'Group list is not an instance'
        );
    }

    public function testAddGroup()
    {
        $this->markTestSkipped();

        $before = self::$_rtm->getGroupList();
        $group = $before->getGroupByName(self::$_testGroup);
        if ($group !== null) {
            self::$_rtm->deleteGroup($group->getId());
            $before = self::$_rtm->getGroupList();
            $group = $before->getGroupByName(self::$_testGroup);
            if ($group !== null) {
                $this->markTestSkipped('Group exists and deletion failed');
            }
        }

        $added = self::$_rtm->addGroup(self::$_testGroup);

        $after = self::$_rtm->getGroupList();
        $group = $after->getGroupByName(self::$_testGroup);

        $this->assertTrue(
            $group instanceof Zend_Service_RememberTheMilk_Group,
            'Group was not added'
        );

        $this->assertEquals(
            $group->getId(),
            $added->getId(),
            'Group identifiers do not match'
        );

        $this->assertEquals(
            $group->getName(),
            $added->getName(),
            'Group names do not match'
        );

        $this->assertEquals(
            $group->getContacts(),
            $added->getContacts(),
            'Group contacts do not match'
        );
    }

    public function testAddContactToGroup()
    {
        $this->markTestSkipped();

        $group = $this->_getGroup();
        $contact = $this->_getContact();

        if (in_array($contact->getId(), $group->getContacts())) {
            self::$_rtm->removeContactFromGroup($group->getId(), $contact->getId());
            $groups = self::$_rtm->getGroupList();
            $group = $groups->getGroupByName(self::$_testGroup);
            if (in_array($contact->getId(), $group->getContacts())) {
                $this->markTestSkipped('Contact is in group and removal failed');
            }
        }

        self::$_rtm->addContactToGroup($group->getId(), $contact->getId());

        $groups = self::$_rtm->getGroupList();
        $group = $groups->getGroupByName(self::$_testGroup);

        $this->assertTrue($group->hasContact($contact->getId()));
    }

    public function testRemoveContactFromGroup()
    {
        $this->markTestSkipped();

        $group = $this->_getGroup();
        $contact = $this->_getContact();

        if (! in_array($contact->getId(), $group->getContacts())) {
            self::$_rtm->addContactToGroup($group->getId(), $contact->getId());
            $groups = self::$_rtm->getGroupList();
            $group = $groups->getGroupByName(self::$_testGroup);
            if (! in_array($contact->getId(), $group->getContacts())) {
                $this->markTestSkipped('Contact is not in group and addition failed');
            }
        }

        self::$_rtm->removeContactFromGroup($group->getId(), $contact->getId());

        $groups = self::$_rtm->getGroupList();
        $group = $groups->getGroupByName(self::$_testGroup);

        $this->assertFalse(
            $group->hasContact($contact->getId()),
            'Contact was not removed'
        );
    }

    public function testDeleteGroup()
    {
        $this->markTestSkipped();

        $group = $this->_getGroup();

        self::$_rtm->deleteGroup($group->getId());

        $after = self::$_rtm->getGroupList();
        $group = $after->getGroupByName(self::$_testGroup);

        $this->assertNull($group, 'Group was not deleted');
    }

    public function testAddList()
    {
        $this->markTestSkipped();

        $before = self::$_rtm->getListList();
        $list = $before->getListByName(self::$_testList);
        if ($list !== null) {
            self::$_rtm->deleteList($list->getId());
            $before = self::$_rtm->getListList();
            $list = $before->getListByName(self::$_testList);
            if ($list !== null) {
                $this->markTestSkipped('List exists and deletion failed');
            }
        }

        $added = self::$_rtm->addList(self::$_testList);

        $after = self::$_rtm->getListList();
        $list = $after->getListByName(self::$_testList);

        $this->assertTrue(
            $list instanceof Zend_Service_RememberTheMilk_List,
            'List was not added'
        );

        $this->assertEquals(
            $list->getId(),
            $added->getId(),
            'List identifiers do not match'
        );

        $this->assertEquals(
            $list->getName(),
            $added->getName(),
            'List names do not match'
        );

        $this->assertEquals(
            $list->getPosition(),
            $added->getPosition(),
            'List positions do not match'
        );

        $this->assertEquals(
            $list->getFilter(),
            $added->getFilter(),
            'List filters do not match'
        );

        $this->assertEquals(
            $list->isDeleted(),
            $added->isDeleted(),
            'List deletion statuses do not match'
        );

        $this->assertEquals(
            $list->isLocked(),
            $added->isLocked(),
            'List lock statuses do not match'
        );

        $this->assertEquals(
            $list->isArchived(),
            $added->isArchived(),
            'List archive statuses do not match'
        );

        $this->assertEquals(
            $list->isSmart(),
            $added->isSmart(),
            'List smart statuses do not match'
        );
    }

    public function testSetDefaultList()
    {
        $this->markTestSkipped();

        $list = $this->_getList();

        self::$_rtm->setDefaultList($list->getId());

        $settings = self::$_rtm->getSettings();

        $this->assertEquals(
            $settings->getDefaultList(),
            $list->getId(),
            'Default list was not set'
        );
    }

    public function testGetListList()
    {
        $this->markTestSkipped();

        $listList = self::$_rtm->getListList();

        $this->assertTrue(
            $listList instanceof Zend_Service_RememberTheMilk_ListList,
            'List list is not an instance'
        );
    }

    public function testDeleteList()
    {
        $this->markTestSkipped();

        $list = $this->_getList();

        self::$_rtm->deleteList($list->getId());

        $after = self::$_rtm->getGroupList();
        $group = $after->getGroupByName(self::$_testGroup);

        $this->assertNull($group, 'Group was not deleted');
    }

    public function testRenameList()
    {
        $this->markTestSkipped();

        $listName = 'new list';

        $list = $this->_getList();

        self::$_rtm->renameList($list->getId(), $listName);

        $lists = self::$_rtm->getListList();

        $this->assertNotNull(
            $lists->getListByName($listName),
            'List with new name does not exist'
        );

        $this->assertNull(
            $lists->getListByName(self::$_testList),
            'List with old name still exists'
        );

        self::$_rtm->renameList($list->getId(), self::$_testList);
    }

    public function testArchiveList()
    {
        $this->markTestSkipped();

        $list = $this->_getList();

        if ($list->isArchived()) {
            self::$_rtm->unarchiveList($list->getId());
            $lists = self::$_rtm->getListList();
            $list = $lists->getListByName(self::$_testList);
            if ($list->isArchived()) {
                $this->markTestSkipped('List is already archived and unarchival failed');
            }
        }

        self::$_rtm->archiveList($list->getId());

        $lists = self::$_rtm->getListList();
        $list = $lists->getListByName(self::$_testList);

        $this->assertTrue($list->isArchived(), 'List was not archived');
    }

    public function testUnarchiveList()
    {
        $this->markTestSkipped();

        $list = $this->_getList();

        if (!$list->isArchived()) {
            self::$_rtm->archiveList($list->getId());
            $lists = self::$_rtm->getListList();
            $list = $lists->getListByName(self::$_testList);
            if (!$list->isArchived()) {
                $this->markTestSkipped('List is not archived and archival failed');
            }
        }

        self::$_rtm->unarchiveList($list->getId());

        $lists = self::$_rtm->getListList();
        $list = $lists->getListByName(self::$_testList);

        $this->assertFalse($list->isArchived(), 'List was not unarchived');
    }

    public function testAddTask()
    {
        $this->markTestIncomplete();

        $list = $this->_getList();

        $series = self::$_rtm->addTask(self::$_testTask, $list->getId());

        $this->assertTrue(
            $series instanceof Zend_Service_RememberTheMilk_TaskSeries,
            'Series is not an instance'
        );

        $seriesList = self::$_rtm->getTaskList($list->getId());

        $this->assertNotNull(
            $seriesList->getSeriesById($series->getId()),
            'Series was not added'
        );
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testAddTaskTags()
    {
        $this->markTestIncomplete();
    }

    public function testCompleteTask()
    {
        $this->markTestIncomplete();

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        if ($task->isComplete()) {
            self::$_rtm->uncompleteTask(
                $series->getListId(),
                $series->getId(),
                $task->getId()
            );
            if ($this->_getTask()->isComplete()) {
                $this->markTestSkipped('Task is complete and uncompletion failed');
            }
        }

        self::$_rtm->completeTask(
            $series->getListId(),
            $series->getId(),
            $task->getId()
        );

        $task = $this->_getTask();

        $this->assertTrue(
            $task->isComplete(),
            'Task was not completed'
        );
    }

    public function testGetTaskList()
    {
        $this->markTestSkipped();

        $taskList = self::$_rtm->getTaskList();

        $this->assertTrue(
            $taskList instanceof Zend_Service_RememberTheMilk_TaskSeriesList,
            'Task list is not an instance'
        );
    }

    public function testMoveTaskPriority()
    {
        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        self::$_rtm->setTaskPriority(
            $series->getListId(),
            $series->getId(),
            $task->getId(),
            2
        );

        $task = $this->_getTask();

        $this->assertEquals(
            $task->getPriority(),
            2,
            'Task priority was not set'
        );

        self::$_rtm->moveTaskPriority(
            $series->getListId(),
            $series->getId(),
            $task->getId(),
            Zend_Service_RememberTheMilk::PRIORITY_UP
        );

        $task = $this->_getTask();

        $this->assertEquals(
            $task->getPriority(),
            1,
            'Task priority was not moved'
        );
    }

    public function testMoveTask()
    {
        $this->markTestIncomplete();

        $lists = self::$_rtm->getListList();

        $from = $lists->getListByName(self::$_testList);
        $to = $lists->getListByName('Inbox');
        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        self::$_rtm->moveTask(
            $from->getId(),
            $to->getId(),
            $series->getId(),
            $task->getId()
        );

        $tasks = self::$_rtm->getTaskList($to->getId());

        $this->assertNotNull(
            $tasks->getSeriesById($series->getId()),
            'Task was not moved'
        );

        self::$_rtm->moveTask(
            $to->getId(),
            $from->getId(),
            $series->getId(),
            $task->getId()
        );
    }

    public function testPostponeTask()
    {
        $this->markTestIncomplete();

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        self::$_rtm->postponeTask(
            $series->getListId(),
            $series->getId(),
            $task->getId()
        );

        $task = $this->_getTask();
        $date = new Zend_Date();

        $this->assertTrue(
            $date->equals($task->getDueDate()),
            'Task was not postponed'
        );
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testRemoveTaskTags()
    {
        $this->markTestIncomplete();
    }

    public function testSetTaskDueDate()
    {
        $this->markTestIncomplete();

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());
        $date = new Zend_Date();

        self::$_rtm->setTaskDueDate(
            $series->getListId(),
            $series->getId(),
            $task->getId(),
            $date->toString()
        );

        $this->assertTrue(
            $date->equals($this->_getTask()->getDueDate()),
            'Task due date was not set'
        );
    }

    public function testSetTaskEstimate()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testSetTaskLocation()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testSetTaskName()
    {
        $this->markTestIncomplete();
    }

    public function testSetTaskPriority()
    {
        $this->markTestIncomplete();

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        $priority = $task->getPriority() ? 'N' : '1';
        self::$_rtm->setTaskPriority(
            $series->getListId(),
            $seties->getId(),
            $task->getId(),
            $priority
        );

        $task = reset($this->_getTaskSeries()->getTaskList());

        $this->assertEquals(
            $task->getPriority(),
            $priority,
            'Priority was not changed'
        );
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testSetTaskRecurrence()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testSetTaskTags()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Determine if this is specific to task or task series
     */
    public function testSetTaskUrl()
    {
        $this->markTestIncomplete();
    }

    public function testUncompleteTask()
    {
        $this->markTestIncomplete();

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        if (!$task->isComplete()) {
            self::$_rtm->completeTask(
                $series->getListId(),
                $series->getId(),
                $task->getId()
            );
            if (!$this->_getTask()->isComplete()) {
                $this->markTestSkipped('Task is not complete and completion failed');
            }
        }

        self::$_rtm->uncompleteTask(
            $series->getListId(),
            $series->getId(),
            $task->getId()
        );

        $task = $this->_getTask();

        $this->assertFalse(
            $task->isComplete(),
            'Task was not uncompleted'
        );
    }

    public function testAddNote()
    {
        $this->markTestIncomplete();

        $title = 'test note';
        $text = 'test note text';

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        $added = self::$_rtm->addNote(
            $series->getListId(),
            $series->getId(),
            $task->getId(),
            $title,
            $text
        );

        $note = $this->_getNote($added->getId());

        $this->assertTrue(
            $note instanceof Zend_Service_RememberTheMilk_Note,
            'Note is not an instance'
        );

        $this->assertNotNull(
            $note,
            'Note was not added'
        );

        $this->assertEquals(
            $note->getTitle(),
            $added->getTitle(),
            'Note titles do not match'
        );

        $this->assertEquals(
            $note->getText(),
            $added->getText(),
            'Note text does not match'
        );
    }

    public function testEditNote()
    {
        $this->markTestIncomplete();

        $title = 'new note';
        $text = 'new note text';

        $note = $this->_getNote();

        self::$_rtm->editNote(
            $note->getId(),
            $title,
            $text
        );

        $note = $this->_getNote($note->getId());

        $this->assertEquals(
            $note->getTitle(),
            $title,
            'Titles do not match'
        );

        $this->assertEquals(
            $note->getText(),
            $text,
            'Text does not match'
        );
    }

    public function testDeleteNote()
    {
        $this->markTestIncomplete();

        $note = $this->_getNote();

        self::$_rtm->deleteNote($note->getId());

        $note = $this->_getNote($note->getId());

        $this->assertNull(
            $note,
            'Note was not deleted'
        );
    }

    public function testDeleteTask()
    {
        $this->markTestIncomplete();

        $series = $this->_getTaskSeries();
        $task = reset($series->getTaskList());

        $task = self::$_rtm->deleteTask(
            $series->getListId(),
            $series->getId(),
            $task->getId()
        );

        $this->assertNotEquals(
            $task->getDeletedDate(),
            '',
            'Task was not deleted'
        );
    }

    public function testGetMethods()
    {
        $this->markTestIncomplete();

        $expected = array(
            'rtm.auth.checkToken',
            'rtm.auth.getFrob',
            'rtm.auth.getToken',
            'rtm.contacts.add',
            'rtm.contacts.delete',
            'rtm.contacts.getList',
            'rtm.groups.add',
            'rtm.groups.addContact',
            'rtm.groups.delete',
            'rtm.groups.getList',
            'rtm.groups.removeContact',
            'rtm.lists.add',
            'rtm.lists.archive',
            'rtm.lists.delete',
            'rtm.lists.getList',
            'rtm.lists.setDefaultList',
            'rtm.lists.setName',
            'rtm.lists.unarchive',
            'rtm.locations.getList',
            'rtm.reflection.getMethodInfo',
            'rtm.reflection.getMethods',
            'rtm.settings.getList',
            'rtm.tasks.add',
            'rtm.tasks.addTags',
            'rtm.tasks.complete',
            'rtm.tasks.delete',
            'rtm.tasks.getList',
            'rtm.tasks.movePriority',
            'rtm.tasks.moveTo',
            'rtm.tasks.notes.add',
            'rtm.tasks.notes.delete',
            'rtm.tasks.notes.edit',
            'rtm.tasks.postpone',
            'rtm.tasks.removeTags',
            'rtm.tasks.setDueDate',
            'rtm.tasks.setEstimate',
            'rtm.tasks.setLocation',
            'rtm.tasks.setName',
            'rtm.tasks.setPriority',
            'rtm.tasks.setRecurrence',
            'rtm.tasks.setTags',
            'rtm.tasks.setURL',
            'rtm.tasks.uncomplete',
            'rtm.test.echo',
            'rtm.test.login',
            'rtm.time.convert',
            'rtm.time.parse',
            'rtm.timelines.create',
            'rtm.timezones.getList',
            'rtm.transactions.undo'
        );

        $actual = self::$_rtm->getMethods();
        sort($actual);

        $this->assertEquals(
            $expected,
            $actual,
            'Method lists do not match'
        );
    }

    public function testGetMethodInfo()
    {
        $this->markTestIncomplete();

        $method = self::$_rtm->getMethodInfo('rtm.test.login');

        $this->assertEquals(
            $method->getName(),
            'rtm.test.login',
            'Method names do not match'
        );

        $this->assertTrue(
            $method->needsLogin(),
            'Login flags do not match'
        );

        $this->assertTrue(
            $method->needsSigning(),
            'Signature flags do not match'
        );

        $this->assertEquals(
            $method->requiredPerms(),
            Zend_Service_RememberTheMilk::PERMS_READ,
            'Required permissions do not match'
        );

        /**
         * @todo Cast both to DOMDocument instances
         */
        $actual = new DOMDocument();
        $actual->loadXML($method->getResponse());
        $expected = new DOMDocument();
        $expected->loadXML('A testing method which checks if the caller is logged in.');
        $this->assertEquals(
            $actual,
            $expected,
            'Descriptions do not match'
        );

        $actual = new DOMDocument();
        $actual->loadXML($method->getResponse());
        $expected = new DOMDocument();
        $expected->loadXML('<user id="987654321"><username>bob<username></user>');
        $this->assertEquals(
            $actual,
            $expected,
            'Responses do not match'
        );

        $arg = $method->getArguments()->getArgumentByName('api_key');

        $this->assertFalse(
            $arg->isOptional(),
            'Optional flags do not match'
        );

        $this->assertEquals(
            $arg->getDescription(),
            'Your API application key. <a href="/services/api/api_key.rtm">See here</a> for more details.',
            'Descriptions do not match'
        );

        $errors = $method->getErrors();

        $error = reset($errors);

        $this->assertEquals(
            $error->getCode(),
            '96',
            'Error codes do not match'
        );

        $this->assertEquals(
            $error->getMessage(),
            'Invalid signature',
            'Error messages do not match'
        );

        $this->assertEquals(
            $error->getDescription(),
            'The passed signature was invalid.',
            'Error descriptions do not match'
        );
    }

    public function testGetSettings()
    {
        $this->markTestIncomplete();

        $list = $this->_getList();
        self::$_rtm->setDefaultList($list->getId());

        $settings = self::$_rtm->getSettings();

        $this->assertTrue(
            $settings instanceof Zend_Service_RememberTheMilk_Settings,
            'Settings is not an instance'
        );

        $this->assertEquals(
            $settings->getTimezone(),
            'Australia/Sydney',
            'Timezones do not match'
        );

        $this->assertFalse(
            $settings->getDateFormat(),
            'Date formats do not match'
        );

        $this->assertFalse(
            $settings->getTimeFormat(),
            'Time formats do not match'
        );

        $this->assertEquals(
            $list->getId(),
            $settings->getDefaultList(),
            'Default lists do not match'
        );
    }

    public function testConvertTime()
    {
        $this->markTestIncomplete();

        $converted = self::$_rtm->convertTime(
            'Australia/Sydney',
            'America/Chicago',
            '2006-05-07T10:00:00'
        );

        /**
         * @todo Provide a converted time here
         */
        $this->assertEquals(
            $converted,
            '',
            'Time was not converted'
        );
    }

    public function testParseTime()
    {
        $this->markTestIncomplete();

        $this->assertEquals(
            self::$_rtm->parseTime('5/10/06 7:00 AM'),
            '2006-05-10T07:00:00Z',
            'Parsed time does not match expected value'
        );
    }

    public function testGetTimezoneList()
    {
        $this->markTestIncomplete();

        $timezones = self::$_rtm->getTimezoneList();

        $this->assertTrue(
            $timezones instanceof Zend_Service_RememberTheMilk_TimezoneList,
            'Timezone list is not an instance'
        );
    }

    public function testUndo()
    {
        $this->markTestIncomplete();

        $name = 'renamed list';

        $list = $this->_getList();

        self::$_rtm->renameList($list->getId(), $name);

        $lists = self::$_rtm->getListList();
        $list = $lists->getListByName($name);
        if ($list === null) {
            $this->markTestSkipped('Could not rename list');
        }

        self::$_rtm->undo();

        $lists = self::$_rtm->getListList();
        $list = $lists->getListById($list->id());

        $this->assertEquals(
            $list->getName(),
            self::$_testList,
            'Transaction was not undone'
        );
    }
}