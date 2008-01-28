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
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Service_Exception
 */
require_once 'Zend/Service/Exception.php';

/**
 * @see Zend_Date
 */
require_once 'Zend/Date.php';

/**
 * @see Zend_Json
 */
require_once 'Zend/Json.php';

/**
 * @see Zend_Service_RememberTheMilk_Argument
 */
require_once 'Zend/Service/RememberTheMilk/Argument.php';

/**
 * @see Zend_Service_RememberTheMilk_ArgumentList
 */
require_once 'Zend/Service/RememberTheMilk/ArgumentList.php';

/**
 * @see Zend_Service_RememberTheMilk_Contact
 */
require_once 'Zend/Service/RememberTheMilk/Contact.php';

/**
 * @see Zend_Service_RememberTheMilk_ContactList
 */
require_once 'Zend/Service/RememberTheMilk/ContactList.php';

/**
 * @see Zend_Service_RememberTheMilk_Error
 */
require_once 'Zend/Service/RememberTheMilk/Error.php';

/**
 * @see Zend_Service_RememberTheMilk_ErrorList
 */
require_once 'Zend/Service/RememberTheMilk/ErrorList.php';

/**
 * @see Zend_Service_RememberTheMilk_Group
 */
require_once 'Zend/Service/RememberTheMilk/Group.php';

/**
 * @see Zend_Service_RememberTheMilk_GroupList
 */
require_once 'Zend/Service/RememberTheMilk/GroupList.php';

/**
 * @see Zend_Service_RememberTheMilk_List
 */
require_once 'Zend/Service/RememberTheMilk/List.php';

/**
 * @see Zend_Service_RememberTheMilk_ListList
 */
require_once 'Zend/Service/RememberTheMilk/ListList.php';

/**
 * @see Zend_Service_RememberTheMilk_Method
 */
require_once 'Zend/Service/RememberTheMilk/Method.php';

/**
 * @see Zend_Service_RememberTheMilk_Note
 */
require_once 'Zend/Service/RememberTheMilk/Note.php';

/**
 * @see Zend_Service_RememberTheMilk_NoteList
 */
require_once 'Zend/Service/RememberTheMilk/NoteList.php';

/**
 * @see Zend_Service_RememberTheMilk_Request
 */
require_once 'Zend/Service/RememberTheMilk/Request.php';

/**
 * @see Zend_Service_RememberTheMilk_Settings
 */
require_once 'Zend/Service/RememberTheMilk/Settings.php';

/**
 * @see Zend_Service_RememberTheMilk_Task
 */
require_once 'Zend/Service/RememberTheMilk/Task.php';

/**
 * @see Zend_Service_RememberTheMilk_TaskList
 */
require_once 'Zend/Service/RememberTheMilk/TaskList.php';

/**
 * @see Zend_Service_RememberTheMilk_TaskSeries
 */
require_once 'Zend/Service/RememberTheMilk/TaskSeries.php';

/**
 * @see Zend_Service_RememberTheMilk_TaskSeriesList
 */
require_once 'Zend/Service/RememberTheMilk/TaskSeriesList.php';

/**
 * @see Zend_Service_RememberTheMilk_Time
 */
require_once 'Zend/Service/RememberTheMilk/Time.php';

/**
 * @see Zend_Service_RememberTheMilk_Timezone
 */
require_once 'Zend/Service/RememberTheMilk/Timezone.php';

/**
 * @see Zend_Service_RememberTheMilk_TimezoneList
 */
require_once 'Zend/Service/RememberTheMilk/TimezoneList.php';

/**
 * @see Zend_Service_RememberTheMilk_Token
 */
