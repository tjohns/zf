<?php

class Zend_Tool_Framework_Client_Response
{
    protected $_callback = null;
    protected $_content = array();
    protected $_exception = null;

    public function setContentCallback($callback)
    {
        $this->_callback = $callback;

        return $this;
    }

    public function setContent($content)
    {
        $this->_content = array();

        $this->appendContent($content);

        return $this;
    }

    public function appendContent($content)
    {
        if ($this->_callback !== null) {
            call_user_func($this->_callback, $content);
        }

        $this->_content[] = $content;

    	return $this;
    }

    public function getContent()
    {
        return $this->_content;
    }

    public function isException()
    {
        return isset($this->_exception);
    }

    public function setException(Exception $exception)
    {
        $this->_exception = $exception;
        return $this;
    }

    public function getException()
    {
        return $this->_exception;
    }

    public function __toString()
    {
        return (string) implode(PHP_EOL, $this->_content);
    }

}