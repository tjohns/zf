<?php

class Zend_Tool_Provider_ZfProject_Project extends Zend_Tool_Provider_ZfProject_Abstract {}
/*
<?php

class Zend_Tool_Provider_ZfProject_Project extends Zend_Tool_Provider_Abstract
{
    
    protected $_name = 'project';
    
    public function getDefault()
    {
        $default = <<<EOS
<projectProfile>
    <directory context="application">
        <directory context="controllers">
            <file context="controller" name="IndexController.php" /> <!-- could also be controllerName="index" -->
            <file context="controller" name="ErrorController.php" />
        </directory>
        <directory context="models">
        </directory>
        <directory context="views">
            <directory context="viewScripts" name="scripts">
                <directory name="index">
                    <file context="viewScript" name="index.phtml" />
                </directory>
            </directory>
            <directory context="viewHelpers" name="helpers">
            </directory>
        </directory>
        <file context="bootstrap" />
    </directory>
    <directory context="library">
        <directory context="ZendFrameworkStandardLibrary" />
    </directory>
    <directory context="public">
        <file context="publicIndex" />
        <file context="htaccess" />
    </directory>
    
</projectProfile>
EOS;
    }
    
    
    
}
*/