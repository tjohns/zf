<?php



class Zend_Tool_Provider_ZfProject_Project extends Zend_Tool_Provider_ZfProject_ProviderAbstract
{

    
    /**
     * Create Action
     * 
     * 
     *
     * @param string $path shortName=c
     */
    public function create($path = null, $profileSetClass = 'Zend_Tool_Provider_ZfProject_ProfileSet_Default')
    {
        /**
         * @todo make sure a project doesnt alredy exist here
         */
        $projectProfile = $this->_getProjectProfile();

        if ($projectProfile) {
            throw new Exception('A project already exists here');
        }
        
        if ($path == null) {
            $path = $_SERVER['PWD'];
        }
        
        try {

            $projectProfile = new Zend_Tool_Provider_ZfProject_ProjectProfile();

            if ($projectProfile->projectDirectory) {
                $projectProfile->projectDirectory->setBaseDirectoryName($path);
            }
            
            $projectProfile->create();

        } catch (Exception $e) {
            die($e->getMessage());
        }
        
        $this->_response->setContent('creating project at ' . $path);
    }

    public function show()
    {
        $projectProfile = $this->_getProjectProfile();
        Zend_Debug::dump($projectProfile);
    }
    
}
