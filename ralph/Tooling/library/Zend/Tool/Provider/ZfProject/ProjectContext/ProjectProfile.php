<?php

/**
 * @todo determine if this should be RecursiveDirectoryIterator
 */

class Zend_Tool_Provider_ZfProject_ProjectContext_ProjectProfile //implements RecursiveIterator 
{
/*    
    const SOURCE_XML        = 'SOURCE_XML';
    const SOURCE_INI        = 'SOURCE_INI';
    const SOURCE_PHP        = 'SOURCE_PHP';
   
    protected $_saveFormat  = self::SOURCE_XML;
*/
}

/*
        $profileIterator = new RecursiveIteratorIterator($projectFile->getProfile(), RecursiveIteratorIterator::SELF_FIRST);
        foreach ($profileIterator as $name => $item) {
            
            if ($skipToDepth !== null && $profileIterator->getDepth() > $skipToDepth) {
                continue;
            } elseif ($skipToDepth !== null && $profileIterator->getDepth() == $skipToDepth) {
                 $skipToDepth = null;
            }
            
            $currentDepth = $profileIterator->getDepth();
            
            if ($currentDepth <= $lastDepth) {
                array_pop($pathArray);
            }
            
            if ($currentDepth < $lastDepth) {
                for ($x = 0; $x < ($lastDepth - $currentDepth); $x++) {
                    array_pop($pathArray);
                }
            }
            
            $fullPath = $basePath;
            if ($pathArray) {
                $fullPath .= implode('/', $pathArray);
            }
            
            if ($item['enabled'] == 'false') {
                $skipToDepth = $profileIterator->getDepth();
                continue;
            }
            
            $resource = $this->_buildManifest->getContext('resource', $name);

            if ($resource === null) {
                throw new Zend_Build_Exception('Context not available.');
            }
            
            $className = $resource->getClassName();
            
            Zend_Loader::loadClass($className);
            $resourceObject = new $className();


            if (!$resourceObject instanceof Zend_Build_Resource_Filesystem) {
                throw new Zend_Build_Exception('Projects can only deal with file and directory resources.');
            }
            
            $resourceObject->setParameter('basePath', $fullPath);
            
            if (count($attrs = $item->attributes()) > 0) {
                
                foreach ($attrs as $attrName => $attrValue) {
                    if ($attrName != 'enabled') {
                        $resourceObject->setParameter($attrName, $attrValue);
                    }
                }
            }
            
            $resourceObject->validate();
            
            if (!isset($item->enabled) || (isset($item->enabled) && ($item->enabled != false))) {
                $resourceObject->execute($actionName);
            }
            
            $dirRemainder = preg_replace('#^' . preg_quote($fullPath, '#') . '#', '', $resourceObject->getDirname());

            
            if ($dirRemainder) {
                array_push($pathArray, trim($dirRemainder, '/'));
            }
            
            $lastDepth = $profileIterator->getDepth();
        }
    }

*/