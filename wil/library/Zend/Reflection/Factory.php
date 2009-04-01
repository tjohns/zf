<?php
class Factory {
    function createClass($name) {
        require('Zend_Reflection_Class.php');
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