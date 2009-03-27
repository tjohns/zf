<?php

class Zend_Tool_Project_Context_Zf_ViewScriptFile extends Zend_Tool_Project_Context_Filesystem_File
{
    
    protected $_filesystemName = 'view.phtml';
    
    protected $_forActionName = null;
    protected $_scriptName = null;

    public function init()
    {
        if ($forActionName = $this->_resource->getAttribute('forActionName')) {
            $this->_forActionName = $forActionName;
            $this->_filesystemName = $forActionName . '.phtml';
        } elseif ($scriptName = $this->_resource->getAttribute('scriptName')) {
            $this->_scriptName = $scriptName;
            $this->_filesystemName = $scriptName . '.phtml';
        } else {
            throw new Exception('Either a forActionName or scriptName is required.');
        }
        
        parent::init();
    }
    
    
    public function getPersistentAttributes()
    {
        $attributes = array();
        
        if ($this->_forActionName) {
            $attributes['forActionName'] = $this->_forActionName;      
        }
        
        if ($this->_scriptName) {
            $attributes['scriptName'] = $this->_scriptName;
        }
        
        return $attributes;
    }
    
    public function getName()
    {
        return 'ViewScriptFile';
    }
    
    
    public function getContents()
    {
        $contents = '';
        
        if ($this->_filesystemName == 'error.phtml') {  // should also check that the above directory is forController=error
            $contents .= <<<EOS
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"; "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd> 
<html xmlns="http://www.w3.org/1999/xhtml"> 
<head>  
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
  <title>Zend Framework Default Application</title> 
</head> 
<body> 
  <h1>An error occurred</h1> 
  <h2><?= \$this->message ?></h2> 

  <? if ('development' == \$this->env): ?> 
  
  <h3>Exception information:</h3> 
  <p> 
      <b>Message:</b> <?= \$this->exception->getMessage() ?> 
  </p> 

  <h3>Stack trace:</h3> 
  <pre><?= \$this->exception->getTraceAsString() ?> 
  </pre> 

  <h3>Request Parameters:</h3> 
  <pre><? var_dump(\$this->request->getParams()) ?> 
  </pre> 
  <? endif ?>
  
</body> 
</html>
            
EOS;
        } elseif ($this->_forActionName == 'index' && $this->_resource->getParentResource()->getAttribute('forControllerName') == 'index') {
            
            $contents =<<<EOS
<style>
    
    a:link,
    a:visited
    {
        color: #0398CA;
    }

    span#zf-name
    {
        color: #91BE3F;
    }

    div#welcome
    {
        color: #FFFFFF;
        background-image: url(http://framework.zend.com/images/bkg_header.jpg);
        width:  600px;
        height: 400px;
        border: 2px solid #444444;
        overflow: hidden;
    }
    
    div#more-information
    {
        background-image: url(http://framework.zend.com/images/bkg_body-bottom.gif);
        height: 100%;
    }

</style>
<center>
    <div id="welcome">
        <center>
        <br />
        <h1>Welcome to the <span id="zf-name">Zend Framework!</span><h1 />
        <h3>This is your projects main page<h3 /><br /><br />
        <div id="more-information">
            <br />
            <img src="http://framework.zend.com/images/PoweredBy_ZF_4LightBG.png" /><br /><br />
            Helpful Links: <br />
            <A href="http://framework.zend.com/">Zend Framework Website</a> |
            <A href="http://framework.zend.com/manual/en/">Zend Framework Manual</a>
        </div>
    </div>
</center>
EOS;
            
        } else {
            $contents = '<br /><br /><center>View script for controller <b>' . $this->_resource->getParentResource()->getAttribute('forControllerName') . '</b>'
                . ' and script/action name <b>' . $this->_forActionName . '</b></center>';
        }
        return $contents;
    }
    
}