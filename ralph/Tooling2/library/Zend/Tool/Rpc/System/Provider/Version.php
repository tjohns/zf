<?php

require_once 'Zend/Version.php';

/**
 * Version Provider
 *
 */
class Zend_Tool_Rpc_System_Provider_Version implements Zend_Tool_Rpc_Provider_Interface
{

    const MODE_MAJOR = 'major';
    const MODE_MINOR = 'minor';
    const MODE_MINI  = 'mini';
    
    protected $_specialties = array('MajorPart', 'MinorPart', 'MiniPart');
    
    /**
     * Show Action
     *
     * @param string $mode The mode switch can be one of: major, minor, or mini (default)
     * @param bool $nameIncluded
     */
    public function show($mode = self::MODE_MINI, $nameincluded = true)
    {
        $versionInfo = $this->_splitVersion();
        
        switch($mode) {
            case self::MODE_MINOR:
                unset($versionInfo['mini']);
                break;
            case self::MODE_MAJOR:
                unset($versionInfo['mini'], $versionInfo['minor']);
                break;
        }
        
        $output = implode('.', $versionInfo);
        
        if ($nameIncluded) {
            $output = 'Zend Framework Version: ' . $output;
        }
        
        echo $output;
    }
    
    public function displayAction()
    {
        $this->show();
    }

    public function showMajorPart($nameincluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        echo (($nameincluded == true) ? 'ZF Major Version: ' : null) . $versionNumbers['major'];
    }
    
    public function displayMajorPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        echo (($nameIncluded == true) ? 'ZF Major Version: ' : null) . $versionNumbers['major'];
    }
    
    public function showMinorPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        echo (($nameIncluded == true) ? 'ZF Minor Version: ' : null) . $versionNumbers['minor'];
    }
    
    public function showMiniPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        echo (($nameIncluded == true) ? 'ZF Mini Version: ' : null)  . $versionNumbers['mini'];
    }
    
    protected function _splitVersion()
    {
        list($major, $minor, $mini) = explode('.', Zend_Version::VERSION);
        return array('major' => $major, 'minor' => $minor, 'mini' => $mini);
    }
    
}