require_once 'Zend/Service/RememberTheMilk/Token.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage RememberTheMilk
 * @copyright  Copyright (c) 2005-2007 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_RememberTheMilk
{
    /**
     * Identifier for read permissions
     *
     * @const string
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    const PERMS_READ = 'read';

    /**
     * Identifier for write permissions
     *
     * @const string
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    const PERMS_WRITE = 'write';

    /**
     * Identifier for delete permissions
     *
     * @const string
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    const PERMS_DELETE = 'delete';

    /**
     * Identifier for moving task priority up
     *
     * @const string
     * @see http://www.rememberthemilk.com/services/api/rtm.tasks.movePriority.rtm
     */
    const PRIORITY_UP = 'up';

    /**
     * Identifier for moving task priority down
     *
     * @const string
     * @see http://www.rememberthemilk.com/services/api/rtm.tasks.movePriority.rtm
     */
    const PRIORITY_DOWN = 'down';

    /**
     * Base URI to which API methods and parameters will be appended
     *
     * @var string
     */
    protected static $_baseUri = 'http://api.rememberthemilk.com';

    /**
     * API key used to make requests
     *
     * @var string
     * @see http://www.rememberthemilk.com/services/api/keys.rtm
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    protected $_apiKey;

    /**
     * Shared secret used to sign API requests
     *
     * @var string
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    protected $_sharedSecret;

    /**
     * Authentication token
     *
     * @var Zend_Service_RememberTheMilk_Token
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     */
    protected $_token;

    /**
     * Timeline identifier
     *
     * @var int
     * @see http://www.rememberthemilk.com/services/api/timelines.rtm
     */
    protected $_timeline;

    /**
     * Zend_Service_Rest object
     *
     * @var Zend_Service_Rest
     */
    protected $_rest;

    /**
     * Minimum amount of time in seconds that should elapse between API
     * requests
     *
     * @var int
     */
    protected $_throttle;

    /**
     * Transmission time of the last API request, used for anti-throttling
     *
     * @var int
     */
    protected $_lastRequest;

    /**
     * List of undoable transactions, used as a stack
     *
     * @var array
     */
    protected $_transactions;

    /**
     * Constructs a new Remember The Milk REST API Client and
     *
     * @param string $apiKey API key
     * @param string $sharedSecret Shared secret used to sign requests
     * @return void
     */
    public function __construct($apiKey, $sharedSecret)
    {
        $this->_apiKey = $apiKey;
        $this->_sharedSecret = $sharedSecret;
        $this->_throttle = 2;
        $this->_lastRequest = 0;
        $this->_transactions = array();

        /**
         * @see Zend_Service_Rest
         */
        require_once 'Zend/Rest/Client.php';
        $this->_rest = new Zend_Rest_Client(self::$_baseUri);
    }

    /**
     * Appends a signature to a set of request parameters.
     *
     * @param array $params Associative array of parameters
     */
    protected function _sign(&$params)
    {
        ksort($params);
        $api_sig = $this->_sharedSecret;
        foreach ($params as $key => $value) {
            $api_sig .= $key . $value;
        }
        $params['api_sig'] = md5($api_sig);
    }

    /**
     * Executes an API request and returns the response.
     *
     * @param Zend_Service_RememberTheMilk_Request $request
     * @throws Zend_Service_Exception
     * @return array Associative array containing the response data
     */
    protected function _request($request)
    {
        $params = $request->getParameters();

        $params['api_key'] = $this->_apiKey;
        $params['format'] = 'json';
        $params['method'] = $request->getMethod();

        if ($request->requiresTimeline()) {
            if ($this->_timeline == null) {
                $timeline = new Zend_Service_RememberTheMilk_Request();
                $timeline->setMethod('rtm.timelines.create');
                $timeline->useTimeline(false);
                $response = $this->_request($timeline);
                $this->_timeline = $response->timeline;
            }
            $params['timeline'] = $this->_timeline;
        }

        if ($request->requiresAuth()) {
            if (!$this->_token instanceof Zend_Service_RememberTheMilk_Token) {
                throw new Zend_Service_Exception('Authentication token not set');
            }
            $params['auth_token'] = $this->_token->getToken();
        }

        if (strpos($params['method'], 'rtm.time.') !== 0) {
            $this->_sign($params);
        }

        $time = time();
        $elapsed = $time - $this->_lastRequest;
        if ($elapsed < $this->_throttle) {
            sleep($this->_throttle - $elapsed);
        }
        $this->_lastRequest = $time;

        /**
         * @see Zend_Service_Exception
         */
        require_once 'Zend/Service/Exception.php';

        $response = $this->_rest->restGet('/services/rest/', $params);
        if ($response->isSuccessful()) {
            $body = $response->getBody();
            if ($body === null) {
                throw new Zend_Service_Exception('Service appears to be unavailable');
            }
            $body = Zend_Json::decode($body, Zend_Json::TYPE_OBJECT);
            $body = $body->rsp;
            if ($body->stat == 'fail') {
                throw new Zend_Service_Exception(
                    $body->err->msg,
                    $body->err->code
                );
            } else if (isset($body->transaction)
                && isset($body->transaction->undoable)
                && $body->transaction->undoable == '1') {
                $this->_transactions[] = $body->transaction->id;
            }

            return $body;
        }

        throw new Zend_Service_Exception('HTTP ' . $response->getStatus());
    }

    /**
     * Sets the amount of time that should elapse between requests in order
     * to prevent throttling of the API server.
     *
     * @param int $time Amount of time, in seconds (minimum 1)
     */
    public function setThrottleTime($time)
    {
        $this->_throttle = max($time, 1);
    }

    /**
     * Returns a URL to allow an application user to authenticate their
     * identity.
     *
     * @param string $perms Permission level to assign
     * @param string $frob Frob, for use by desktop applications (optional)
     * @see Zend_Service_RememberTheMilk::PERMS_READ
     * @see Zend_Service_RememberTheMilk::PERMS_WRITE
     * @see Zend_Service_RememberTheMilk::PERMS_DELETE
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     * @return string
     */
    public function getAuthUrl($perms, $frob = null)
    {
        $params = array(
            'api_key' => $this->_apiKey,
            'perms' => $perms
        );

        if ($frob !== null) {
            $params['frob'] = $frob;
        }

        $this->_sign($params);

        $url = 'http://www.rememberthemilk.com/services/auth/?';
        $url .= http_build_query($params);
        return $url;
    }

    /**
     * Returns a frob to be used during authentication.
     *
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.auth.getFrob.rtm
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     * @throws Zend_Service_Exception
     * @return string
     */
    public function getFrob()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.auth.getFrob');
        $request->useAuth(false);
        $request->useTimeline(false);

        $response = $this->_request($request);
        return $response->frob;
    }

    /**
     * Obtains the auth token for the given frob, sets it as the auth token
     * to use for subsequent operations, and returns it.
     *
     * @param string $frob Frob to check
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.auth.getToken.rtm
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_Token
     */
    public function getToken($frob)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.auth.getToken');
        $request->useAuth(false);
        $request->useTimeline(false);
        $request->addParameter('frob', $frob);

        $response = $this->_request($request);
        $this->_token = new Zend_Service_RememberTheMilk_Token($response);
        return $this->_token;
    }

    /**
     * Sets the auth token to use for subsequent operations, mainly meant for
     * testing purposes.
     *
     * @param Zend_Service_RememberTheMilk_Token $token Token to use
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function setToken(Zend_Service_RememberTheMilk_Token $token)
    {
        $this->_token = $token;
    }

    /**
     * Returns the credentials attached to an authentication token.
     *
     * @param string $token Token to check
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.auth.checkToken.rtm
     * @see http://www.rememberthemilk.com/services/api/authentication.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_Token
     */
    public function checkToken($token)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.auth.checkToken');
        $request->useAuth(false);
        $request->useTimeline(false);
        $request->addParameter('auth_token', $token);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Token($response);
    }

    /**
     * Adds a new contact.
     *
     * @param string $contact Username or e-mail address of a registered
     *                        Remember The Milk user to add
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.contacts.add.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_Contact
     */
    public function addContact($contact)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.contacts.add');
        $request->addParameter('contact', $contact);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Contact($response->contact);
    }

    /**
     * Retrieves a list of contacts.
     *
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.contacts.getList.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_ContactList
     */
    public function getContactList()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.contacts.getList');
        $request->useTimeline(false);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_ContactList($response);
    }

    /**
     * Deletes a contact.
     *
     * @param int $id Identifier for the contact to delete
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.contacts.delete.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function deleteContact($id)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.contacts.delete');
        $request->addParameter('contact_id', $id);
        $this->_request($request);
    }

    /**
     * Creates a new group.
     *
     * @param string $group Name of the group to create
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.groups.add.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_Group
     */
    public function addGroup($group)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.groups.add');
        $request->addParameter('group', $group);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Group($response->group);
    }

    /**
     * Retrieves a list of groups.
     *
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.groups.getList.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_GroupList
     */
    public function getGroupList()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.groups.getList');
        $request->useTimeline(false);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_GroupList($response);
    }

    /**
     * Deletes a group.
     *
     * @param int $id Identifier for the group to delete
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.groups.delete.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function deleteGroup($id)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.groups.delete');
        $request->addParameter('group_id', $id);
        $this->_request($request);
    }

    /**
     * Adds a contact to a group.
     *
     * @param int $group Identifier for the group
     * @param int $contact Identifier for the contact
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.groups.addContact.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function addContactToGroup($group, $contact)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.groups.addContact');
        $request->addParameter('group_id', $group);
        $request->addParameter('contact_id', $contact);
        $this->_request($request);
    }

    /**
     * Removes a contact from a group.
     *
     * @param int $group Identifier for the group
     * @param int $contact Identifier for the contact
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.groups.removeContact.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function removeContactFromGroup($group, $contact)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.groups.removeContact');
        $request->addParameter('group_id', $group);
        $request->addParameter('contact_id', $contact);
        $this->_request($request);
    }

    /**
     * Creates a new list.
     *
     * @param string $name Desired list name (cannot be Inbox or Sent)
     * @param string $filter Criteria if list should be a Smart List
     *                       (optional)
     * @see http://www.rememberthemilk.com/help/answers/search/advanced.rtm
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.add.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_List
     */
    public function addList($name, $filter = null)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.add');
        $request->addParameter('name', $name);
        if ($filter !== null) {
            $request->addParameter('filter', $filter);
        }

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_List($response->list);
    }

    /**
     * Sets the default list.
     *
     * @param int $id Identifier for the list
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.setDefaultList.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function setDefaultList($id)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.setDefaultList');
        $request->addParameter('list_id', $id);
        $this->_request($request);
    }

    /**
     * Returns a list of lists.
     *
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.getList.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_ListList
     */
    public function getListList()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.getList');
        $request->useTimeline(false);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_ListList($response);
    }

    /**
     * Deletes a list.
     *
     * @param int $id Identifier for the list
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.delete.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_List
     */
    public function deleteList($id)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.delete');
        $request->addParameter('list_id', $id);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_List($response->list);
    }

    /**
     * Renames a list.
     *
     * @param int $id Identifier for the list
     * @param string $name New name for the list (cannot be Inbox or Sent)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.setName.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_List
     */
    public function renameList($id, $name)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.setName');
        $request->addParameter('list_id', $id);
        $request->addParameter('name', $name);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_List($response->list);
    }

    /**
     * Archives a list.
     *
     * @param int $id Identifier for the list
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.archive.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_List
     */
    public function archiveList($id)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.archive');
        $request->addParameter('list_id', $id);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_List($response->list);
    }

    /**
     * Unarchives a list.
     *
     * @param int $id Identifier for the list
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.lists.unarchive.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_List
     */
    public function unarchiveList($id)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.lists.unarchive');
        $request->addParameter('list_id', $id);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_List($response->list);
    }

    /**
     * Adds a task to a list.
     *
     * @param string $name Name for the task
     * @param int $list Identifier for the list to contain the task, or NULL
     *                  to add the task to the Inbox (default is NULL)
     * @param bool $parse TRUE to have the task name analyzed for a potential
     *                    due date, FALSE otherwise (default is FALSE)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.add.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function addTask($name, $list = null, $parse = false)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.add');
        $request->addParameter('name', $name);
        $request->addParameter('parse', $parse ? '1' : '0');

        if ($list !== null) {
            $request->addParameter('list_id', $list);
        }

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Adds tags to a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param mixed $tags Array or comma-delimited string of tags
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.addTags.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function addTaskTags($list, $series, $task, $tags)
    {
        if (is_array($tags)) {
            $tags = implode(',', $tags);
        }

        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.addTags');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('tags', $tags);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Marks a task complete.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.complete.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function completeTask()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.complete');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Marks a task as deleted.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.delete.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function deleteTask($list, $series, $task)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.delete');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Retrieves a list of tasks. If $list, $filter, and $lastSync are not
     * specified, all tasks are returned.
     *
     * @param int $list If specified, returned tasks will be restricted to
     *                  those contained in the list with the specified
     *                  identifier
     * @param string $filter If specified, returned tasks will be restricted
     *                       to those matching the specified criteria
     * @param string $lastSync An ISO 8601 formatted time value. If speciifed,
     *                         returned tasks will be restricted to those
     *                         modified since that time
     * @see http://www.rememberthemilk.com/help/answers/search/advanced.rtm
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.getList.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeriesList
     */
    public function getTaskList($list = null, $filter = null, $lastSync = null)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.getList');
        $request->useTimeline(false);

        if ($list !== null) {
            $request->addParameter('list_id', $list);
        }

        if ($filter !== null) {
            $request->addParameter('filter', $filter);
        }

        if ($lastSync !== null) {
            $request->addParameter('last_sync', $lastSync);
        }

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeriesList($response->tasks);
    }

    /**
     * Moves the priority of a task up or down.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $direction Direction to move priority (use PRIORITY_*
     *                          constants)
     * @see Zend_Service_RememberTheMilk::PRIORITY_UP
     * @see Zend_Service_RememberTheMilk::PRIORITY_DOWN
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.movePriority.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function moveTaskPriority($list, $series, $task, $direction)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.movePriority');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('direction', $direction);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Moves a task between lists.
     *
     * @param int $fromList Identifier for the list containing the task
     * @param int $toList Identifier for the list to receive the task
     * @param int $series Identifier for the task series containing the task
     * @param int $task Identifier for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.moveTo.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function moveTask($fromList, $toList, $series, $task)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.moveTo');
        $request->addParameter('from_list_id', $fromList);
        $request->addParameter('to_list_id', $toList);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Postpones a task. If the task has no due date or is overdue, its due
     * date is set to today. Otherwise, the task due date is advanced a day.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.postpone.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function postponeTask()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.postpone');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Removes tags from a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param mixed $tags Array or comma-delimited string of tags
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.removeTags.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function removeTaskTags($list, $series, $task)
    {
        if (is_array($tags)) {
            $tags = implode(',', $tags);
        }

        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.removeTags');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('tags', $tags);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets the due date of a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $due Due date for the task, in ISO 8601 format, or NULL
     *                    to remove any existing due date (optional)
     * @param bool $hasDueTime TRUE if $due has a time, FALSE otherwise
     *                         (optional)
     * @param bool $parse TRUE to have $due be parsed with rtm.time.parse,
     *                    FALSE otherwise
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setDueDate.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskDueDate($list, $series, $task, $due = null,
        $hasDueTime = false, $parse = false)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setDueDate');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('due', ($due === null) ? '' : $due);
        $request->addParameter('has_due_date', $hasDueTime ? '1' : '0');
        $request->addParameter('parse', $parse ? '1' : '0');

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets a time estimate for a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $estimate Time estimate for the task with units of days,
     *                         hours, or minutes, or NULL to unset any
     *                         existing estimate for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setEstimate.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskEstimate($list, $series, $task, $estimate = null)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setEstimate');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('estimate', ($estimate === null) ? '' : $estimate);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets a location for a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param int $location Identifier for the location
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setLocation.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskLocation($list, $series, $task, $location)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setLocation');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('location_id', $location);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Renames a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $name Name for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setName.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskName($list, $series, $task, $name)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setName');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('name', $name);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets the priority of a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param int $priority 1, 2, or 3, or any other value to unset any
     *                      existing priority for the task (optional)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setPriority.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskPriority($list, $series, $task, $priority = null)
    {
        if (!in_array($priority, array(1, 2, 3))) {
            $priority = 'N';
        }

        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setPriority');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('priority', $priority);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets a recurrence pattern for a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $repeat Recurrence pattern, or NULL to unset any existing
     *                       recurrence pattern (optional)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setRecurrence.rtm
     * @see http://www.rememberthemilk.com/help/answers/basics/repeatformat.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskRecurrence($list, $series, $task, $repeat = null)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setRecurrence');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('repeat', $repeat);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets tags for a task. Any previous task tags will be overwritten.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param mixed $tags Array or comma-delimited string of tags (optional)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setTags.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskTags($list, $series, $task, $tags = '')
    {
        if (is_array($tags)) {
            $tags = implode(',', $tags);
        }

        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setTags');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('tags', $tags);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Sets a URL for a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $url URL associated with the task. Valid protocols are
     *                    http, https, ftp and file. If left empty, any
     *                    existing URL will be unset. (optional)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.setURL.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function setTaskUrl($list, $series, $task, $url = '')
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.setURL');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('url', $url);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Marks a task incomplete.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.uncomplete.rtm
     * @return Zend_Service_RememberTheMilk_TaskSeries
     */
    public function uncompleteTask()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.uncomplete');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TaskSeries($response);
    }

    /**
     * Adds a new note to a task.
     *
     * @param int $list Identifier for the list containing the task
     * @param int $series Identifier for the task series including the task
     * @param int $task Identifier for the task
     * @param string $title Title of the note
     * @param string $text Text of the note
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.notes.add.rtm
     * @return Zend_Service_RememberTheMik_Note
     */
    public function addNote($list, $series, $task, $title, $text)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.notes.add');
        $request->addParameter('list_id', $list);
        $request->addParameter('taskseries_id', $series);
        $request->addParameter('task_id', $task);
        $request->addParameter('note_title', $title);
        $request->addParameter('note_text', $text);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Note($response);
    }

    /**
     * Modifies a note.
     *
     * @param int $note Identifier for the note
     * @param string $title Title of the note
     * @param string $text Text of the note
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.notes.edit.rtm
     * @return Zend_Service_RememberTheMilk_Note
     */
    public function editNote($note, $title, $text)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.notes.edit');
        $request->addParameter('note_id', $note);
        $request->addParameter('note_title', $title);
        $request->addParameter('note_text', $text);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Note($response);
    }

    /**
     * Deletes a note.
     *
     * @param int $note Identifier for the note
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.tasks.notes.delete.rtm
     * @return void
     */
    public function deleteNote($note)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.tasks.notes.delete');
        $request->addParameter('note_id', $note);
        $this->_request($request);
    }

    /**
     * Returns a list of available Remember The Milk API methods.
     *
     * @throws Zend_Service_Exception
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.reflection.getMethods.rtm
     * @return array Strings containing method names
     */
    public function getMethods()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.reflection.getMethods');
        $request->useAuth(false);
        $request->useTimeline(false);

        $response = $this->_request($request);
        return $response->methods->method;
    }

    /**
     * Returns information for a given Remember The Milk API method.
     *
     * @param string $name Name of the method
     * @throws Zend_Service_Exception
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.reflection.getMethodInfo.rtm
     * @return Zend_Service_RememberTheMilk_Method
     */
    public function getMethodInfo($name)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.reflection.getMethodInfo');
        $request->useAuth(false);
        $request->useTimeline(false);
        $request->addParameter('method_name', $name);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Method($response);
    }

    /**
     * Retrieves a list of user settings.
     *
     * @throws Zend_Service_Exception
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.settings.getList.rtm
     * @return Zend_Service_RememberTheMilk_Settings
     */
    public function getSettings()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.settings.getList');
        $request->useTimeline(false);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Settings($response);
    }

    /**
     * Returns the specified time in the desired timezone.
     *
     * @param string $toTimezone Target timezone
     * @param string $fromTimezone Originating timezone, defaults to UTC
     *                            (optional)
     * @param string $time Time to convert in ISO 8601, defaults to now
     *                     (optional)
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.time.convert.rtm
     * @throws Zend_Service_Exception
     * @return string Converted time in ISO 8601
     */
    public function convertTime($toTimezone, $fromTimezone = null,
        $time = null)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.settings.getList');
        $request->useAuth(false);
        $request->useTimeline(false);
        $request->addParameter('to_timezone', $toTimezone);

        if ($fromTimezone !== null) {
            $request->addParameter('from_timezone', $fromTimezone);
        }

        if ($time !== null) {
            $request->addParameter('time', $time);
        }

        $response = $this->_request($request);
        return $response->time->time;
    }

    /**
     * Returns the time, in UTC, for the parsed input.
     *
     * @param string $text Text to parse
     * @param string $timezone Timezone with respect to which text should be
     *                         parsed, defaults to UTC (optional)
     * @param bool $dateFormat TRUE to use American format (02/14/2006), FALSE
     *                         to use European format (14/02/2006) (optional)
     * @return string Parsed time in ISO 8601
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.time.parse.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_Time
     */
    public function parseTime($text, $timezone = null, $dateFormat = false)
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.time.parse');
        $request->useAuth(false);
        $request->useTimeline(false);
        $request->addParameter('text', $text);
        $request->addParameter('dateformat', $dateFormat ? '1' : '0');

        if ($timezone !== null) {
            $request->addParameter('timezone', $timezone);
        }

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_Time($response);
    }

    /**
     * Retrieves a list of timezones.
     *
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.timezones.getList.rtm
     * @throws Zend_Service_Exception
     * @return Zend_Service_RememberTheMilk_TimezoneList
     */
    public function getTimezoneList()
    {
        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.timezones.getList');
        $request->useAuth(false);
        $request->useTimeline(false);

        $response = $this->_request($request);
        return new Zend_Service_RememberTheMilk_TimezoneList($response);
    }

    /**
     * Reverts the affects of the last undoable action.
     *
     * @see http://www.rememberthemilk.com/services/api/methods/rtm.transactions.undo.rtm
     * @throws Zend_Service_Exception
     * @return void
     */
    public function undo()
    {
        $transaction = array_pop($this->_transactions);

        $request = new Zend_Service_RememberTheMilk_Request();
        $request->setMethod('rtm.transactions.undo');
        $request->addParameter('transaction_id', $transaction);
        $this->_request($request);
    }
}
