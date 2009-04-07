<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Reflection_Parameter
 */
require_once 'Zend/Reflection/Parameter.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Reflection_Method extends ReflectionMethod
{

    /**
     * getDocblock()
     *
     * @throws Zend_Reflection_Exception
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock()
    {
        if ($this->getDocComment() != '') {
            return new Zend_Reflection_Docblock($this);
        }
        
        throw new Zend_Reflection_Exception($this->getName() . ' does not have a Docblock.');
        
    }
    
    /**
     * getStartLine()
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
     * getDeclaringClass()
     *
     * @return Zend_Reflection_Class
     */
    public function getDeclaringClass()
    {
        $phpReflection = parent::getDeclaringClass();
        $zendReflection = new Zend_Reflection_Class($phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    /**
     * getParameters()
     *
     * @return Zend_Reflection_Parameter
     */
    public function getParameters()
    {
        $phpReflections = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Parameter(array($this->getDeclaringClass()->getName(), $this->getName()), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    /**
     * getContents()
     *
     * @param bool $includeDocblock
     * @return string
     */
    public function getContents($includeDocblock = true)
    {
        $fileContents = file($this->getFileName());
        $startNum = $this->getStartLine($includeDocblock);
        $endNum = ($this->getEndLine() - $this->getStartLine());
        
        return implode("\n", array_splice($fileContents, $startNum, $endNum, true));
    }
    
    /**
     * getBody()
     *
     * @return string
     */
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

