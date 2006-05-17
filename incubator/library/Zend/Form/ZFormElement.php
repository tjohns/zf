<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to version 1.0 of the Zend Framework
 * license, that is bundled with this package in the file LICENSE, and
 * is available through the world-wide-web at the following URL:
 * http://www.zend.com/license/framework/1_0.txt. If you did not receive
 * a copy of the Zend Framework license and are unable to obtain it
 * through the world-wide-web, please send a note to license@zend.com
 * so we can mail you a copy immediately.
 *
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */


/**
 * The ZFormElements needs access to the ZRequest object for processing
 */
require_once('ZRequest/ZRequest.php');

/**
 * Events are needed for sourcing and consuming within a form element
 */
require_once('ZForm/ZFormElementEvent.php');

/**
 * All elements are also general purpose listeners which implement
 * the ZFormElementEventListenerInterface.
 */
require_once('ZForm/ZFormElementEventListenerInterface.php');

/**
 * The javascript based behaviors assocated with the element
 */
require_once('ZForm/ZFormElementBehaviorAbstract.php');

/**
 * @package    ZForm
 * @copyright  Copyright (c) 2005-2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://www.zend.com/license/framework/1_0.txt Zend Framework License version 1.0
 */
abstract class ZFormElement implements ZFormElementEventListenerInterface {

    /**
     * Elements are uniquely addressable via the path generated
     * by walking the parent chain and seperating each name segment
     * with this string @see _computeIDPath()
     * @var string
     */
    const PATH_SEPARATOR = '-';

    /**
     * Contains the list of the server side events to be fired
     * this element
     * @var array
     */
    protected $_events = null;

    /**
     * Contains the list of client event hooks registered on
     * this element
     * @var array
     */
    protected $_eventListeners = null;

    /**
     * Contains the list of client/server side validators registered on
     * this element
     * @var array
     */
    protected $_validators = null;

    protected $_errors = null;

    /**
     * Contains the list of client side behaviors registered on
     * this element
     * @var array
     */
    protected $_behaviors = null;

    /**
     * Contains the list of children associated with the element
     *
     * @var array
     */
    protected $_childNodes 	 = null;

    /**
     * Contains a reference to the parent of the element
     *
     * @var ZFormElement
     */
    protected $_parentNode 	 = null;

    /**
     * Associative array containing the attributes associated with
     * the element
     *
     * @var array;
     */
    protected $_attributes 	 = null;

    /**
     * The full path name of the element. Elements must be uniquely
     * identified within the context of their parent. The _idPath is construct
     * by combining the parent references and the ID of the element into
     * a path separated by PATH_SEPARATOR.
     *
     * @var string
     */
    protected $_idPath		 = null;

    /**
     * Boolean property specifing weather to allow events to fire or not.
     *
     * @var boolean
     */
    protected $_eatEvents	 = false;

    /**
     * Boolean property used to signify if processing of the element should
     * continue
     *
     * @var toolean
     */
    protected $_process	         = false;


    /**
     * Class constructor. Initializes internal instance variables. $id is
     * optional however it is recommended the each element by name. If an
     * $id is not passed a unique one will be generated. Additionally, if
     * a $parentNode is passed upon exit from the constructor the new
     * instance is appended to the child list.
     *
     * @param string $id Optional unique name within the context of the
     * $parentNode if not specified a unique one will be generated.
     *
     * @param ZFormElement $parentNode The parent element of the element
     * being created
     *
     * @throws ZFormElementException When the ID collides within the
     * parent name space.
     *
     * @return void
     */
    public function __construct($id = null, $parentNode = null)
    {
    	$this->_childNodes = array();
    	$this->_attributes = array();
    	$this->_parentNode = $parentNode;

    	if ($parentNode) {
    	    $parentNode->appendChild($this);
    	}

    	$this->setID($id ? $id : uniqid('id'));
    }


    /**
     * Compute, if necessary, and return the fully qualified path of
     * the element within the containment structured defined via
     * the parentnode
     *
     * @return string
     */
    public function getIDPath()
    {
    	if ($this->_idPath == null) {
    	    $this->_computeIDPath();
    	}

    	return($this->_idPath);
    }


