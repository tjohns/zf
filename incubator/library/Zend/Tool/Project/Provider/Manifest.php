<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Tool/Project/Provider/Profile.php';
require_once 'Zend/Tool/Project/Provider/Project.php';

class Zend_Tool_Project_Provider_Manifest implements Zend_Tool_Framework_Manifest_Interface
{
    public function getProviders()
    {
        return array(
            new Zend_Tool_Project_Provider_Profile(),
            new Zend_Tool_Project_Provider_Project()
        );
    }
}