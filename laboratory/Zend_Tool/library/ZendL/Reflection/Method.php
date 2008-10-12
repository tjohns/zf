<?php

class ZendL_Reflection_Method extends ReflectionMethod
{

    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new ZendL_Reflection_Docblock($this);
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
    
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new ZendL_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    public function getParameters()
    {
        $phpReflections = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new ZendL_Reflection_Parameter(array($this->getDeclaringClass()->getName(), $this->getName()), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    public function getContents($includeDocblock = true)
    {
        $fileContents = file($this->getFileName());
        $startNum = $this->getStartLine($includeDocblock);
        $endNum = ($this->getEndLine() - $this->getStartLine());
        
        return implode("\n", array_splice($fileContents, $startNum, $endNum, true));
    }
    
    public function getBody()
    {
        $lines = array_slice(file($this->getDeclaringClass()->getFileName()), $this->getStartLine(), ($this->getEndLine() - $this->getStartLine()), true);
        
        $firstLine = array_shift($lines);

        if (trim($firstLine) !== '{') {
            array_unshift($lines, $firstLine);
        }
        
        $lastLine = array_pop($lines);
        
        if (trim($lastLine) !== '}') {
            array_push($lines, $lastLine);
        }

        // just in case we had code on the braket lines
        return rtrim(ltrim(implode("\n", $lines), '{'), '}');
    }
    
}