    /**
     * Return simple ID of the element
     *
     * @return string
     */
    public function getID()
    {
    	return($this->getAttribute("ID"));
    }


    /**
     * Searchs the parent chain defined by the _parentNode reference
     * until the top of the tree is reached. If the element has no
     * parents then the element is the 'root'
     *
     * @return ZFormElement
     */
    public function getRoot()
    {
    	if ($this->_parentNode === null) {
    	    return $this;
    	}

    	return($this->_parentNode->getRoot());
    }


    /**
     * Return the list (array) of children contained by the element
     *
     * @return array
     */
    public function getChildNodes()
    {
    	return $this->_childNodes;
    }


    /**
     * Sets the ID of the element. The ID is checked for validitiy within
     * the parent naming space. NOTE: IDs must be unique within the parent
     * name space. If the name collides a ZFormElementException is thrown
     * Additionally, the id path is recalcuated when the ID is changed
     * @see _computeIDPath()
     * @param string $id
     * @return void
     */
    public function setID($id)
    {
    	$this->_isLegalName($id);
    	$this->setAttribute("ID", $id);
    	$this->_computeIDPath();
    }


    /**
     * Returns the value of the $name attribute associated with the element
     *
     * @param string $name
     * @return string 
     */
    public function getAttribute($name)
    {
    	return $this->_attributes[$name];
    }


    /**
     * Set the value of the $name attribute within the element
     *
     * @param string $name
     * @return void
     */
    public function setAttribute($name, $value)
    {
    	$this->_attributes[$name] = $value;
    }


    /**
     * Return the associative array of attribute maintained by the element.
     *
     * @return array
     */
    public function getAttributes()
    {
    	return($this->_attributes);
    }


    /**
     * Return the parent node reference maintained by the element
     *
     * @return ZFormElement
     */
    public function getParentNode()
    {
    	return($this->_parentNode);
    }


    /**
     * Changes the parent node associated with the element to the
     * new $parent specified in the parameter.
     *
     * @param ZFormElement $parent
     */
    protected function setParentNode(ZFormElement $parent)
    {
    	if ($this->_parentNode &&  $this->_parentNode != $parent) {
    	    $parent->removeChild($this);
    	}

    	$this->_parentNode = $parent;
    	$this->_computeIDPath();
    }


    /**
     * Does the element contain children?
     *
     * @return boolean
     */
    public function hasChildNodes()
    {
    	return $this->_childNodes && count($this->_childNodes) > 0;
    }


    /**
     * Adds the $child to the list of children associated with the element
     * The parent of the $child is set to the element
     *
     * @param ZFormElement $child
     * @return ZFormElement The newly added $child (the one passed in).
     */
    public function appendChild(ZFormElement $child)
    {
    	// Set the parent of the new $child
    	$child->setParentNode($this);

    	// Remove child just in case
    	$this->removeChild($child);
    	$this->_childNodes[] = $child;

    	return($child);
    }


    /**
     * Remove the given $child from the list (array) of children
     * associated with the element
     *
     * @param ZFormElement $child
     * @return boolean true if removed, false otherwise
     */
    public function removeChild(ZFormElement $child)
    {
        // @todo can be made more efficient(?)
    	$index = array_search($child, $this->_childNodes);

    	if ($index) {
    	    unset($this->_childNodes[$index]);
    	    $child->setParentNode(null);
    	    return(true);
    	}

    	return(false);
    }


    /**
     * Abstract method used to retrive the value associated with the
     * element. Each element maintains an abstract value which can be
     * changed which triggers a value change event. This method is
     * abstract so subclasses can define the contents of that value
     *
     * @return mixed
     */
    abstract public function getValue();


    /**
     * Implementation of the setValue which ONLY generates a
     * ONVALUECHANGE event if the new $value is different from the current
     * value. This means the subclasses MUST call this implementation and
     * store the value.
     *
     * @param mixed $value
     *
     */
    public function setValue($value)
    {
    	$current = $this->getValue();

    	if ($value != $current) {
    	    $this->fireEvent(new ZFormElementEvent(ZFormElementEvent::ONVALUECHANGE,
                        						   $this,
                        						   array('old' => $current,
                        							     'new' => $value)));
    	}
    }


