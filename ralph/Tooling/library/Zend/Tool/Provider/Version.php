<?php

require_once 'Zend/Version.php';

/**
 * Version Provider
 *
 */
class Zend_Tool_Provider_Version extends Zend_Tool_Provider_Abstract
{

    const MODE_MAJOR = 'major';
    const MODE_MINOR = 'minor';
    const MODE_MINI  = 'mini';
    
    protected $_specialties = array('majorPart', 'minorPart', 'miniPart');
    
    /**
     * Show Action
     *
     * @param string $mode The mode switch can be one of: major, minor, or mini (default)
     * @param bool $nameIncluded
     */
    public function show($mode = self::MODE_MINI, $nameIncluded = true)
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
        
        $this->_response->setContent($output);
    }
    
    public function showMajorPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        $this->_response->setContent( (($nameIncluded == true) ? 'ZF Major Version: ' : null) . $versionNumbers['major']);
    }
    
    public function showMinorPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        $this->_response->setContent( (($nameIncluded == true) ? 'ZF Minor Version: ' : null) . $versionNumbers['minor']);
    }
    
    /**
     * Enter description here...
     *
     * @param bool $nameIncluded shortName=x longName=xinclude
     */
    public function showMiniPart($nameIncluded = true)
    {
        $versionNumbers = $this->_splitVersion();
        $this->_response->setContent( (($nameIncluded == true) ? 'ZF Mini Version: ' : null)  . $versionNumbers['mini']);
    }
    
    protected function _splitVersion()
    {
        list($major, $minor, $mini) = explode('.', Zend_Version::VERSION);
        return array('major' => $major, 'minor' => $minor, 'mini' => $mini);
    }
    
}
