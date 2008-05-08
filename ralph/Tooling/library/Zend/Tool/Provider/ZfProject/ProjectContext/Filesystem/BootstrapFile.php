<?php

class Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_BootstrapFile extends Zend_Tool_Provider_ZfProject_ProjectContext_Filesystem_File
{

    protected $_name = 'bootstrap.php';

    public function getContextName()
    {
        return 'bootstrapFile';
    }

    public function append(Zend_Tool_Provider_ZfProject_ProjectContext_ProjectContextAbstract $node)
    {
        throw new Exception('cannot append to a file');
    }

    public function getContents()
    {

        $profileSet = $this->getProfileSet();

        $output = $profileSet->bootstrapFile();
        return $output;
    }

}
