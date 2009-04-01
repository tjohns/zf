<?php
class Zend_Reflection_Factory {
    function createClass($name) {
        require_once('Zend/Reflection/Class.php');
        return new Zend_Reflection_Class($name);
    }
    
    function createDocBlock() {
    }
    
    function createException() {
    }
    
    function createExtension() {  
    }
    
    function createFile() {  
    }
    
    function createFunction() { 
    }
    
    function createMethod() {
    }
    
    function createParameter() {   
    }
    
    function createProperty() {
    }
}
?>