    /**
     * This method (process) does much of the work associated with the element.
     * In general, ZFormElement enables the processing of input data(request and
     * persistent) to be loaded/validated and committed to the application
     * model. ZFormElements can be wired-up to each other so that events can
     * be triggered in one element and consumed in another. Processing of an
     * element and its children does not render the element tree, it simply
     * gives the controls a chance to interact with each other and the
     * controller of the application. Rendering is defined by subclasses
     * and/or templating engines.
     * Processing consists of:
     * 1) If the element is persistent restore the saved state @see
     *    restoreState()
     * 2) Allow events to be process after restoring the state
     * 3) Ask the elements to load state from the request parameter.
     * 4) Allow events to be process after loading data from request
     * 5) Validate the data loaded
     * 6) Allow events to be process after loading data from request
     * 7) Invoke the application to allow the elements to communicate with the
     *    model
     * 8) One last chance to process events
     * NOTE: Events are queued during steps 1, 3, 5, 7 until the whole child
     * tree has been visited.
     *
     */
    public function process()
    {
    	do {
    	    $this->_process = true;
    	    if (!$this->restoreState() || !$this->_process) {
        		break;
    	    }

    	    if (!$this->_processEvents() || !$this->_process) {
        		break;
    	    }

    	    if (!$this->loadRequestData() || !$this->_process) {
        		break;
    	    }

    	    if (!$this->_processEvents() || !$this->_process) {
        		break;
    	    }

    	    if (!$this->validate() || !$this->_process) {
        		break;
    	    }

    	    if (!$this->_processEvents() || !$this->_process) {
        		break;
    	    }

    	    if (!$this->invokeApplication() || !$this->_process) {
        		break;
    	    }

    	    $this->_processEvents();
    	    break;
    	} while (true);

    }


    /**
     * Sets the processing flag, at any point during the process loop an
     * element can terminate futher process by setting this flag to false
     *
     * @param boolean
     * @return void
     */
    public function setProcess($process)
    {
    	$this->_process = ($process ? true : false);
    }


    /**
     * Returns the status of the processing flag
     *
     * @return boolean true of the element is currently in a processing
     * loop, false otherwise
     */
    public function getIsProcessing()
    {
	   return $this->_process;
    }


    /**
     * Returns the error message originated by the $child
     * 
     * @param ZFormElement $child 
     * @return string The error message if the child generated
     * an error message or ''
     */
    public function getErrorMessage($child)
    {
    	if ($this->_errors && ($validator = $this->_errors[$child->getIDPath()])) {
    	    return $validator->getErrorMessage();
    	}

    	return '';
    }


    /**
     * Returns the array of validation errors associated with the element.
     * The array is an assoc array whose key is the ID path of the child
     * element and the value which is an array of validators that reported
     * the error
     * @return array
     */
    public function getValidationErrors()
    {
    	return $this->_errors;
    }


    /**
     * Validates the element and all its children by applying the validators
     * associated with the element and then recursively invoking validate on the
     * children.
     *
     * @return array an assoc array whose key is the ID path of the child
     * element and the value which is an array of validators the reported
     * the error.
     */
    public function validate()
    {
    	$this->_errors = array();
    	$this->_performValidation($this->_errors);

    	return($this->_errors);
    }


    /**
     * Iterates over the validators associated with the elements and inokes
     * the validator on the element. Errors are collected in the $errors array
     *
     * @param array $errors 
     * @return void
     */
    protected function _performValidation(&$errors = null)
    {
    	if ($this->_validators) {
    	    foreach ($this->_validators as $validator) {
        		if ($validator instanceof ZFormElementValidator) {
        		    if (! $validator->performValidation($this)) {
            			$idPath = $this->getIDPath();
            			$curVal = $errors[$idPath];
            			if ($curVal) {
            			    if (is_array($curVal)) {
                				$curVal[] = $validator;
            			    } else {
                				$curVal = array($curVal);
            			    }

            			    $errors[$idPath] = $curVal;
            			} else {
            			    $errors[$idPath] = $validator;
            			}
        		    }
        		}
    	    }
    	}

    	if ($this->hasChildNodes()) {
    	    foreach ($this->_childNodes as $child) {
        		$child->_performValidation($errors);
    	    }
    	}

    }


