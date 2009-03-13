<?php

final class Zend_Tool_Framework_Client_Response
{
    protected $_callback = null;
    protected $_content = array();
    protected $_contentSeparator = null;
    protected $_exception = null;

    /**
     * Enter description here...
     *
     * @param callback $callback
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setContentCallback($callback)
    {
        $this->_callback = $callback;

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param string $contentSeparator
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setContentSeparator($contentSeparator)
    {
        $this->_contentSeparator = $contentSeparator;
        return $this;
    }
    
    /**
     * Enter description here...
     *
     * @return string
     */
    public function getContentSeparator()
    {
        return $this->_contentSeparator;
    }
    
    /**
     * Enter description here...
     *
     * @param string $content
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setContent($content)
    {
        $this->_content = array();

        $this->appendContent($content);

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param string $content
     * @return Zend_Tool_Framework_Client_Response
     */
    public function appendContent($content)
    {
        if ($this->_callback !== null) {
            call_user_func($this->_callback, $content);
        }

        $this->_content[] = $content;

        return $this;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getContent()
    {
        return implode($this->_contentSeparator, $this->_content);
    }

    /**
     * Enter description here...
     *
     * @return bool
     */
    public function isException()
    {
        return isset($this->_exception);
    }

    /**
     * Enter description here...
     *
     * @param Exception $exception
     * @return Zend_Tool_Framework_Client_Response
     */
    public function setException(Exception $exception)
    {
        $this->_exception = $exception;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Exception
     */
    public function getException()
    {
        return $this->_exception;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function __toString()
    {
        return (string) implode($this->_contentSeparator, $this->_content);
    }

}