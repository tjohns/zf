<?php

class Zend_Entity_Event_Listener extends Zend_Entity_Event_EventAbstract
{
    /**
     * @var array
     */
    protected $_events = array();

    /**
     * @var array
     */
    protected $_callbacks = array(
        'preInsert' => array(),
        'postInsert' => array(),
        'preUpdate' => array(),
        'postUpdate' => array(),
        'preDelete' => array(),
        'postDelete' => array(),
        'postLoad' => array(),
    );

    /**
     * @param Zend_Entity_Event $event
     * @return boolean
     */
    public function registerEvent(Zend_Entity_Event_EventAbstract $event)
    {
        $this->_events[] = $event;
    }

    /**
     *
     * @param string $eventName
     * @param callable $callback
     * @return boolean
     */
    public function registerCallback($eventName, $callback)
    {
        if(!is_callable($callback)) {
            throw new Zend_Entity_Exception("Invalid callback given to EventListener.");
        }
        $this->_callbacks[$eventName][] = $callback;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function preInsert($entity)
    {
        $blockAction = false;
        
        foreach($this->_events AS $event) {
            if($event->preInsert($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('preInsert', $entity) || $blockAction;

        return !$blockAction;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function postInsert($entity)
    {
        $blockAction = false;

        foreach($this->_events AS $event) {
            if($event->postInsert($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('postInsert', $entity) || $blockAction;
        return !$blockAction;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function preUpdate($entity)
    {
        $blockAction = false;

        foreach($this->_events AS $event) {
            if($event->preUpdate($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('preUpdate', $entity) || $blockAction;
        return !$blockAction;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function postUpdate($entity)
    {
        $blockAction = false;

        foreach($this->_events AS $event) {
            if($event->postUpdate($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('postUpdate', $entity) || $blockAction;
        return !$blockAction;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function preDelete($entity)
    {
        $blockAction = false;

        foreach($this->_events AS $event) {
            if($event->preDelete($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('preDelete', $entity) || $blockAction;
        return !$blockAction;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function postDelete($entity)
    {
        $blockAction = false;

        foreach($this->_events AS $event) {
            if($event->postDelete($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('postDelete', $entity) || $blockAction;
        return !$blockAction;
    }

    /**
     * @param Zend_Entity_Interface $entity
     * @return boolean
     */
    public function postLoad($entity)
    {
        $blockAction = false;

        foreach($this->_events AS $event) {
            if($event->postLoad($entity) === false) {
                $blockAction = true;
            }
        }
        $blockAction = $this->_executeCallbacks('postLoad', $entity) || $blockAction;
        return !$blockAction;
    }

    /**
     * @param  string $eventType
     * @param  Zend_Entity_Interface $entity
     * @return boolean
     */
    protected function _executeCallbacks($eventType, $entity)
    {
        $blockAction = false;
        foreach($this->_callbacks[$eventType] AS $callback) {
            if(call_user_func_array($callback, array($entity)) === false) {
                $blockAction = true;
            }
        }
        return $blockAction;
    }
}