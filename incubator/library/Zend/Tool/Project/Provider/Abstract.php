<?php

require_once 'Zend/Tool/Project/Profile.php';
require_once 'Zend/Tool/Framework/Provider/Abstract.php';
require_once 'Zend/Tool/Project/Context/Repository.php';
require_once 'Zend/Tool/Project/Profile/FileParser/Xml.php';
require_once 'Zend/Tool/Framework/Registry.php';

abstract class Zend_Tool_Project_Provider_Abstract extends Zend_Tool_Framework_Provider_Abstract
{

    protected static $_isInitialized = false;

    /**
     * @var Zend_Tool_Project_Profile
     */
    protected $_loadedProfile = null;

    final public function __construct()
    {
        // initialize the ZF Contexts (only once per php request)
        if (!self::$_isInitialized) {
            $contextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
            $contextRegistry->addContextsFromDirectory(
                dirname(dirname(__FILE__)) . '/Context/Zf/', 'Zend_Tool_Project_Context_Zf_'
            );
            self::$_isInitialized = true;
        }

        // load up the extending providers required context classes
        if ($contextClasses = $this->getContextClasses()) {
            $this->_loadContextClassesIntoRegistry($contextClasses);
        }

    }

    public function getContextClasses()
    {
        return array();
    }

    /**
     * _getProject is designed to find if there is project file in the context of where
     * the client has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh client registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     *
     * @return Zend_Tool_Project_Profile
     */
    protected function _loadProfile($projectDirectory = null)
    {

        if ($projectDirectory == null) {
            $projectDirectory = getcwd();
        }

        $profile = new Zend_Tool_Project_Profile();
        $profile->setAttribute('projectDirectory', $projectDirectory);

        if ($profile->isLoadableFromFile()) {
            $profile->loadFromFile();
            $this->_loadedProfile = $profile;
            return $profile;
        }

        return false;
    }

    /**
     * Enter description here...
     *
     * @param string|array|Zend_Tool_Project_Profile_Resource_SearchConstraints $appendNodeOrSearchConstraints
     * @param string|Zend_Tool_Project_Context_Interface $context
     * @param array
     * @return Zend_Tool_Project_Profile_Resource
     */
    /*
    protected function _createResource($appendResourceOrSearchConstraints, $context, Array $attributes = array())
    {
        if ($this->_loadedProfile == null) {
            require_once 'Zend/Tool/Project/Provider/Exception.php';
            throw new Zend_Tool_Project_Provider_Exception('You must call _loadExistingProfile first.');
        }
        
        if (!$appendResourceOrSearchConstraints instanceof Zend_Tool_Project_Profile_Resource_Container) {
            if (($parentResource = $this->_loadedProfile->search($appendResourceOrSearchConstraints)) == false) {
                require_once 'Zend/Tool/Project/Provider/Exception.php';
                throw new Zend_Tool_Project_Provider_Exception('No node was found to append to.');                
            }
        } else {
            $parentResource = $appendResourceOrSearchConstraints;
        }

        if (is_string($context)) {
            $contextRegistry = Zend_Tool_Project_Context_Repository::getInstance();
            if ($contextRegistry->hasContext($context)) {
                $context = $contextRegistry->getContext($context);
            } else {
                require_once 'Zend/Tool/Project/Provider/Exception.php';
                throw new Zend_Tool_Project_Provider_Exception('Context by name ' . $context . ' was not found in the context registry.');  
            }
        } elseif (!$context instanceof Zend_Tool_Project_Context_Interface) {
            require_once 'Zend/Tool/Project/Provider/Exception.php';
            throw new Zend_Tool_Project_Provider_Exception('Context must be of type string or Zend_Tool_Project_Context_Interface.');  
        }
        
        $newResource = new Zend_Tool_Project_Profile_Resource($context);
        
        if ($attributes) {
            $newResource->setAttributes($attributes);
        }
        
        $parentResource->append($newResource);
        $newResource->initializeContext();
        return $newResource;
    }
    */

    /**
     * Return the currently loaded profile
     *
     * @return Zend_Tool_Project_Profile
     */
    protected function _getProfile()
    {
        if (!$this->_loadedProfile) {
            $this->_loadProfile();
        }

        return $this->_loadedProfile;
    }

    protected function _storeProfile()
    {
        $projectProfileFile = $this->_loadedProfile->search('ProjectProfileFile');
        
        $name = $projectProfileFile->getContext()->getPath();

        $this->_getResponse()->appendContent('Updating project profile \'' . $name . '\'');

        $projectProfileFile->getContext()->save();
    }

    private function _loadContextClassesIntoRegistry($contextClasses)
    {
        $registry = Zend_Tool_Project_Context_Repository::getInstance();

        foreach ($contextClasses as $contextClass) {
            $registry->addContextClass($contextClass);
        }
    }
}