<?php

class Zend_Doctrine2_Tool_ProviderAbstract extends Zend_Tool_Project_Provider_Abstract
    implements Zend_Tool_Framework_Provider_Pretendable
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em = null;

    /**
     * @var Zend_Application
     */
    protected $_applicationInstance = null;

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function _getEntityManager()
    {
        if ($this->_em === null) {
            $this->_em = $this->_createDoctrineEntityManager();
        }
        return $this->_em;
    }

    /**
     * @return Zend_Application
     */
    protected function _getApplicationInstance()
    {
        if ($this->_applicationInstance === null) {
            $profile = $this->_loadProfileRequired();

            $searchParams = array('BootstrapFile');
            $bootstrapFile = $profile->search($searchParams);

            if ($bootstrapFile !== false && $bootstrapFile->getName() == "BootstrapFile") {
                $path = $bootstrapFile->getContext()->getPath();
                require_once($path);
            }

            // Code taken from Zf_BootstrapFile Context Resource
            $applicationConfigFile = $profile->search('ApplicationConfigFile');

            if ($applicationConfigFile === false) {
                throw new Zend_Doctrine2_Exception(
                    "Doctrine2 Provider requires an ApplicationConfigFile resource in your ".
                    "Zend Framework Project configuration."
                );
            }

            if ($applicationConfigFile->getContext()->exists() == false) {
                throw new Zend_Doctrine2_Exception(
                    "Doctrine2 Provider requires an existing ApplicationConfigFile ".
                    "resource in your Zend Framework Project configuration."
                );
            }

            $applicationOptions = array();
            $applicationOptions['config'] = $applicationConfigFile->getPath();

            $this->_applicationInstance = new Zend_Application(
                'development',
                $applicationOptions
            );
        }

        return $this->_applicationInstance;
    }

    protected function _createDoctrineEntityManager()
    {
        $app = $this->_getApplicationInstance();
        $app->bootstrap();

        $emf = $app->getBootstrap()->getContainer()->entitymanager;
        if ($emf instanceof \Doctrine\ORM\EntityManager) {
            $em = $emf;
        } else {
            $em = $emf->createEntityManager();
        }
        return $em;
    }

    protected function _getAllClassMetadata()
    {
        $em = $this->_getEntityManager();

        // TODO: replace with $cmf->preload(true) when DDC-97 and DDC-98 are applied.
        $driver = $em->getConfiguration()->getMetadataDriverImpl();
        $classNames = $driver->getAllClassNames();

        $classes = array();
        $cmf = $em->getMetadataFactory();
        foreach ($classNames AS $className) {
            if (!class_exists($className, true)) {
                throw new Zend_Doctrine2_Exception("Can't load $className into scope from Zend_Tool. ".
                    "Have you configured your Autoloader and Resource Loaders in your Zend_Application Bootstrap?");
            }
            $classes[] = $cmf->getMetadataFor($className);
        }

        if (count($classes) == 0) {
            throw new Zend_Doctrine2_Exception("No processable classes were found.");
        }

        return $classes;
    }

    protected function _getAskForConfirmationConfig()
    {
        if (isset($this->_registry->getConfig()->doctrine2->askForConfirmation)) {
            return (bool)$this->_registry->getConfig()->doctrine2->askForConfirmation;
        }
        return false;
    }

    protected function _getAskForConfirmationCriticalTaskConfig()
    {
        if (isset($this->_registry->getConfig()->doctrine2->askForConfirmationCriticalTask)) {
            return (bool)$this->_registry->getConfig()->doctrine2->askForConfirmationCriticalTask;
        }
        return true;
    }

    public function getContextClasses()
    {
        return array(
            'Zend_Doctrine2_Tool_Project_Context_MetadataDirectory',
            'Zend_Doctrine2_Tool_Project_Context_ProxyDirectory',
        );
    }
}