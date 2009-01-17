<?php

require_once 'Zend/Tool/Project/Profile.php';
require_once 'Zend/Tool/Framework/Provider/Interface.php';
require_once 'Zend/Tool/Project/Context/Registry.php';
require_once 'Zend/Tool/Project/Profile/FileParser/Xml.php';
require_once 'Zend/Tool/Framework/Client/Registry.php';

abstract class Zend_Tool_Project_Provider_Abstract implements Zend_Tool_Framework_Provider_Interface
{

    protected static $_isInitialized = false;

    /**
     * @var Zend_Tool_Project_Profile
     */
    protected $_loadedProfile = null;

    public static function initialize()
    {
        // return if we have already initialized the ZF contexts
        if (self::$_isInitialized) {
            return;
        }

        $contextRegistry = Zend_Tool_Project_Context_Registry::getInstance();
        $contextRegistry->addContextsFromDirectory(
            dirname(dirname(__FILE__)) . '/Context/Zf/', 'Zend_Tool_Project_Context_Zf_'
        );

        self::$_isInitialized = true;
    }

    final public function __construct()
    {
        // initialize always
        self::initialize();

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

    protected function _createNode($searchNodeToAppendTo, Zend_Tool_Project_Context_Interface $context)
    {
        if ($this->_loadedProfile == null) {
            require_once 'Zend/Tool/Project/Provider/Exception.php';

            throw new Zend_Tool_Project_Provider_Exception('You must call _loadExistingProfile first.');
        }

        $searchNode = $this->_loadedProfile->findNodeByContext($searchNodeToAppendTo);

        $newNode = new ZendL_Tool_Project_Resource($context);
        $searchNode->append($newNode);
    }

    protected function _getProfile()
    {
        if (!$this->_loadedProfile) {
            $this->_loadProfile();
        }

        return $this->_loadedProfile;
    }

    protected function _storeProfile()
    {
        $projectProfileFile = $this->_profile->findNodeByContext('ProjectProfileFile');

        $name = $projectProfileFile->getContext()->getPath();

        Zend_Tool_Framework_Client_Registry::getInstance()->response->appendContent(
            'Updating project profile \'' . $name . '\''
        );

        $projectProfileFile->create();
    }

    private function _loadContextClassesIntoRegistry($contextClasses)
    {
        $registry = Zend_Tool_Project_Context_Registry::getInstance();

        foreach ($contextClasses as $contextClass) {
            $registry->addContextClass($contextClass);
        }
    }
}