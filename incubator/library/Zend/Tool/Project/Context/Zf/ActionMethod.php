<?php


require_once 'Zend/Tool/Project/Context/Interface.php';

class Zend_Tool_Project_Context_Zf_ActionMethod implements Zend_Tool_Project_Context_Interface 
{
    /**
     * @var Zend_Tool_Project_Profile_Resource
     */
    protected $_resource = null;

    public function init()
    {
        $this->_resource->setAppendable(false);
        if (!$this->_resource->getParentResource()->getContext() instanceof Zend_Tool_Project_Context_Zf_ControllerFile) {
            throw new Zend_Tool_Project_Context_Exception('ActionMethod must be a sub resource of a ControllerFile');
        }
    }
    
    public function getName()
    {
        return 'Action';
    }
    
    public function setResource($resource)
    {
        $this->_resource = $resource;
    }
    
    public function create()
    {
        
    }
    
    public function delete()
    {
        
    }
    
}