    /**
     * Abstract implementation of invoking the application component associated
     * with the element. This method simply recurs over the children of the
     * element calling the invokeApplication of each child. Subclasses should
     * call this method (e.g. parent::invokeApplication) to invoke application
     * logic that is associated with its children
     *
     */
    public function invokeApplication()
    {
    	if ($this->hasChildNodes()) {
    	    foreach ($this->_childNodes as $child) {
        		$child->invokeApplication();
    	    }
    	}
    }


    /**
     * Abstract implementation that interates of the children of the element
     * invoking loadRequestData. During this phase of the processing cycle
     * elements should retrieve input data from the ZRequest object
     *
     * @return boolean true if all children were processed, false otherwise
     */
    public function loadRequestData()
    {
    	if ($this->hasChildNodes()) {
    	    foreach ($this->_childNodes as $child) {
        		if (! $child->loadRequestData()) {
        		    return(false);
        		}
    	    }
    	}

    	return true;
    }


    /**
     * An element can persist itself between requests (@see persist()), if so
     * it is during this stage of the processing cycle that elements restore
     * their state. Persistent data is retrieved from the session object
     * under the elements idPath.
     *
     * @return boolean
     */
    public function restoreState()
    {
    	$mementos = ZRequest::session($this->getIDPath());

    	if ($mementos) {
    	    $this->_restoreMementos($this, $mementos);
    	}

    	return true;
    }


    /**
     * Persists the elements to the session for subsequent restoration and
     * presentation to the user (this is useful with multi-page forms).
     * Persisting an element consists of asking itself and each of its
     * children for a memento which is place into the session object.
     * Upon restoreState the memento is passed to the object for
     * reconstitution.
     */
    public function persist()
    {
    	$mementos = array();
    	$this->_gatherMementos($this, $mementos);

    	if (count($mementos) > 0) {
    	    ZRequest::setSession($this->getIDPath(), $mementos);
    	}

    	return($mementos);
    }


    /**
     * Fire the given event into the element tree. Elements are not
     * delivered right away, they are queued for delivery at the
     * appropriate time of the process cycle (@see process())
     */
    public function fireEvent(ZFormElementEvent $event)
    {
    	if ($this->_eatEvents) {
    	    return;
    	}

    	if (! $this->_eventListeners) {
    	    $this->_eventListeners = array();
    	}

    	$listeners = $this->_eventListeners[$event->getType()];
    	if ($listeners) {
    	    $this->_events[] = $event;
    	}
    }


    /**
     * Remove the event listener associated with the given event type
     * from the list of listeners. If $listener is not specified
     * all events of the given type will be removed from the listeners
     *
     * @param string $type (@see ZFormElementEvent for event types)
     * @param ZFormElementEventListenerInterface $listener
     */
    public function removeEventListener($type, $listener = '')
    {
    	if (! $this->_eventListeners) {
    	    return;
    	}

    	if ($listener === '') {
    	    unset($this->_eventListeners[$type]);
        } else if (isset($this->_eventListeners[$type])) {
    	    $index = array_search($this->_eventListeners[$type]);
    	    if ($index) {
       		unset($this->_eventListeners[$type][$index]);
    	    }
    	}
    }


    /**
     * Adds an event listener of the element for the given $type
     *
     * @param string $type (@see ZFormElementEvent for event types)
     * @param mixed $listener
     */
    public function addEventListener($type, $listener)
    {
    	if (! $this->_eventListeners) {
    	    $this->_eventListeners = array();
    	}

    	$this->removeEventListener($type, $listener);
    	if (! $this->_eventListeners[$type]) {
    	    $this->_eventListeners[$type] = array();
    	}

    	$this->_eventListeners[$type][] = $listener;
    }


