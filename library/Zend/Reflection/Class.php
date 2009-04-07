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
 * @see Zend_Reflection_Property
 */
require_once 'Zend/Reflection/Property.php';

/**
 * @see Zend_Reflection_Method
 */
require_once 'Zend/Reflection/Method.php';

/**
 * Zend_Reflection_Docblock
 */
require_once 'Zend/Reflection/Docblock.php';

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Reflection_Class extends ReflectionClass
{

    /**
     * getDeclaringFile() - Return the reflection file of the declaring file.
     *
     * @return Zend_Reflection_File
     */
    public function getDeclaringFile()
    {
        return new Zend_Reflection_File($this->getFileName());
    }
    
    /**
     * getDocblock() - Return the classes Docblock reflection object
     *
     * @return Zend_Reflection_Docblock
     */
    public function getDocblock()
    {
        if (($comment = $this->getDocComment()) != '') {
            return new Zend_Reflection_Docblock($this);
        }
        
        throw new Zend_Reflection_Exception($this->getName() . ' does not have a Docblock.');
        
    }
    
    /**
     * getStartLine() - Return the start line
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
     * getContents() - Return the contents of the class
     *
     * @param bool $includeDocblock
     * @return string
     */
    public function getContents($includeDocblock = true)
    {
        $filename  = $this->getFileName();
        $filelines = file($filename);
        $startnum  = $this->getStartLine($includeDocblock);
        $endnum    = $this->getEndLine() - $this->getStartLine();
        
        return implode('', array_splice($filelines, $startnum, $endnum, true));
    }
    
    /**
     * getInterfaces()
     *
     * @return array Array of Zend_Reflection_Class
     */
    public function getInterfaces()
    {
        $phpReflections = parent::getInterfaces();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }
    
    /**
     * getMethod() Return method reflection by name
     *
     * @param string $name
     * @return Zend_Reflection_Method
     */
    public function getMethod($name)
    {
        $phpReflection = parent::getMethod($name);
        $zendReflection = new Zend_Reflection_Method($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }

    /**
     * getMethods()
     *
     * @param string $filter
     * @return array Array of Zend_Reflection_Method
     */
    public function getMethods($filter = -1)
    {
        $phpReflections = parent::getMethods($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Method($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

    /**
     * getParentClass() - Parent reflection class of reflected class
     *
     * @return Zend_Reflection_Class
     */
    public function getParentClass()
    {
        $phpReflection = parent::getParentClass();
        if ($phpReflection) {
            $zendReflection = new Zend_Reflection_Class($phpReflection->getName());
            unset($phpReflection);
            return $zendReflection;
        } else {
            return false;
        }
    }

    /**
     * getProperty() - Return reflection property of this class by name
     *
     * @param string $name
     * @return Zend_Reflection_Property
     */
    public function getProperty($name)
    {
        $phpReflection = parent::getProperty($name);
        $zendReflection = new Zend_Reflection_Property($this->getName(), $phpReflection->getName());
        unset($phpReflection);
        return $zendReflection;
    }
    
    /**
     * getProperties() - Return reflection properties of this class
     *
     * @param int $filter
     * @return array Array of Zend_Reflection_Property
     */
    public function getProperties($filter = -1)
    {
        $phpReflections = parent::getProperties($filter);
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $zendReflections[] = new Zend_Reflection_Property($this->getName(), $phpReflection->getName());
            unset($phpReflection);
        }
        unset($phpReflections);
        return $zendReflections;
    }

}
