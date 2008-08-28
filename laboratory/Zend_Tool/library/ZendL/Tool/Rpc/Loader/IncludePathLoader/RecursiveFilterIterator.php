<?php

class ZendL_Tool_Rpc_Loader_IncludePathLoader_RecursiveFilterIterator extends RecursiveFilterIterator
{
    
    protected static $_denyDirectoryPattern = null;
    protected static $_acceptFilePattern = null;
    
    public static function setDenyDirectoryPattern($denyDirectoryPattern)
    {
        self::$_denyDirectoryPattern = $denyDirectoryPattern;
    }
    
    public static function setAcceptFilePattern($acceptFilePattern)
    {
        self::$_acceptFilePattern = $acceptFilePattern;
    }
    
    public function accept()
    {
        $currentNode = $this->current();
        
        if ($currentNode->isDir() && !preg_match('#' . self::$_denyDirectoryPattern. '#', $currentNode)) {
            return true;
        }

        return (preg_match('#' . self::$_acceptFilePattern  . '#', $currentNode)) ? true : false;
    }
    
}

