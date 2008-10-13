<?php

class ZendL_Reflection_Function extends ReflectionFunction
{
    
    
    
    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new ZendL_Reflection_Docblock($comment);
        }
        
        throw new ZendL_Reflection_Exception($this->getName() . ' does not have a Docblock.');
    }
    
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment) {
            if ($this->getDocComment() != '') {
                return $this->getDocblock()->getStartLine();
            }
        }
        
        return parent::getStartLine();
    }
    
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
    
    public function getParameters()
    {
        $phpReflections = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Parameter($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

}