<?php
class Zend_Reflection_Factory {
    function createClass($name) {
        require_once('Zend/Reflection/Class.php');
        return new Zend_Reflection_Class($name, $this);
    }
    
    function createDocblock($commentOrReflector) {
        require_once('Zend/Reflection/Docblock.php');
        return new Zend_Reflection_Docblock($commentOrReflector, $this);
    }
    
    function createExtension($name) {  
        require_once('Zend/Reflection/Extension.php');
        return new Zend_Reflection_Extension($name, $this);
    }
    
    function createFile($file) {
        require_once('Zend/Reflection/File.php');
        return new Zend_Reflection_File($file, $this);
    }
    
    function createFunction($name) {
        require_once('Zend/Reflection/Function.php');
        return new Zend_Reflection_Function($name, $this);
    }
    
    function createMethod($class, $name) {
        require_once('Zend/Reflection/Method.php');
        return new Zend_Reflection_Method($class, $name, $this);
    }
    
    function createParameter($function, $parameter) {
        require_once('Zend/Reflection/Parameter.php');
        return new Zend_Reflection_Parameter($function, $parameter, $this);  
    }
    
    function createProperty($class, $name) {
        require_once('Zend/Reflection/Property.php');
        return new Zend_Reflection_Property($class, $name, $this);
    }
}
?>