    /**
     * Turns of processing events, that is events delivered to the
     * fireEvent method (@see fireEvent()) are not process, they are
     * ignored
     *
     * @param boolean $allow true - allow events, otherwise ignore them
     */
    public function setAllowEvents($allow)
    {
    	$this->_eatEvents =  ! $allow;
    }


    /**
     * Simple getter for the event listeners associated with the web element
     *
     * @return array All the events and listeners associated with the web
     * elements
     * Format: array(n) { ["submit"] => array(m) { [0] => eventlistener,
     *                                             [m] = eventlistener},
     *                    ["click"]  => array(m) ....}
     *
     */
    public function getEventListeners($type = null)
    {
    	return ($this->_eventListeners && $type) ?
                	       $this->_eventListeners[$type] :
                	       $this->_eventListeners;
    }


    /**
     * Add the given validator to the list of validators associated with the
     * element. If the validator is already contained in the list no action is
     * taken. Elements support multiple validators on a FIFO basis. When a
     * validator fails the remaining validators (if any) are not called.
     *
     * @param ZFormElementValidator $validator
     */
    public function addValidator(ZFormElementValidator $validator)
    {
    	if (! $this->_validators) {
    	    $this->_validators = array();
    	}

    	$this->_appendToList($validator, $this->_validators);
    }


    /**
     * Removes validator from the list of validators associated with the
     * element
     *
     * @param ZFormElementValidator $validator The validator instance to remove
     * @return boolean true if removed false otherwise
     */

    public function removedValidator(ZFormElementValidator $validator)
    {
    	return $this->_removeFromList($validator, $this->_validators);
    }


    /**
     * Returns the list of validators associated with the element
     *
     * @return array
     */
    public function getValidators()
    {
    	return $this->_validators;
    }


    /**
     * Add the given behavior to the list of behaviors associated with the
     * element. If the behavior is already contained in the list no action is
     * taken. Elements support multiple behaviors on a FIFO basis. When a
     * behaviors fails the remaining behaviors (if any) are not called.
     *
     * @param ZFormElementBehavior $behavior
     */
    public function addBehavior(ZFormElementBehaviorAbstract $behavior)
    {
    	if (! $this->_behaviors) {
    	    $this->_behaviors = array();
    	}

    	$this->_appendToList($behavior, $this->_behaviors);
    }


    /**
     * Removes behavior from the list of behaviors associated with the
     * element
     *
     * @param ZFormElementBehavior $behavior The validator instance to remove
     * @return boolean true if removed false otherwise
     */
    public function removedBehavior($behavior)
    {
    	return $this->_removeFromList($behavior, $this->_behaviors);
    }


    /**
     * Returns the list (array) of behaviors associated with the element
     *
     * @return array
     */
    public function getBehaviors()
    {
    	return $this->_behaviors;
    }


    /**
     * Returns the childs whose ID is $id. If the $recursive = true, continue
     * search depth first for the child
     *
     * @param string $id
     * @param boolean true - recursively search child, false - child search
     * the children
     * @return ZFormElement If found, null otherwise
     */
    public function getElementById($id, $recursive = false)
    {
    	if ($this->hasChildNodes()) {
    	    foreach ($this->_childNodes as $child) {
        		if ($child->getID() == $id) {
        		    return($child);
        		}

        		if ($recursive) {
        		    $result = $child->getElementById($id, true);
        		    if ($result) {
            			return($result);
        		    }
        		}
    	    }
    	}
        	return(null);
    }


    /**
     * Default implementation of retriving the memento associated with the element
     * that will be used during persistent (@see persist())
     * The default implementation does not persist anything, we implement it here
     * so subclasses are not required to
     *
     * @return mixed null for the default implementation, subclasses should
     * override.
     */
    abstract public function getMemento();


    /**
     * The bookend implementation to @see getMemento(). This function is a void
     * implementation of the protocol to simplify the task of subclassing
     *
     */
    abstract public function setMemento($memento);


