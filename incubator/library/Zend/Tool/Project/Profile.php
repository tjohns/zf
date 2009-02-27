<?php

require_once 'Zend/Tool/Project/Profile/FileParser/Xml.php';
require_once 'Zend/Tool/Project/Profile/Resource/Container.php';

/**
 * This class is the front most class for utilizing Zend_Tool_Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 */
class Zend_Tool_Project_Profile extends Zend_Tool_Project_Profile_Resource_Container
{

    /**
     * @var bool
     */
    protected static $_traverseEnabled = false;

    /**
     * @var array
     */
    protected $_attributes = array();

    /**
     * Constructor, standard usage would allow the setting of options
     *
     * @param array $options
     * @return bool
     */
    public function __construct($options = null)
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->_topResources = new Zend_Tool_Project_Profile_Resource_Container();
    }

    /**
     * Process options and either set a profile property or
     * set a profile 'attribute'
     *
     * @param array $options
     */
    public function setOptions(Array $options)
    {
        $this->setAttributes($options);
    }

    public function getIterator()
    {
        require_once 'Zend/Tool/Project/Profile/Iterator/EnabledResource.php';

        return new RecursiveIteratorIterator(
            new Zend_Tool_Project_Profile_Iterator_EnabledResource($this),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }

    public function loadFromData()
    {
        if (!isset($this->_attributes['profileData'])) {
            require_once 'Zend/Tool/Project/Exception.php';
            throw new Zend_Tool_Project_Exception('loadFromData() must have "profileData" set.');
        }

        $profileFileParser = new Zend_Tool_Project_Profile_FileParser_Xml();
        $profileFileParser->unserialize($this->_attributes['profileData'], $this);

        $this->rewind();
    }

    public function isLoadableFromFile()
    {
        if (!isset($this->_attributes['projectProfileFile']) && !isset($this->_attributes['projectDirectory'])) {
            return false;
        }

        if (isset($this->_attributes['projectProfileFile'])) {
            $projectProfileFilePath = $this->_attributes['projectProfileFile'];
            if (!file_exists($projectProfileFilePath)) {
                return false;
            }
        } else {
            $projectProfileFilePath = rtrim($this->_attributes['projectDirectory'], '/\\') . '/.zfproject.xml';
            if (!file_exists($projectProfileFilePath)) {
                return false;
            }
        }

        return true;
    }

    /**
     * load() attempts to load a project profile file from a variety of locations depending
     * on what information the user provided vie $options or attributes
     *
     */
    public function loadFromFile()
    {
        // if no data is supplied, need either a projectProfileFile or a projectDirectory
        if (!isset($this->_attributes['projectProfileFile']) && !isset($this->_attributes['projectDirectory'])) {
            require_once 'Zend/Tool/Project/Exception.php';
            throw new Zend_Tool_Project_Exception('loadFromFile() must have at least "projectProfileFile" or "projectDirectory" set.');
        }

        if (isset($this->_attributes['projectProfileFile'])) {
            $projectProfileFilePath = $this->_attributes['projectProfileFile'];
            if (!file_exists($projectProfileFilePath)) {
                require_once 'Zend/Tool/Project/Exception.php';
                throw new Zend_Tool_Project_Exception('"projectProfileFile" was supplied but file was not found at location ' . $projectProfileFilePath);
            }
        } else {
            $projectProfileFilePath = rtrim($this->_attributes['projectDirectory'], '/\\') . '/.zfproject.xml';
            if (!file_exists($projectProfileFilePath)) {
                require_once 'Zend/Tool/Project/Exception.php';
                throw new Zend_Tool_Project_Exception('"projectDirectory" was supplied but no profile file file was not found at location ' . $projectProfileFilePath);
            }
        }

        $profileData = file_get_contents($projectProfileFilePath);

        $profileFileParser = new Zend_Tool_Project_Profile_FileParser_Xml();
        $profileFileParser->unserialize($profileData, $this);

        $this->rewind();
    }

    public function storeToFile()
    {
        $file = null;

        if (isset($this->_attributes['projectProfileFile'])) {
            $file = $this->_attributes['projectProfileFile'];
        }

        if ($file == null) {
            require_once 'Zend/Tool/Project/Exception.php';
            throw new Zend_Tool_Project_Exception('storeToFile() must have a "projectProfileFile" attribute set.');
        }

        $parser = new Zend_Tool_Project_Profile_FileParser_Xml();
        $xml = $parser->serialize($this);
        file_put_contents($file, $xml);
    }

    public function storeToData()
    {
        $parser = new Zend_Tool_Project_Profile_FileParser_Xml();
        $xml = $parser->serialize($this);
        return $xml;
    }
    

    
    public function __toString()
    {
        $string = '';
        foreach ($this as $resource) {
            $string .= $resource->getName() . PHP_EOL;
            $rii = new RecursiveIteratorIterator($resource, RecursiveIteratorIterator::SELF_FIRST);
            foreach ($rii as $item) {
                $string .= str_repeat('  ', $rii->getDepth()+1) . $item->getName()
                        . ((count($attributes = $item->getAttributes()) > 0) ? ' [' . http_build_query($attributes) . ']' : '')
                        . PHP_EOL;
            }
        }
        return $string;
    }
}