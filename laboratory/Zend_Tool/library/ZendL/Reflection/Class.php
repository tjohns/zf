<?php

require_once 'ZendL/Reflection/Property.php';
require_once 'ZendL/Reflection/Method.php';
require_once 'ZendL/Reflection/Docblock.php';

class ZendL_Reflection_Class extends ReflectionClass
{

    /**
     * Return the reflection file of the declaring file.
     *
     * @return ZendL_Reflection_File
     */
    public function getDeclaringFile()
    {
        return new ZendL_Reflection_File($this->getFileName());
    }
    
    
    /**
     * Return the classes Docblock reflection object
     *
     * @return ZendL_Reflection_Docblock
     */
    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new ZendL_Reflection_Docblock($this);
        }
        
        throw new ZendL_Reflection_Exception($this->getName() . ' does not have a Docblock.');
        
    }
    
    /**
     * Return the start line
     *
     * @param bool $includeDocComment
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment) {
            if ($this->getDocComment() != '') {
                return $this->getDocblock()->getStartLine();
            }
        }
        
        return parent::getStartLine();
    }
    
    /**
     * Return the contents of the class
     *
     * @param bool $includeDocblock
     * @return string
     */
    public function getContents($includeDocblock = true)
    {
        return implode("\n", 
            array_splice(
                file($this->getFileName()), 
                $this->getStartLine($includeDocblock), 
                ($this->getEndLine() - $this->getStartLine()), 
                true
                )
            );
    }
    
    /**
     * Enter description here...
     *
     * @return ZendL_Reflection_Class[]
     */
    public function getInterfaces()
    {
        $phpReflections = parent::getInterfaces();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    /**
     * Return method reflection by name
     *
     * @param string $name
     * @return ZendL_Reflection_Method
     */
    public function getMethod($name)
    {
        $phpReflection = parent::getMethod($name);
        $zendReflection = new ZendL_Reflection_Method($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * Enter description here...
     *
     * @param string $filter
     * @return ZendL_Reflection_Method[]
     */
    public function getMethods($filter = -1)
    {
        $phpReflections = parent::getMethods($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Method($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    /**
     * Parent reflection class of reflected class
     *
     * @return ZendL_Reflection_Class
     */
    public function getParentClass()
    {
        $phpReflection = parent::getParentClass();
        if ($phpReflection) {
            $zendReflection = new ZendL_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
            return $zendReflection;
        } else {
            return false;
        }
    }

    /**
     * Return reflection property of this class by name
     *
     * @param string $name
     * @return ZendL_Reflection_Property
     */
    public function getProperty($name)
    {
        $phpReflection = parent::getProperty($name);
        $zendReflection = new ZendL_Reflection_Property($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    /**
     * Return reflection properties of this class
     *
     * @param int $filter
     * @return ZendL_Reflection_Property[]
     */
    public function getProperties($filter = -1)
    {
        $phpReflections = parent::getProperties($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Property($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    
    
    
}
