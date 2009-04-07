<?php

require_once 'Zend/Tool/Framework/Manifest/Interface.php';
require_once 'Zend/Tool/Project/Provider/Profile.php';
require_once 'Zend/Tool/Project/Provider/Project.php';
require_once 'Zend/Tool/Project/Provider/Controller.php';
require_once 'Zend/Tool/Project/Provider/Action.php';
require_once 'Zend/Tool/Project/Provider/View.php';
require_once 'Zend/Tool/Project/Provider/DbAdapter.php';
require_once 'Zend/Tool/Project/Provider/ProjectProvider.php';
//require_once 'Zend/Tool/Project/Provider/DbTable.php';
//require_once 'Zend/Tool/Project/Provider/Model.php';
//require_once 'Zend/Tool/Project/Provider/Form.php';

class Zend_Tool_Project_Provider_Manifest implements Zend_Tool_Framework_Manifest_Interface
{
    public function getProviders()
    {
        return array(
            new Zend_Tool_Project_Provider_Profile(),
            new Zend_Tool_Project_Provider_Project(),
            new Zend_Tool_Project_Provider_Controller(),
            new Zend_Tool_Project_Provider_Action(),
            new Zend_Tool_Project_Provider_View(),
            new Zend_Tool_Project_Provider_DbAdapter(),
            new Zend_Tool_Project_Provider_ProjectProvider(),
            //new Zend_Tool_Project_Provider_DbTable(),
            //new Zend_Tool_Project_Provider_Model(),
            //new Zend_Tool_Project_Provider_Form(),
        );
    }
}