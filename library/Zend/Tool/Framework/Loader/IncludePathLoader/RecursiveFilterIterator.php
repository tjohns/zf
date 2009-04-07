<?php

class Zend_Tool_Framework_Loader_IncludePathLoader_RecursiveFilterIterator extends RecursiveFilterIterator
{

    protected $_denyDirectoryPattern = null;
    protected $_acceptFilePattern    = null;
    

    /*
    public function __construct(RecursiveIterator $iterator, $denyDirectoryPattern = null, $acceptFilePattern = null)
    {
        parent::__construct($iterator);
    }
    */
    

    public function accept()
    {
        $currentNode = $this->current();

        if ($currentNode->isDir() && !preg_match('#.*(/|\\\\).svn#', $currentNode)) {
            //echo ' accept ' . $currentNode . PHP_EOL;
            return true;
        }

        return (preg_match('#.*(?:Manifest|Provider)\.php$#', $currentNode)) ? true : false;
        
        /* This will not work
        if ($currentNode->isDir() && !preg_match('#' . $this->_denyDirectoryPattern . '#', $currentNode)) {
            //echo ' accept ' . $currentNode . PHP_EOL;
            return true;
        }

        return (preg_match('#' . $this->_acceptFilePattern . '#', $currentNode)) ? true : false;
         */
    }

}