    /**
     * Default implementation of the ZFormElementEventListenerInterface
     * interface which simply does nothing. Intended to make subclassing
     * more easy.
     */
    public function handleEvent(ZFormElementEvent $event)
    {
    	// Void implementation eat the event
    }


    /**
     * Helper array management routines to add/remove and item if it doesn't
     * already exist to an array
     *
     * @param mixed $item
     * @param array &$list
     * @return boolean true if added, false otherwise
     */
    protected function _appendToList($item, &$list)
    {
    	if (! array_search($item, $list)) {
    	    $list[] = $item;
    	    return true;
    	}

    	return false;
    }


    /**
     * Helper array management that removes a given element for a list
     *
     * @param mixed $element
     * @param array &$list
     * @return boolean true if removed false otherwise
     */
    protected function _removeFromList($element, &$list)
    {
        // @todo can be optimized?
    	$index = array_search($element, $list);

    	if ($index) {
    	    unset($list[$index]);
    	    return true;
    	}

    	return false;
    }


    /**
     * Generates the unique ID path for the element which 
     * consists of its ancestor IDs concatenated with the element's
     * ID.
     *
     * @return string
     */
    protected function _computeIDPath()
    {
    	$result = $this->getID();

    	if ($result) {
    	    $temp = $this->_parentNode;
    	    while ($temp) {
        		$result = $temp->getID().self::PATH_SEPARATOR.$result;
        		$temp = $temp->_parentNode;
    	    }
    	    $this->_idPath = $result;
    	}

    	return $result;
    }


    /**
     * Determines if a given $id is unique within the element children 
     * names.
     *
     * @param string $id
     * @throws ZFormElementException
     * @return boolean true or throws ZFormElementException
     */
    protected function _isLegalName($id)
    {
    	if ($this->_parentNode) {
    	    $children = $this->_parentNode->_childNodes;
    	    if ($children != null) {
        		foreach ($children as $child) {
        		    if ($child->getID() == $id) {
            			throw new ZFormElementException
            			    ("Illegal ID: $id, IDs must be uniqued within parent!");
        		    }
        		}
    	    }
    	}

    	return true;
    }


    /**
     * Process the list (array) of queued events ready for delivery
     * (@see process(), @see fireEvent()).
     *
     * @return boolean true of all child events were processed
     */
    protected function _processEvents()
    {
    	if ($this->_events) {
    	    foreach ($this->_events as $event) {
        		if (! $event->fire()) {
        		    return false;
        		}
    	    }
    	    $this->_events = array();
    	}

    	if ($this->hasChildNodes()) {
    	    foreach ($this->_childNodes as $child) {
        		if (! $child->_processEvents()) {
        		    return false;
        		}
    	    }
    	}

    	return true;
    }


    /**
     * Support methods for gathering and redistributing mementos to th
     * children of the element (@see persist(), @see restoreState())
     *
     * @param ZFormElement $visiting
     * @param array $mementos
     */
    protected function _gatherMementos($visiting, &$mementos)
    {
    	$memento = $visiting->getMemento();

    	if ($memento) {
    	    $mementos[$visiting->getIDPath()] = $memento;
    	}

    	if ($visiting->hasChildNodes()) {
    	    foreach ($visiting->_childNodes as $child) {
        		$this->_gatherMementos($child, $mementos);
    	    }
    	}
    }


    /**
     * Support methods for gathering and redistributing mementos to th
     * children of the element (@see persist(), @see restoreState())
     *
     * @param ZFormElement $visiting
     * @param array $mementos
     */
    protected function _restoreMementos($visiting, &$mementos)
    {
    	$memento = $mementos[$visiting->getIDPath()];
    	$visiting->setAllowEvents(false);
    	if ($memento) {
    	    $visiting->setMemento($memento);
    	}

    	if ($visiting->hasChildNodes()) {
    	    foreach ($visiting->_childNodes as $child) {
        		$this->_restoreMementos($child, $mementos);
    	    }
    	}

    	$visiting->setAllowEvents(true);
    }
}

?>